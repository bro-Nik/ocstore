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
    content: 'index.php?route=modal/predzakaz&revproduct_id=',
    submit: 'index.php?route=modal/predzakaz/send',
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
    this.dialog.querySelector(this.selectors.submitBtn).innerHTML = this.zakazType;
    super.afterShow();
  }
}


export const predzakazPopup = new PredzakazPopup();
