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
    content: 'index.php?route=modal/callback',
    submit: 'index.php?route=modal/callback/send',
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
