// /**
//  * Модуль быстрого заказа и просмотра корзины
//  * @module CartQuick
//  */

import { BaseCartPopup } from './cart-base.js';

const CONFIG = {
  endpoints: {
    content: 'index.php?route=revolution/revpopupcartquick',
    submit: 'index.php?route=revolution/revpopupcartquick/make_order',
    cartStatus: 'index.php?route=revolution/revpopupcartquick/status_cart',
  },
  selectors: {
    popupId: '#popup-cart-quick',
  },
  globalEvents: {
    'quick-order': 'show'
  },
};

class CartQuickPopup extends BaseCartPopup {
  constructor() {
    super(CONFIG);
  }

  async updateCartItem(productId, quantity) {
    const url = `${this.endpoints.content}&update=${productId}&quantity=${quantity}`;
    super.updateCartItem(url);
  }

}

export const cartQuickPopup = new CartQuickPopup();
