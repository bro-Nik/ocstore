/**
 * Модуль работы с оформлением заказа
 * @module Purchase
 */

import { BaseModule } from './core/base';
import { PurchasePopup } from './purchase-popup';

const CONFIG = {
  moduleName: 'purchase',
  endpoints: {
    quickOrder: 'index.php?route=revolution/revpopuporder&revproduct_id='
  }
};

const SELECTORS = {
  quickOrderButtons: [
    '.product-thumb .quick-order-btn', // Кнопки в карточках товаров
    '.product-info .quick-order-btn',  // Кнопки на странице товара
    '.quickview .quick-order-btn'      // Кнопки в быстром просмотре
  ]
};

export class Purchase extends BaseModule {
  constructor() {
    super(CONFIG, SELECTORS);
    this.popup = new PurchasePopup();
    this.init();
  }

  init() {
    if (this.initialized) return;
    this.bindEvents();
    this.initialized = true;
  }

  bindEvents() {
    document.addEventListener('click', (e) => {
      // Обработка кликов по кнопкам быстрого заказа
      const quickOrderBtn = e.target.closest('.quick-order-btn');
      if (quickOrderBtn) {
        e.preventDefault();
        const productId = quickOrderBtn.dataset.productId;
        if (productId) this.showQuickOrderPopup(productId);
      }
    });
  }

  /**
   * Показать попап быстрого заказа
   * @param {string} productId - ID товара
   */
  showQuickOrderPopup(productId) {
    this.popup.show(productId);
  }
}

// Создаем и экспортируем экземпляр класса
export const purchase = new Purchase();
