/**
 * Базовый класс для попапов корзины
 * @module BaseCartPopup
 */

import { BasePopup } from './base';
import { cart } from '../cart';
import { events } from '../events/events';
import { eventManager } from '../events/event-manager';

const BASE_CART_CONFIG = {
  endpoints: {
    cartInfo: 'index.php?route=common/cart/info'
  },
  selectors: {
    // Элементы корзины
    quantityInput: '.plus-minus',
    productIdInput: 'input[name="product_id"]',
    quantityPlus: '.btn-plus button',
    quantityMinus: '.btn-minus button',
    quantityContainer: '.number',
    removeButton: '.remove button',

    // Кнопки действий
    cartItems: '#cart > ul',
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
    this.addEvent('click', this.selectors.quantityPlus, this.quantityChange);
    this.addEvent('click', this.selectors.quantityMinus, this.quantityChange);

    // Обработчики для ручного ввода
    this.addEvent('change', this.selectors.quantityInput, this.quantityChangeManual);
    this.addEvent('keyup', this.selectors.quantityInput, this.quantityChangeManual);

    // Обработчики удаления
    this.addEvent('click', this.selectors.removeButton, this.removeItem);
  }

  async quantityChange(e, btn) {
    const action = btn.closest(this.selectors.quantityPlus) ? 'increase' : 'decrease';
    const container = btn.closest(this.selectors.quantityContainer);
    const input = container.querySelector(this.selectors.quantityInput);
    const productId = container.querySelector(this.selectors.productIdInput).value;
    
    let quantity = parseInt(input.value);
    quantity = action === 'increase' ? quantity + 1 : quantity - 1;
    
    if (quantity < 1) quantity = 1;
    input.value = quantity;
    
    await this.updateCartItem(productId, quantity);
  }

  async quantityChangeManual(e, input) {
    input.value = input.value.replace(/[^\d]/g, '');
    const quantity = parseInt(input.value) || 1;
    const productKey = input.closest('tr, .mobile-products-cart > div')
                          .querySelector(this.selectors.productIdInput).value;
    
    if (input.value) await this.updateCartItem(productKey, quantity);
  }

  async removeItem(e, btn) {
    const productId = btn.nextElementSibling.value;
    
    await this.updateCartItem(productId, 0);
    cart.updateButtons(productId);
  }

  async updateCartItem(url) {
    this.loading.show();
    try {
      await this.loadHtml(url, this.content);
      this.updateCartStatus();
    } finally {
      this.loading.hide();
    }
  }

  async updateCartStatus() {
    try {
      const json = await this.loadJson(this.endpoints.cartStatus);

      if (json.total) {
        cart.updateTotalCount(json.total);

        // Удаляем и изменяем кнопки
        if (json.total === '0') {
          this.removecheckoutBtn()
          cart.updateButtons();
        }
        
        // Обновляем список товаров в корзине
        const cartItems = document.querySelector(this.selectors.cartItems);
        await this.loadHtml(this.endpoints.cartInfo, cartItems);
      }
    } catch (error) {
      console.error('Ошибка при обновлении статуса корзины:', error);
    }
  }

  handleSuccess(output) {
    super.handleSuccess(output);
    this.updateCartStatus();
  }
}

export { BaseCartPopup };
