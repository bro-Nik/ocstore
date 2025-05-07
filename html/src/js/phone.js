/**
 * Модуль работы с телефонным попапом
 * @module Phone
 */

import { BaseModule } from './core/base';
import { PhonePopup } from './phone-popup';

const CONFIG = {
  moduleName: 'phone',
  selectors: {
    popupTrigger: '.phone-show-popup'
  }
};

class Phone extends BaseModule {
  constructor() {
    super(CONFIG);
    this.popup = new PhonePopup();
    this.init();
  }

  init() {
    if (this.initialized) return;
    this.bindEvents();
    this.initialized = true;
  }

  bindEvents() {
    document.addEventListener('click', (e) => {
      const popupTrigger = e.target.closest(this.config.selectors.popupTrigger);
      if (popupTrigger) {
        e.preventDefault();
        this.popup.show();
      }
    });
  }
}

export const phone = new Phone();
