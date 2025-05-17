/**
 * Модуль попапа корзины
 * @module CartPopup
 */

import { BaseCartPopup } from './cart-base.js';
import { cartQuickPopup } from './cart-quick';
import { events } from '../events/events';

const SELECTORS = {
  POPUP_ID: '#popup-cart',
};

const ENDPOINTS = {
  CONTENT: 'index.php?route=revolution/revpopupcart',
  CART_STATUS: 'index.php?route=revolution/revpopupcart/status_cart',
};

const GLOBAL_EVENTS = {
  'open-popup-cart': 'show',
  'quick-order': 'handleQuickOrder'
};

class CartPopup extends BaseCartPopup {
  constructor() {
    super(SELECTORS, ENDPOINTS);
    events.addHandlers(GLOBAL_EVENTS, document, this);
  }

  async handleQuickOrder() {
    this.close();
    cartQuickPopup.show();
  }
}

export const cartPopup = new CartPopup();
