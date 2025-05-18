/**
 * Модуль попапа предзаказа
 * @module PredzakazPopup
 */

import { BasePopup } from './base.js';

const SELECTORS = {
  POPUP_ID: '#popup-predzakaz',
};

const ENDPOINTS = {
  CONTENT_DEFAULT: 'index.php?route=revolution/revpopuppredzakaz&revproduct_id=',
  CONTENT: 'index.php?route=revolution/revpopuppredzakaz&revproduct_id=',
  MAKE_ORDER: 'index.php?route=revolution/revpopuppredzakaz/make_order_notify',
};

const EVENTS = {
  'submit': 'handleCheckout',
};


class PredzakazPopup extends BasePopup {
  constructor() {
    super(SELECTORS, ENDPOINTS, {}, EVENTS);
  }

  bindEvents() {
    document.addEventListener('click', (e) => {
      // Обработка кликов
      const predzakazBtn = e.target.closest(`[data-action="predzakaz"]`);
      if (predzakazBtn) {
        e.preventDefault();
        const productId = predzakazBtn.dataset.productId || '';
        this.endpoints.CONTENT = `${this.endpoints.CONTENT_DEFAULT}${productId}`
        this.zakazType = predzakazBtn.getAttribute('title') || predzakazBtn.getAttribute('data-original-title') || '';
        this.show();
      }
    });
  }

  prepareBeforeShow() {
    this.dialog.querySelector(this.selectors.POPUP_HEADER).innerHTML = this.zakazType;
  }

}


export const predzakazPopup = new PredzakazPopup();
