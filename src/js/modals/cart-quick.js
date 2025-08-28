// /**
//  * Модуль быстрого заказа и просмотра корзины
//  * @module CartQuick
//  */

import { BaseCartPopup } from './cart-base.js';

const CONFIG = {
  endpoints: {
    content: 'index.php?route=modal/cartquick',
    submit: 'index.php?route=modal/cartquick/send',
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
}

export const cartQuickPopup = new CartQuickPopup();
