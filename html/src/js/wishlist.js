/**
 * Модуль работы с избранным (ванильная JS версия)
 * @module Wishlist
 */

import { ToggleModule } from './core/toggle';

const CONFIG = {
  moduleName: 'wishlist',
  titleIn: 'В закладки',
  titleOut: 'Из закладок',
  endpoints: {
    toggle: 'index.php?route=account/wishlist/add',
  },
  selectors: {
    btns: [
      '.product-thumb.product_{product_id} .wishlist a',       // Кнопки в карточках товаров
      '.product-info a.wishlist.pjid_{product_id}',           // Кнопки на странице товара
      '.cart_wish a.wishlist.wishlist_wprid_{product_id}'     // Кнопки в корзине/блоке покупок
    ],
  },
};


class Wishlist extends ToggleModule {
  constructor() {
    super(CONFIG);
  }
}

export const wishlist = new Wishlist();
