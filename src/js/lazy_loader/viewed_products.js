/**
 * Модуль просмотренных товаров
 * @module CallbackPopup
 */

import { ProductsSlider } from './products_slider.js';
import { getCookie } from '../cookie';

const CONFIG = {
  selectors: {
    containerId: 'viewed-products',
  },
  endpoints: {
    content: 'index.php?route=revolution/viewed_products',
  },
};

class ViewedProducts extends ProductsSlider {
  constructor() {
    super(CONFIG);
  }

  loadingCondition() {
    const infoEl = document.querySelector('#counter_data');
    if (!infoEl) return;

    const productId = infoEl.dataset.id;
    const products = getCookie('viewed') || [];
    const wasInCookies = products.includes(productId.toString());

    // Если это единственный товар то не показываем
    return ((products.length > 1) || (products.length == 1 && !wasInCookies));
  }
}

export const viewedProducts = new ViewedProducts();
