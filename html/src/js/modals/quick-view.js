/**
 * Модуль попапа поедпросмотра
 * @module QuickViewPopup
 */

import { BasePopup } from './base.js';
import { initProductSwipers } from '../swiper';
import Swiper from 'swiper/core';
import { Navigation, Thumbs } from 'swiper/modules';

const SELECTORS = {
  POPUP_ID: '#popup-quick-view',
  POPUP_CLASS: '.big-dialog'
};

const ENDPOINTS = {
  CONTENT_DEFAULT: 'index.php?route=revolution/revpopupview&revproduct_id=',
  CONTENT: '',
};

class QuickViewPopup extends BasePopup {
  constructor() {
    super(SELECTORS, ENDPOINTS);
  }

  bindEvents() {
    document.addEventListener('click', (e) => {
      // Обработка кликов
      const openBtn = e.target.closest(`[data-action="quick-view"]`);
      if (openBtn) {
        e.preventDefault();
        const productId = openBtn.dataset.productId || '';
        this.endpoints.CONTENT = `${this.endpoints.CONTENT_DEFAULT}${productId}`
        this.show();
      }
    });
  }

  prepareBeforeShow() {
    // Показываем первую вкладку
    const firstTab = document.querySelector('.nav.nav-tabs li:first-child a');
    console.log(firstTab)
    if (firstTab) {
      firstTab.click();
    }

    initProductSwipers();

  }

}


export const quickViewPopup = new QuickViewPopup();
