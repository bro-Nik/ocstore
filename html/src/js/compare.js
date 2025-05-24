/**
 * Модуль работы со сравнением товаров (ванильная JS версия)
 * @module Compare
 */

import { ToggleModule } from './core/toggle';

const CONFIG = {
  moduleName: 'compare',
  endpoints: {
    toggle: 'index.php?route=product/compare/add',
  },
  selectors: {
    btns: [
      '.product-thumb.product_{product_id} .compare a', // Кнопки в миниатюрах товаров
      '.product-info .compare.pjid_{product_id}',      // Кнопки на странице товара
    ],
  },
};


class Compare extends ToggleModule {
  constructor() {
    super(CONFIG);
  }
}

export const compare = new Compare();
