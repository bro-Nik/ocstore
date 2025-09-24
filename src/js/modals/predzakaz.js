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
    const productId = btn.closest('[data-product-id]')?.dataset.productId || 0;
    const url = `${this.endpoints.content}${productId}`;
    super.show(url);
  };
}


export const predzakazPopup = new PredzakazPopup();
