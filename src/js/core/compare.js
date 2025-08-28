/**
 * Модуль работы со сравнением товаров (ванильная JS версия)
 * @module Compare
 */

import { ToggleModule } from './toggle';

const CONFIG = {
  moduleName: 'compare',
  selectors: {
    btns: [
      '[data-action="compare-toggle"][data-product-id="{product_id}"]'
    ],
  },
};


class Compare extends ToggleModule {
  constructor() {
    super(CONFIG);
  }
}

export const compare = new Compare();
