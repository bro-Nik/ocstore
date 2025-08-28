import { LazyLoaderBase } from './base.js';
import { initCarouselSwipers } from '../swiper';
import { markProducts } from '../core/products';

class ProductsSlider extends LazyLoaderBase {
  constructor(config = {}) {
    super(config);
  }

  afterLoad() {
    initCarouselSwipers(this.container);
    markProducts(this.container);
  }
}

export { ProductsSlider };
