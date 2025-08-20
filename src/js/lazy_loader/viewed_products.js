/**
 * Модуль просмотренных товаров
 * @module CallbackPopup
 */

import { LazyLoaderBase } from './base.js';
import { initCarouselSwipers } from '../swiper';

const CONFIG = {
  selectors: {
    containerId: 'viewed-products',
  },
  endpoints: {
    content: 'index.php?route=revolution/viewed_products',
  },
};

class ViewedProducts extends LazyLoaderBase {
  constructor() {
    super(CONFIG);
  }

  afterLoad() {
    initCarouselSwipers(this.container);
  }
}

export const viewedProducts = new ViewedProducts();
