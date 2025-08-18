/**
 * Модуль работы с отзывами
 * @module Review
 */
import { FeedbackBase } from './base';
import { initStars } from '../elements';

const CONFIG = {
  moduleName: 'review',
  endpoints: {
    load: 'index.php?route=product/product/getReviews&product_id=',
    write: 'index.php?route=product/product/writeReview&product_id=',
  },
  selectors: {
    container: '.reviews_container',
    content: '#reviews',
    pagination: '.pagination a',
    loadTrigger: "#load_reviews"
  },
};

class Review extends FeedbackBase {
  constructor() {
    super(CONFIG);
  }

  bindEvents() {
    initStars(this.form);
    super.bindEvents();
  }
}

export const review = new Review();
