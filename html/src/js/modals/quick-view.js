/**
 * Модуль попапа поедпросмотра
 * @module QuickViewPopup
 */

import { BasePopup } from './base.js';
import { initProductSwipers } from '../swiper';
import Swiper from 'swiper/core';
import { Navigation, Thumbs } from 'swiper/modules';

const CONFIG = {
  selectors: {
    popupId: '#popup-quick-view',
    popupClass: '.big-dialog'
  },
  endpoints: {
    content: 'index.php?route=revolution/revpopupview&revproduct_id=',
  },
  globalEvents: {
    'quick-view': 'show'
  },
};

class QuickViewPopup extends BasePopup {
  constructor() {
    super(CONFIG);
  }

  show(e, btn) {
    const productId = btn.dataset.productId || '';
    const url = `${this.endpoints.content}${productId}`
    super.show(url);
  }

  async updateCartItem(productId, quantity) {
    const url = `${this.endpoints.content}&update=${productId}&quantity=${quantity}`;
    super.updateCartItem(url);
  }

  afterShow() {
    super.afterShow();
    // Показываем первую вкладку
    const firstTab = document.querySelector('.nav.nav-tabs li:first-child a');
    if (firstTab) {
      firstTab.click();
    }
    initProductSwipers();
  }
}


export const quickViewPopup = new QuickViewPopup();
