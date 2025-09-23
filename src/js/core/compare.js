/**
 * Модуль работы со сравнением товаров (ванильная JS версия)
 * @module Compare
 */

import { ToggleModule } from './toggle';

const CONFIG = {
  moduleName: 'compare',
  ariaLabelIn: 'Добавить {productName} в сравнение',
  ariaLabelOut: 'Удалить {productName} из сравнения',
};


class Compare extends ToggleModule {
  constructor() {
    super(CONFIG);
  }
}

export const compare = new Compare();
