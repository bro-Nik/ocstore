/**
 * Модуль работы со сравнением товаров (ванильная JS версия)
 * @module Compare
 */

import { BaseModule } from './core/base';

const CONFIG = {
  moduleName: 'compare',
  endpoints: {
    toggle: 'index.php?route=product/compare/add',
  },
};

const SELECTORS = {
  total: '.compare-total', // Общее количество в сравнении
  btns: [
    '.product-thumb.product_{product_id} .compare a', // Кнопки в миниатюрах товаров
    '.product-info a.compare.pjid_{product_id}',      // Кнопки на странице товара
  ],
  toggle: 'compare-toggle',
  toggleClassToRemove: 'in_compare'
};

class Compare extends BaseModule {
  constructor() {
    super(CONFIG, SELECTORS);
    this.init();
  }

  init() {
    if (this.initialized) return;
    this.bindEvents();
    this.initialized = true;
  }

  bindEvents() {
    document.addEventListener('click', (e) => {
      const toggleButton = e.target.closest(`.${this.selectors.toggle}`);
      if (toggleButton) {
        e.preventDefault();
        const productId = toggleButton.dataset.productId;
        const brand = toggleButton.dataset.brand || '';
        if (productId) this.toggle(productId, brand, toggleButton);
        return;
      }
    });
  }

  /**
   * Добавление и удаление товара в сравнение
   * @param {number} product_id - ID товара
   * @param {string} brand - Бренд товара
   * @param {HTMLElement} btn - Кнопка для управления состоянием
   */
  toggle(product_id, brand, btn) {
    this.showLoadingState(btn, true);
    
    const formData = new FormData();
    formData.append('product_id', product_id);
    if (brand) formData.append('brand', brand);

    this.sendFormData(this.config.endpoints.toggle, formData)
      .then(json => this.handleToggleResponse(json, product_id))
      .catch(error => this.handleError(error))
      .finally(() => this.showLoadingState(btn, false));
  }

  /**
   * Обработка успешного ответа от сервера
   * @param {Object} json - Ответ сервера
   * @param {number} product_id - ID товара
   */
  handleToggleResponse(json, product_id) {
    if (!json.success) return;

    // Обновляем общее количество товаров в сравнении
    this.updateTotalCount(json.total);

    // Обновляем кнопки
    const updateParams = {
      baseClass: 'compare compare-toggle',
      newClass: json.class_compare,
      newTitle: json.button_compare,
      classToRemove: this.selectors.toggleClassToRemove
    };
    
    this.updateButtons(product_id, updateParams);
    
    // Генерируем событие
    this.dispatchEvent('toggle', { productId: product_id, data: json });
  }
}

export const compare = new Compare();
