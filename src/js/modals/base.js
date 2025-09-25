/**
 * Базовый класс для попапов
 * @module BaseCartPopup
 */

import { events } from '../events/events';
import { LoadingManager } from '../services/loading';
import { createError, createElement, toggleClass } from '../services/dom';
import { eventManager } from '../events/event-manager';
import { validator } from '../services/validations';
import { LoaderMixin } from '../mixins/loader';
import { FormMixin } from '../mixins/form';

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

    Object.assign(this, LoaderMixin, FormMixin);
    this.init();
  }

  init() {
    if (this.initialized) return;
    this.bindEvents();
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

    if (!this.dialog) this.createDialog();
    if (!this.loading) this.loading = new LoadingManager(this.dialog)
    this.loading.show();

    // Скрываем конфликтующие элементы
    this.hideDropdowns();
    this.hideTooltips();
    this.hideVisible();

    // Блокируем прокрутку
    document.body.classList.add('popup-open');
  }

  afterShow() {
    this.bindPopupEvents();
    this.loading.hide();
  }

  async show(url) {
    await this.beforeShow();

    this.dialog.showModal();

    if (typeof url !== 'string') url = this.endpoints.content;
    await this.loadHtml(url, this.content);

    this.afterShow();
  }

  /**
  * Скрытие выпадающих меню
  */
  hideDropdowns() {
    document.querySelectorAll(this.selectors.dropdowns).forEach(dropdown => {
      dropdown.style.display = 'none';
    });
  }

  hideVisible() {
    document.querySelectorAll('.temporarily-visible').forEach(el => {
      el.classList.remove('temporarily-visible');
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

  close(e, target) {
    this.delEvents()
    this.dialog.close();
    this.content.innerHTML = '';
    this.cleanupAfterClose();
  }

  handleErrors(errors) {
    // Удаляем предыдущие ошибки
    this.dialog.querySelectorAll('.text-danger').forEach(el => el.remove());
    this.dialog.querySelectorAll('.error_style').forEach(el => el.classList.remove('error_style'));

    // if (errors.option) {
    //   for (const [optionId, errorText] of Object.entries(errors.option)) {
    //     const element = this.dialog.querySelector(`#input-option${optionId.replace('_', '-')}`);
    //     if (element) element.after(createError(errorText));
    //   }
    // }

    // if (errors.recurring) {
    //   const recurringSelect = this.dialog.querySelector('select[name="recurring_id"]');
    //   if (recurringSelect) recurringSelect.after(createError(errors.recurring));
    // }

    // if (errors.z_min_sum) {
    //   const productMax = this.dialog.querySelector('.product_max');
    //   if (productMax) productMax.after(createError(errors.z_min_sum));
    // }
  }

  async handleSubmit() {
    const form = this.dialog.querySelector(this.selectors.purchaseForm);
    const json = await this.submit(form, this.endpoints.submit);

    if (!json) return;
    if (json.error) this.handleErrors(json.error);
    if (json.html) this.handleSuccess(json.html);
  }

  handleSuccess(html) {
    const popup = this.dialog.querySelector(this.selectors.popupCenter)
    if (popup) popup.innerHTML = html;

    this.removecheckoutBtn()
  }

  removecheckoutBtn() {
    const checkoutBtn = this.dialog.querySelector(this.selectors.submitBtn);
    if (checkoutBtn) checkoutBtn.remove();

    // const quickCartBtn = this.dialog.querySelector('[data-action="quick-order"]');
    // if (quickCartBtn) quickCartBtn.remove();

    // const checkoutLinkBtn = this.dialog.querySelector('.checkout-btn');
    // if (checkoutLinkBtn) checkoutLinkBtn.remove();
  }
}


export { BasePopup };
