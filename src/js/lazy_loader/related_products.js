/**
 * Модуль просмотренных товаров
 * @module CallbackPopup
 */

import { LazyLoaderBase } from './base.js';
import { initCarouselSwipers } from '../swiper';

const CONFIG = {
  selectors: {
    containerId: 'related-products',
  },
  endpoints: {
    content: 'index.php?route=product/module/similar_products&revproduct_id=',
  },
};

class RelatedProducts extends LazyLoaderBase {
  constructor() {
    super(CONFIG);
  }

  contentUrl() {
    const infoEl = document.querySelector('#counter_data');
    if (!infoEl) return;

    const id = infoEl.dataset.id;
    console.log(id)
    return `${this.endpoints.content}${id}`;
  }

  afterLoad() {
    initCarouselSwipers(this.container);
  }
}

export const relatedProducts = new RelatedProducts();
