// /**
//  * Модуль быстрого заказа и просмотра корзины
//  * @module CartQuick
//  */

import { BaseCartPopup } from './cart-base.js';
import { validateNumberInput, validatePhoneInput, validateEmailInput } from '../services/validations';

const ENDPOINTS = {
  CONTENT: 'index.php?route=revolution/revpopupcartquick',
  MAKE_ORDER: 'index.php?route=revolution/revpopupcartquick/make_order',
  CART_STATUS: 'index.php?route=revolution/revpopupcartquick/status_cart',
};

const  SELECTORS = {
  POPUP_ID: '#popup-cart-quick',
};


class CartQuickPopup extends BaseCartPopup {
  constructor() {
    super(SELECTORS, ENDPOINTS);
  }

  bindEvents() {
    // Валидация числовых полей
    document.addEventListener('input', (e) => {
      if (e.target.matches('input[type="text"][data-validate="number"]')) {
        validateNumberInput(e.target);
      }
    });
  }

}

export const cartQuickPopup = new CartQuickPopup();
