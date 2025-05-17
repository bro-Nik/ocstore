/**
 * Модуль попапа телефона
 * @module PhonePopup
 */

import { BasePopup } from './base.js';
import { events } from '../events/events';

const SELECTORS = {
  DROPDOWNS: ['.dropdown-menu.dop_contss'],
  POPUP_ID: '#popup-phone',
  PURCHASE_FORM: '#purchase-form',
};

const ENDPOINTS = {
  CONTENT: 'index.php?route=revolution/revpopupphone',
  MAKE_ORDER: 'index.php?route=revolution/revpopupphone/make_order_phone',
};


const GLOBAL_EVENTS = {
  'open-popup-call': 'show',
};

const EVENTS = {
  'submit': 'handleCheckout',
};


class PhonePopup extends BasePopup {
  constructor() {
    super(SELECTORS, ENDPOINTS, {}, EVENTS);
    events.addHandlers(GLOBAL_EVENTS, document, this);
  }

}

export const phonePopup = new PhonePopup();
