// /**
//  * Модуль быстрого заказа и просмотра корзины
//  * @module CartQuick
//  */

import { BaseCartPopup } from './cart-base.js';

const CONFIG = {
  endpoints: {
    content: 'index.php?route=modal/cartquick',
    submit: 'index.php?route=modal/cartquick/send',
    cartStatus: 'index.php?route=modal/cartquick/status',
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
