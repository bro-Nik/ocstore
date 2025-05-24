/**
 * Модуль попапа телефона
 * @module CallbackPopup
 */

import { BasePopup } from './base.js';

const CONFIG = {
  selectors: {
    popupId: '#popup-phone',
  },
  endpoints: {
    content: 'index.php?route=revolution/revpopupphone',
    submit: 'index.php?route=revolution/revpopupphone/make_order_phone',
  },
  globalEvents: {
    'open-popup-call': 'show',
  },
};

class CallbackPopup extends BasePopup {
  constructor() {
    super(CONFIG);
  }
}

export const callbackPopup = new CallbackPopup();
