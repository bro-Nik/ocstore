/**
 * Модуль работы с избранным (ванильная JS версия)
 * @module Wishlist
 */

import { BaseModule } from './core/base';

const CONFIG = {
  moduleName: 'wishlist',
  endpoints: {
    toggle: 'index.php?route=account/wishlist/add',
  },
};

const SELECTORS = {
  total: '#wishlist-total',    // Счетчик общего количества в избранном
  btns: [
    '.product-thumb.product_{product_id} .wishlist a',       // Кнопки в карточках товаров
    '.product-info a.wishlist.pjid_{product_id}',           // Кнопки на странице товара
    '.cart_wish a.wishlist.wishlist_wprid_{product_id}'     // Кнопки в корзине/блоке покупок
  ],
  toggle: 'wishlist-toggle',
  toggleClassToRemove: 'in_wishlist'
};

class Wishlist extends BaseModule {
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
        if (productId) this.toggle(productId, toggleButton);
      }
    });
  }

  /**
   * Переключение состояния товара (добавление/удаление из избранного)
   * @param {number} product_id - ID товара
   * @param {HTMLElement} btn - Кнопка, инициировавшая действие
   */
  toggle(product_id, btn) {
    this.showLoadingState(btn, true);

    const formData = new FormData();
    formData.append('product_id', product_id);

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

    // Обновляем общее количество товаров в wishlist
    this.updateTotalCount(json.total);

    // Обновляем кнопки
    const updateParams = {
      baseClass: this.selectors.toggle,
      newClass: json.class_wishlist,
      newTitle: json.button_wishlist,
      classToRemove: this.selectors.toggleClassToRemove
    };

    this.updateButtons(product_id, updateParams);

    // Генерируем событие
    this.dispatchEvent('toggle', { productId: product_id, data: json });
  }
}

export const wishlist = new Wishlist();
