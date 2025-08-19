/**
 * Модуль попапа поедпросмотра
 * @module QuickViewPopup
 */

import { BasePopup } from './base.js';
import { initProductSwipers } from '../swiper';
import Swiper from 'swiper/core';
import { Navigation, Thumbs } from 'swiper/modules';
import { review } from '../feedback/review';
import { answer } from '../feedback/answer';
import { pageViewCounter } from '../statistic';

const CONFIG = {
  selectors: {
    popupId: '#popup-quick-view',
    popupClass: '.big-dialog'
  },
  endpoints: {
    content: 'index.php?route=modal/quickview&revproduct_id=',
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
    review.init(this.content);
    answer.init(this.content);
    pageViewCounter(this.content);
  }
}


export const quickViewPopup = new QuickViewPopup();
