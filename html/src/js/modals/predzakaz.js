/**
 * Модуль попапа предзаказа
 * @module PredzakazPopup
 */

import { BasePopup } from './base.js';

const CONFIG = {
  selectors: {
    popupId: '#popup-predzakaz',
  },
  endpoints: {
    content: 'index.php?route=revolution/revpopuppredzakaz&revproduct_id=',
    submit: 'index.php?route=revolution/revpopuppredzakaz/make_order_notify',
  },
  globalEvents: {
    'predzakaz': 'show'
  },
};

class PredzakazPopup extends BasePopup {
  constructor() {
    super(CONFIG);
  }

  show(e, btn) {
    const productId = btn.dataset.productId || '';
    this.zakazType = btn.getAttribute('title') || btn.getAttribute('data-original-title') || '';
    const url = `${this.endpoints.content}${productId}`;
    super.show(url);
  };

  afterShow() {
    this.dialog.querySelector(this.selectors.popupHeader).innerHTML = this.zakazType;
    super.afterShow();
  }
}


export const predzakazPopup = new PredzakazPopup();
