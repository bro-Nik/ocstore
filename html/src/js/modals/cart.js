/**
 * Модуль попапа корзины
 * @module CartPopup
 */

import { BaseCartPopup } from './cart-base.js';

const CONFIG = {
  selectors: {
    popupId: '#popup-cart',
  },
  endpoints: {
    content: 'index.php?route=revolution/revpopupcart',
    cartStatus: 'index.php?route=revolution/revpopupcart/status_cart',
  },
  globalEvents: {
    'open-popup-cart': 'show',
  },
};

class CartPopup extends BaseCartPopup {
  constructor() {
    super(CONFIG);
  }

  async updateCartItem(productId, quantity) {
    const url = `${this.endpoints.content}&update=${productId}&quantity=${quantity}`;
    super.updateCartItem(url);
  }
}

export const cartPopup = new CartPopup();
