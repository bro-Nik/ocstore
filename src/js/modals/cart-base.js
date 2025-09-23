/**
 * Базовый класс для попапов корзины
 * @module BaseCartPopup
 */

import { BasePopup } from './base';
import { cart } from '../core/cart';
import { wishlist } from '../core/wishlist';
import { events } from '../events/events';
import { eventManager } from '../events/event-manager';
import { getCookie, clearCookie } from '../cookie';
import { priceFormat } from '../main';

const BASE_CART_CONFIG = {
  endpoints: {
    cartInfo: 'index.php?route=common/cart/info'
  },
  selectors: {
    // Элементы корзины
    quantityInput: '.plus-minus',
    optionInput: '[data-action="update_prices_product"]',
    productIdInput: 'input[name="product_id"]',
    quantity: '.change-quantity button',
    quantityContainer: '.number',
    removeButton: 'button.remove',
    productItem: '.cart-product-item',
    productItemList: '.cart-product-items'
  },
};

class BaseCartPopup extends BasePopup {
  constructor(config = {}) {
    super({
      ...BASE_CART_CONFIG, ...config,
      selectors: { ...BASE_CART_CONFIG.selectors, ...config.selectors },
      modalEvents: { ...BASE_CART_CONFIG.modalEvents, ...config.modalEvents },
      globalEvents: { ...BASE_CART_CONFIG.globalEvents, ...config.globalEvents },
      endpoints: { ...BASE_CART_CONFIG.endpoints, ...config.endpoints }
    });
  }

  bindPopupEvents() {
    super.bindPopupEvents();
    // Обработчики кнопок +/-
    this.addEvent('click', this.selectors.quantity, this.quantityChange);

    // Обработчики для ручного ввода
    this.addEvent('change', this.selectors.quantityInput, this.quantityChangeManual);
    this.addEvent('input', this.selectors.quantityInput, this.quantityChangeManual);

    // Обработчики для опций
    this.addEvent('change', this.selectors.optionInput, this.optionChange);

    // Обработчики удаления
    this.addEvent('click', this.selectors.removeButton, this.removeItem);
  }

  optionChange(e, btn) {
    const productBox = btn.closest(this.selectors.productItem);
    this.updateProduct(productBox)

    // Генерируем кастомное событие
    document.dispatchEvent(new CustomEvent('optionChange'));
  }

  quantityChange(e, btn) {
    const productBox = btn.closest(this.selectors.productItem);
    const input = productBox.querySelector(this.selectors.quantityInput);
    const { productId, action } = btn.dataset;

    const quantity = parseInt(input.value) + (action === 'quantity+' ? 1 : -1);   
    this.updateQuantity(input, quantity, productId);
  }

  quantityChangeManual(e, input) {
    if (e.type === 'keyup' && !['ArrowUp', 'ArrowDown'].includes(e.key)) return;
    
    const productBox = input.closest(this.selectors.productItem);
    const { productId } = productBox.dataset;

    // Обработка пустого поля при уходе фокуса
    input.onblur = () => {
      if (!input.value.trim()) {
        input.value = 1;
        this.updateQuantity(input, 1, productId);
      }
    };

    if (!input.value) {
      this.updateProductTotal(productBox);
      this.updateTotalSum();
      return;
    }

    let quantity = parseInt(input.value) || 1;
    
    // Обрабатываем стрелки
    if (e.key === 'ArrowUp') quantity++;
    if (e.key === 'ArrowDown') quantity--;
    
    this.updateQuantity(input, quantity, productId);
  }

  updateQuantity(input, quantity, productId) {
    // Ограничиваем значение
    quantity = Math.max(1, Math.min(99, quantity));

    input.value = quantity;

    const productBox = input.closest(this.selectors.productItem);
    this.updateProduct(productBox);
  }

  getProductInfo(productBox) {
    // Найти все выбранные чекбоксы внутри элемента
    const selectedOptions = productBox.querySelectorAll('.options input[type="checkbox"]:checked');

    let productTotal = parseFloat(productBox.dataset.productPrice);
    let options = [];
    selectedOptions.forEach(checkbox => {
      productTotal += parseFloat(checkbox.dataset.optionPrice || 0);
      options.push(checkbox.dataset.optionId);
    });
    const quantity = parseInt(productBox.querySelector(this.selectors.quantityInput).value) || 0;

    return { productTotal, quantity, options };
  }

  updateProductTotal(productBox) {
    let { productTotal, quantity, options } = this.getProductInfo(productBox);
    productTotal *= quantity;
    productBox.dataset.total = productTotal;

    const priceElement = productBox.querySelector('.price');
    if (priceElement) priceElement.innerHTML = priceFormat(productTotal);
  }

  updateProduct(productBox) {
    this.updateProductTotal(productBox);
    this.updateTotalSum();

    const { productTotal, quantity, options } = this.getProductInfo(productBox);
    const { productId } = productBox.dataset;
    cart.addToCookieList(productId, quantity, options);
  }

  removeItem(e, btn) {
    const productBox = btn.closest(this.selectors.productItem);
    const { productId } = productBox.dataset;

    productBox.remove();
    this.updateTotalSum();

    const productsCount = cart.addToCookieList(productId, 0);
    cart.updateTotalCount(productsCount);
    cart.updateButtons(productId);
  }

  afterShow() {
    super.afterShow();
    this.checkOptions();
    this.updateTotalSum();

    const wishlistProducts = getCookie('wishlist');
    wishlist.markProducts(wishlistProducts, this.content);
  }

  checkOptions() {
    const productItemList = this.content.querySelector(this.selectors.productItemList);
    if (!productItemList) return;
    const productItems = productItemList.querySelectorAll(this.selectors.productItem);
    if (!productItems.length) return;

    const cartProducts = getCookie('cart');

    productItems.forEach(productBox => {
      const product = cartProducts[productBox.dataset.productId] || {};

      product.options?.forEach(optionId => {
        const optionElement = productBox.querySelector(`[data-option-id="${optionId}"]`);
        if (optionElement) optionElement.checked = true;
      });
    });
  }

  updateTotalSum() {
    const productItemList = this.content.querySelector(this.selectors.productItemList);
    if (!productItemList) {
      this.content.querySelector(this.selectors.popupCenter).innerHTML = 'В корзине пусто';
      this.removecheckoutBtn();
      return;
    }

    const productItems = productItemList.querySelectorAll(this.selectors.productItem);
    if (!productItems.length) {
      this.content.querySelector(this.selectors.popupCenter).innerHTML = 'В корзине пусто';
      this.removecheckoutBtn();
      return;
    }

    let sum = 0;
    productItems.forEach(productBox => {
      // Ставим общую цену и атрибут
      if (!productBox.dataset.total) this.updateProductTotal(productBox);
      sum += parseFloat(productBox.dataset.total);
    });

    const sumElement = this.content.querySelector('#all_total');
    if (sumElement) sumElement.innerHTML = priceFormat(sum);
  }

  handleSuccess(html) {
    clearCookie('cart');
    cart.updateTotalCount(0);
    cart.updateButtons();
    super.handleSuccess(html);
  }
}

export { BaseCartPopup };
