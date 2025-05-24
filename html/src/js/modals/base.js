/**
 * Базовый класс для попапов
 * @module BaseCartPopup
 */

import { validateForm } from '../services/validations';
import { events } from '../events/events';
import { LoadingManager } from '../services/loading';
import { createError, createElement, toggleClass } from '../services/dom';
import { eventManager } from '../events/event-manager';

const BASE_CONFIG = {
  selectors: {
    popupId: '',
    popupClass: '',
    popupCenter: '.popup-center',
    popupHeader: '.popup-heading',
    tooltips: ['.tooltip'],
    submitBtn: ['[data-action="submit"]'],
    purchaseForm: '#purchase-form',
  },
  modalEvents: {
    'close': 'close',
    'submit': 'handleSubmit',
  },
};

class BasePopup {
  constructor(config = {}) {
    this.config = {
      ...BASE_CONFIG, ...config,
      selectors: { ...BASE_CONFIG.selectors, ...config.selectors },
      modalEvents: { ...BASE_CONFIG.modalEvents, ...config.modalEvents },
      globalEvents: { ...BASE_CONFIG.globalEvents, ...config.globalEvents },
      endpoints: { ...BASE_CONFIG.endpoints, ...config.endpoints },
    };
    this.selectors = this.config.selectors;
    this.endpoints = this.config.endpoints;
    this.dialog = null;
    this.content = null;
    this.loading = null;
    this.initialized = false;

    this.init();
  }

  init() {
    if (this.initialized) return;
    this.createDialog();
    this.bindEvents();
    this.loading = new LoadingManager(this.dialog)

    this.initialized = true;
  }

  bindEvents() {
    events.addHandlers(this.config.globalEvents, document, this);
  };

  bindPopupEvents() {
    events.addHandlers(this.config.modalEvents, this.dialog, this)
  };

  addEvent(event, selector, handler, options = {}) {
    eventManager.delegate(
      this.dialog, // контекст сохраняется в классе
      event,
      selector,
      handler.bind(this), // автоматический биндинг контекста
      options
    );
  }

  delEvents() {
    eventManager.offAll(this.dialog);
  }

  /**
   * Создает элемент <dialog>
   * @param {string} dialogClass - CSS класс для dialog
   * @param {string} contentId - ID для контента
   * @param {string} contentClass - CSS класс для контента
   */
  createDialog() {
    this.dialog = createElement('dialog', '', this.selectors.popupClass, { 'aria-modal': 'true' });
    this.content = createElement('div', this.selectors.popupId);

    this.dialog.appendChild(this.content);
    document.body.appendChild(this.dialog);
  }

  beforeShow() {
    // Закрываем диалоговые
    const openedDialog = document.querySelector('dialog[open]');
    if (openedDialog) openedDialog.close()

    this.loading.show();

    // Скрываем конфликтующие элементы
    this.hideDropdowns();
    this.hideTooltips();

    // Блокируем прокрутку
    document.body.classList.add('popup-open');
  }

  async loadHtml(url, obj) {
    if (obj) {
      const response = await fetch(url);
      obj.innerHTML = await response.text();
    }
  };

  async loadJson(url) {
    const response = await fetch(url);
    return await response.json();
  };

  async load(url) {
    const response = await fetch(url);
    return await response;
  };

  afterShow() {
    this.bindPopupEvents();
    this.loading.hide();
  }

  /**
   * Показать попап
   */
  async show(url) {
    await this.beforeShow();

    // Генерируем событие ДО открытия (можно отменить)
    const openingEvent = new CustomEvent('popup:opening', {
      bubbles: true,
      cancelable: true,
      detail: { popup: this, url }
    });
    
    if (!this.dialog.dispatchEvent(openingEvent)) {
      return; // Если событие отменено (preventDefault)
    }

    this.dialog.showModal();

    if (typeof url !== 'string') url = this.endpoints.content;

    await this.loadHtml(url, this.content);

    // Генерируем событие ПОСЛЕ открытия
    const openedEvent = new CustomEvent('popup:opened', {
      bubbles: true,
      detail: { popup: this, url }
    });
    this.dialog.dispatchEvent(openedEvent);
    await this.afterShow();
  }

  /**
  * Скрытие выпадающих меню
  */
  hideDropdowns() {
    document.querySelectorAll(this.selectors.dropdowns).forEach(dropdown => {
      dropdown.style.display = 'none';
    });
  }

  /**
  * Скрытие tooltip'ов
  */
  hideTooltips() {
    document.querySelectorAll(this.selectors.tooltips).forEach(tooltip => {
      tooltip.style.display = 'none';
    });
  }

  /**
  * Восстановление состояния после закрытия
  */
  cleanupAfterClose() {
    // Восстанавливаем элементы
    document.querySelectorAll(this.selectors.dropdowns).forEach(dropdown => {
      dropdown.style.display = '';
    });

    // Разблокируем прокрутку
    document.body.classList.remove('popup-open');
  }

  /**
   * Закрыть попап
   */
  close(e, target) {
    this.delEvents()
    this.dialog.close();
    this.cleanupAfterClose();
  }

  handleErrors(errors) {
    // Удаляем предыдущие ошибки
    this.dialog.querySelectorAll('.text-danger').forEach(el => el.remove());
    this.dialog.querySelectorAll('.error_style').forEach(el => el.classList.remove('error_style'));

    if (errors.field) {
      for (const [fieldName, errorText] of Object.entries(errors.field)) {
        const field = this.dialog.querySelector(`[name="${fieldName}"]`);
        if (field) {
          field.classList.add('error_style');
          field.after(createError(errorText));
        }
      }
    }

    if (errors.option) {
      for (const [optionId, errorText] of Object.entries(errors.option)) {
        const element = this.dialog.querySelector(`#input-option${optionId.replace('_', '-')}`);
        if (element) element.after(createError(errorText));
      }
    }

    if (errors.recurring) {
      const recurringSelect = this.dialog.querySelector('select[name="recurring_id"]');
      if (recurringSelect) recurringSelect.after(createError(errors.recurring));
    }

    if (errors.z_min_sum) {
      const productMax = this.dialog.querySelector('.product_max');
      if (productMax) productMax.after(createError(errors.z_min_sum));
    }
  }

  async handleSubmit() {
    const form = this.dialog.querySelector(this.selectors.purchaseForm);

    // Валидация формы
    if (!validateForm(form)) return;

    try {
      this.loading.show();

      const formData = new FormData(form);
      
      const response = await fetch(this.endpoints.submit, {
        method: 'POST',
        body: formData
      });
      
      const json = await response.json();
      
      if (json.error) {
        this.handleErrors(json.error);
      } else if (json.output) {
        this.handleSuccess(json.output);
      }
    } catch (error) {
      console.error('Ошибка при оформлении заказа:', error);
    } finally {
      this.loading.hide();
    }
  }

  handleSuccess(output) {
    const popup = this.dialog.querySelector(this.selectors.popupCenter)
    if (popup) popup.innerHTML = output;

    this.removecheckoutBtn()
  }

  removecheckoutBtn() {
    const checkoutBtn = this.dialog.querySelector(this.selectors.submitBtn);
    if (checkoutBtn) checkoutBtn.remove();

    const quickCartBtn = this.dialog.querySelector('[data-action="quick-order"]');
    if (quickCartBtn) quickCartBtn.remove();

    const checkoutLinkBtn = this.dialog.querySelector('.checkout-btn');
    if (checkoutLinkBtn) checkoutLinkBtn.remove();
  }
}


export { BasePopup };
