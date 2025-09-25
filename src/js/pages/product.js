import { BaseModule } from '../core/base';
import { getCookie } from '../cookie';
import { priceFormat } from '../main';
import { cart } from '../core/cart';
import { initProductSwipers } from '../swiper';

const CONFIG = {
  globalEvents: {
    'update_prices_product': 'priceChange'
  }
};

export class Product extends BaseModule {

  constructor() {
    super(CONFIG);
  }

  init(container = document) {
    const counterEl = container.querySelector('#counter_data');
    if (!counterEl) return;
    const { type, id } = counterEl.dataset;
    if (type != 'product') return;

    initProductSwipers();
    this.productId = id;
    this.container = container;
    this.infoBox = container.querySelector('.product-card.main-product');

    this.bindEvents();
    requestIdleCallback((deadline) => {
      while (deadline.timeRemaining() > 0) {
        this.checkOptions();
        this.calculatePrice();
      }
    });
  }

  bindEvents() {
    document.addEventListener('optionChange', () => this.checkOptions());
    document.addEventListener('optionChange', () => this.checkOptions());

    this.container.addEventListener('click', (e) => {
      if (e.target.matches('[data-action="scroll-to-reviews"]')) {
        this.scrollToTab(e.target, '#tab-review')
      }
      if (e.target.matches('[data-action="scroll-to-attributes"]')) {
        this.scrollToTab(e.target, '#tab-specification')
      }
    });
    super.bindEvents();
  }

  checkOptions() {
    const cartProducts = getCookie('cart') || {};
    const product = cartProducts[this.productId];
    if (!product) return;

    const options = product.options || [];
    const optionElements = this.infoBox.querySelectorAll('[data-option-id]');

    optionElements.forEach(el => el.checked = options.includes(el.dataset.optionId));
  }

  priceChange(e, btn) {
    this.calculatePrice();

    const cartProducts = getCookie('cart') || {};
    if (!cartProducts[this.productId]) return;

    const selectedOptions = this.infoBox.querySelectorAll('input[type="checkbox"]:checked');
    const options = Array.from(selectedOptions).map(option => option.dataset.optionId);

    cart.addToCookieList(this.infoBox.dataset.productId, 1, options);
  }

  calculatePrice() {
    // Найти все выбранные чекбоксы внутри элемента
    const selectedOptions = this.infoBox.querySelectorAll('input[type="checkbox"]:checked');

    const { productPrice } = this.infoBox.dataset;
    let totalPrice = parseFloat(productPrice);
    selectedOptions.forEach(checkbox => totalPrice += parseFloat(checkbox.dataset.optionPrice));

    const priceBox = this.infoBox.querySelector('.price');
    if (priceBox) priceBox.innerHTML = priceFormat(totalPrice);
  }

  scrollToTab(btn, tabId) {
    const container = btn.closest('dialog') || document;
    container.querySelector(`[href="${tabId}"]`).click();
    // container.querySelector(tabId)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    container.querySelector(tabId)?.scrollIntoView({ block: 'center' });
  }
}


export const productPage = new Product();
