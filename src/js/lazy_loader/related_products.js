/**
 * Модуль просмотренных товаров
 * @module CallbackPopup
 */

import { ProductsSlider } from './products_slider';

const CONFIG = {
  selectors: {
    containerId: 'related-products',
  },
  endpoints: {
    content: 'index.php?route=product/module/similar_products&revproduct_id=',
  },
};

class RelatedProducts extends ProductsSlider {
  constructor() {
    super(CONFIG);
  }
}

export const relatedProducts = new RelatedProducts();
