/**
 * Базовый класс для попапов корзины
 * @module BaseCartPopup
 */

import { BasePopup } from './base';

const BASE_CART_CONFIG = {
};

const BASE_CART_ENDPOINTS = {
    CART_INFO: 'index.php?route=common/cart/info'
    // CART_INFO: 'index.php?route=common/cart/info ul li'
};

const  BASE_CART_SELECTORS = {
  // Элементы корзины
  QUANTITY_INPUT: '.plus-minus',
  PRODUCT_ID_INPUT: 'input[name="product_id"]',
  PRODUCT_KEY_INPUT: 'input[name="product_key"]',
  QUANTITY_CONTROLS: {
    PLUS: '.btn-plus button',
    MINUS: '.btn-minus button',
    CONTAINER: '.number'
  },
  REMOVE_BUTTON: '.remove button',

  // Кнопки действий
  QUICK_ORDER: '.quickorder_b',
  CART_TOTALS: ['#cart-total', '#cart-total-popup',
                '#cart-total_mobi'],
  CART_ITEMS: '#cart > ul',

  CART_DROPDOWN: '#cart .dropdown-menu',
  ABSOLUTE_CART: '#top3.absolutpo',
  MOBILE_CART: '#top #cart_mobi',
  
  // Для формы заказа
  SUBMIT_BTN: '#popup-checkout-button',
};

const BASE_CART_EVENT_HANDLERS = {
  'quick-order': 'handleQuickOrder',
  'sabmit': 'handleCheckout',
  'open-cart': 'handleOpenCart',
};


class BaseCartPopup extends BasePopup {
  constructor(selectors, endpoints, config = {}, events = {}) {
    super({ ...BASE_CART_SELECTORS, ...selectors },
          { ...BASE_CART_ENDPOINTS, ...endpoints },
          { ...BASE_CART_CONFIG, ...config },
          { ...BASE_CART_EVENT_HANDLERS, ...events });
  }

  initPopupHandlers() {
    // Обработчики кнопок +/-
    this.dialog.querySelectorAll(this.selectors.QUANTITY_CONTROLS.PLUS).forEach(btn => {
      btn.addEventListener('click', this.handleQuantityChange.bind(this));
    });
    
    this.dialog.querySelectorAll(this.selectors.QUANTITY_CONTROLS.MINUS).forEach(btn => {
      btn.addEventListener('click', this.handleQuantityChange.bind(this));
    });

    // Обработчики для ручного ввода
    this.dialog.querySelectorAll(this.selectors.QUANTITY_INPUT).forEach(input => {
      input.addEventListener('change', this.handleManualQuantityChange.bind(this));
      input.addEventListener('keyup', this.handleManualQuantityChange.bind(this));
    });

    // Обработчики удаления
    document.querySelectorAll(this.selectors.REMOVE_BUTTON).forEach(btn => {
      btn.addEventListener('click', this.handleRemoveItem.bind(this));
    });
  }

  async handleQuantityChange(e) {
    const btn = e.currentTarget;
    const action = btn.closest(this.selectors.QUANTITY_CONTROLS.PLUS) ? 'increase' : 'decrease';
    const container = btn.closest(this.selectors.QUANTITY_CONTROLS.CONTAINER);
    const input = container.querySelector(this.selectors.QUANTITY_INPUT);
    const productKey = container.querySelector(this.selectors.PRODUCT_ID_INPUT).value;
    
    let quantity = parseInt(input.value);
    quantity = action === 'increase' ? quantity + 1 : quantity - 1;
    
    if (quantity < 1) quantity = 1;
    input.value = quantity;
    
    await this.updateCartItem(productKey, quantity);
  }

  async handleManualQuantityChange(e) {
    const input = e.currentTarget;
    input.value = input.value.replace(/[^\d]/g, '');
    const quantity = parseInt(input.value) || 1;
    const productKey = input.closest('tr, .mobile-products-cart > div')
                          .querySelector(this.selectors.PRODUCT_ID_INPUT).value;
    
    await this.updateCartItem(productKey, quantity);
  }

  async updateCartItem(productKey, quantity) {
    const content = this.dialog.querySelector(this.selectors.POPUP_ID);
    this.masked(content, true);

    try {
      const url = `${this.endpoints.CONTENT}&update=${productKey}&quantity=${quantity}`;
      const response = await fetch(url);
      const html = await response.text();
      
      // this.dialog.querySelector(this.selectors.DIALOG_CONTENT).innerHTML = html;
      content.innerHTML = html;
      this.initPopupHandlers();
      await this.updateCartStatus();
      
    } finally {
      this.masked(content, false);
    }
  }

  async updateCartStatus() {
    if (!this.selectors.CART_STATUS) return;
    try {
      const response = await fetch(this.endpoints.CART_STATUS);
      const json = await response.json();
      
      if (json.total) {
        // Обновляем все элементы с общей суммой
        this.selectors.CART_TOTALS.forEach(selector => {
          const elements = document.querySelectorAll(selector);
          elements.forEach(el => el.textContent = json.total);
        });
        
        // Обновляем список товаров в корзине
        const cartItems = document.querySelector(this.selectors.CART_ITEMS);
        if (cartItems) {
          const cartResponse = await fetch(this.endpoints.CART_INFO);
          const html = await cartResponse.text();
          cartItems.innerHTML = html;
        }
      }
    } catch (error) {
      console.error('Ошибка при обновлении статуса корзины:', error);
    }

  }

  async handleRemoveItem(e) {
    const btn = e.currentTarget;
    const productKey = btn.nextElementSibling.value;
    
    await this.updateCartItem(productKey, 0);
  }
}

export { BaseCartPopup };
