/**
 * Модуль попапа корзины
 * @module CartPopup
 */

import { BaseCartPopup } from './cart-base.js';
import { ToggleBoxManager } from '../services/animations';

const CONFIG = {
  selectors: {
    popupId: '#popup-cart',
  },
  endpoints: {
    content: 'index.php?route=modal/cart',
    submit: 'index.php?route=modal/cart/send',
  },
  globalEvents: {
    'open-popup-cart': 'show',
  },
};

class CartPopup extends BaseCartPopup {
  constructor() {
    super(CONFIG);
  }

  afterShow() {
    this.toggleBox = new ToggleBoxManager(this.content);
    super.afterShow();
  }
}

export const cartPopup = new CartPopup();
