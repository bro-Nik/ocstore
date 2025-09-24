/**
 * Модуль попапа поедпросмотра
 * @module QuickViewPopup
 */

import { BasePopup } from './base.js';
import { initProductSwipers } from '../swiper';
import { review } from '../feedback/review';
import { answer } from '../feedback/answer';
import { pageViewCounter } from '../statistic';
import { productPage } from '../pages/product';
import { markProducts } from '../core/products';

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
    const productId = btn.closest('[data-product-id]')?.dataset.productId || 0;
    const url = `${this.endpoints.content}${productId}`
    super.show(url);
  }

  afterShow() {
    super.afterShow();
    // Показываем первую вкладку
    const firstTab = document.querySelector('.nav.nav-tabs li:first-child a');
    if (firstTab) {
      firstTab.click();
    }
    initProductSwipers(this.content);
    markProducts(this.content);
    review.init(this.content);
    answer.init(this.content);
    productPage.init(this.content);
    pageViewCounter(this.content);
  }
}

export const quickViewPopup = new QuickViewPopup();
