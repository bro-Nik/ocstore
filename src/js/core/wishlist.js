/**
 * Модуль работы с избранным (ванильная JS версия)
 * @module Wishlist
 */

import { ToggleModule } from './toggle';

const CONFIG = {
  moduleName: 'wishlist',
  titleIn: 'В закладки',
  titleOut: 'Из закладок',
  selectors: {
    btns: [
      '[data-action="wishlist-toggle"][data-product-id="{product_id}"]'
    ],
  },
};


class Wishlist extends ToggleModule {
  constructor() {
    super(CONFIG);
  }
}

export const wishlist = new Wishlist();
