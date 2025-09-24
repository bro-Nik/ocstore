/**
 * Модуль работы с избранным (ванильная JS версия)
 * @module Wishlist
 */

import { ToggleModule } from './toggle';

const CONFIG = {
  moduleName: 'wishlist',
  ariaLabelIn: 'Добавить в избранное',
  ariaLabelOut: 'Удалить из избранного',
};


class Wishlist extends ToggleModule {
  constructor() {
    super(CONFIG);
  }
}

export const wishlist = new Wishlist();
