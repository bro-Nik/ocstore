/**
 * Базовый класс для попапов
 * @module BaseCartPopup
 */

import { validateNumberInput, validatePhoneInput, validateEmailInput } from '../services/validations';
import { events } from '../events/events';

const BASE_CONFIG = {
    scrollbarAdjustment: 8.5,
    mobileMarginAdjustment: 17,
    fadeDuration: 70
};

const BASE_ENDPOINTS = {
};

const  BASE_SELECTORS = {
  PAGE_FADER: '#pagefader',
  BODY: 'body',
  POPUP_CENTER: '.popup-center',
  POPUP_HEADER: '.popup-heading',
  // DROPDOWNS: [],
  TOOLTIPS: ['.tooltip'],
  SUBMIT_BTN: ['[data-action="submit"]'],
  PURCHASE_FORM: '#purchase-form',
};

const BASE_EVENT_HANDLERS = {
  'close': 'close',
};


class BasePopup {
  constructor(selectors, endpoints, config = {}, events = {}) {
    this.selectors = { ...BASE_SELECTORS, ...selectors };
    this.endpoints = { ...BASE_ENDPOINTS, ...endpoints };
    this.config = { ...BASE_CONFIG, ...config };
    this.events = { ...BASE_EVENT_HANDLERS, ...events };
    this.dialog = null;

    this.init();
  }

  init() {
    if (this.initialized) return;
    this.createDialog();
    this.bindEvents();
    this.initialized = true;
  }

  bindEvents() {};

  initPopupHandlers() {};

  /**
   * Создает элемент <dialog>
   * @param {string} dialogClass - CSS класс для dialog
   * @param {string} contentId - ID для контента
   * @param {string} contentClass - CSS класс для контента
   */
  createDialog() {
    this.dialog = document.createElement('dialog');
    this.dialog.innerHTML = `
      <div id="${this.selectors.POPUP_ID.substring(1)}"></div>
    `;
    document.body.appendChild(this.dialog);
  }

  /**
   * Показать/скрыть состояние загрузки
   * @param {boolean} show - Показать или скрыть
   */
  showLoadingState(show) {
    const pageFader = document.querySelector(this.selectors.PAGE_FADER);
    if (!pageFader) return;

    if (show) {
      pageFader.style.display = 'block';
      pageFader.style.transition = `opacity ${this.config.fadeDuration}ms`;
      pageFader.style.opacity = '1';
      document.querySelector(this.selectors.BODY).classList.add('razmiv2');
    } else {
      pageFader.style.opacity = '0';
      setTimeout(() => {
        pageFader.style.display = 'none';
      }, this.config.fadeDuration);
    }
  }
  prepareBeforeShow() {}

  /**
   * Показать попап
   */
  async show() {
    this.prepareBeforeOpen();
    this.showLoadingState(true);

    try {
      const response = await fetch(this.endpoints.CONTENT);
      const html = await response.text();
      
      this.dialog.querySelector(this.selectors.POPUP_ID).innerHTML = html;

      this.initPopupHandlers();
      this.initEventHandlers();
      this.prepareBeforeShow();
      this.dialog.showModal();
    } finally {
      this.showLoadingState(false);
    }
  }

  /**
   * Корректировка макета при появлении скроллбара
   */
  adjustLayout() {
    const hasScrollbar = document.body.scrollHeight > document.body.clientHeight;
    if (hasScrollbar) {
      const absoluteCart = document.querySelector(this.selectors.ABSOLUTE_CART);
      if (absoluteCart) absoluteCart.style.right = `${this.config.scrollbarAdjustment}px`;
      
      if (window.innerWidth < 768) {
        const mobileCart = document.querySelector(this.selectors.MOBILE_CART);
        if (mobileCart) mobileCart.style.marginRight = `${this.config.mobileMarginAdjustment}px`;
      }
    }
  }

  /**
   * Сброс корректировок скролла
   */
  resetScrollAdjustments() {
    const absoluteCart = document.querySelector(this.selectors.ABSOLUTE_CART);
    if (absoluteCart) absoluteCart.style.right = '';
    
    if (window.innerWidth < 768) {
      const mobileCart = document.querySelector(this.selectors.MOBILE_CART);
      if (mobileCart) mobileCart.style.marginRight = '';
    }
  }

  /**
  * Подготовка перед открытием попапа (универсальная)
  */
  prepareBeforeOpen() {
      // 1. Скрываем конфликтующие элементы
      this.hideDropdowns();
      this.hideTooltips();

      // 2. Корректируем layout
      this.adjustLayout();

      // 3. Блокируем прокрутку
      document.querySelector(this.selectors.BODY).classList.add('popup-open');
  }

  /**
  * Корректировка макета при появлении скроллбара
  */
  // adjustLayout() {
  //   const hasScrollbar = document.body.scrollHeight > window.innerHeight;
  //
  //   if (hasScrollbar) {
  //     // Коррекция фиксированных элементов
  //     document.querySelectorAll(this.selectors.ABSOLUTE_ELEMENTS).forEach(el => {
  //       el.style.right = `${this.config.scrollbarAdjustment}px`;
  //     });
  //
  //     // Мобильная адаптация
  //     if (window.innerWidth < 768) {
  //       document.querySelectorAll(this.selectors.ABSOLUTE_ELEMENTS).forEach(el => {
  //         el.style.marginRight = `${this.config.mobileMarginAdjustment}px`;
  //       });
  //     }
  //   }
  // }

  /**
  * Скрытие выпадающих меню
  */
  hideDropdowns() {
    document.querySelectorAll(this.selectors.DROPDOWNS).forEach(dropdown => {
      dropdown.style.display = 'none';
    });
  }

  /**
    * Скрытие tooltip'ов
    */
  hideTooltips() {
    document.querySelectorAll(this.selectors.TOOLTIPS).forEach(tooltip => {
      tooltip.style.display = 'none';
    });
  }


  /**
    * Восстановление состояния после закрытия
    */
  cleanupAfterClose() {
    // 1. Восстанавливаем элементы
    document.querySelectorAll(this.selectors.DROPDOWNS).forEach(dropdown => {
      dropdown.style.display = '';
    });

    // 2. Сбрасываем корректировки
    document.querySelectorAll(this.selectors.ABSOLUTE_ELEMENTS).forEach(el => {
      el.style.right = '';
      el.style.marginRight = '';
    });

    // 3. Разблокируем прокрутку
    document.querySelector(this.selectors.BODY).classList.remove('popup-open');
  }

  /**
   * Закрыть попап
   */
  close() {
    if (this.dialog) {
      this.dialog.close();
      this.cleanupAfterClose();
    }
  }

  /**
   * Показать ошибку для конкретного поля
   * @param {HTMLElement} field - Поле ввода
   * @param {string} message - Текст ошибки
   */
  showFieldError(field, message) {
    // Удаляем предыдущую ошибку
    const existingError = field.nextElementSibling;
    if (existingError && existingError.classList.contains('text-danger')) {
      existingError.remove();
    }
    
    field.classList.add('error_style');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-danger';
    errorDiv.textContent = message;
    field.after(errorDiv);
    
    // Прокручиваем к полю с ошибкой
    field.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  handleErrors(errors) {
    // Удаляем предыдущие ошибки
    console.log('handleErrors')
    this.dialog.querySelectorAll('.text-danger').forEach(el => el.remove());
    this.dialog.querySelectorAll('.error_style').forEach(el => el.classList.remove('error_style'));

    console.log(errors)
    if (errors.field) {
      for (const [fieldName, errorText] of Object.entries(errors.field)) {
        const field = this.dialog.querySelector(`[name="${fieldName}"]`);
        if (field) {
          field.classList.add('error_style');
          const errorDiv = document.createElement('div');
          errorDiv.className = 'text-danger';
          errorDiv.textContent = errorText;
          field.after(errorDiv);
        }
      }
    }

    if (errors.option) {
      for (const [optionId, errorText] of Object.entries(errors.option)) {
        // const element = this.dialog.querySelector(`input-option${optionId.replace('_', '-')}`);
        const element = this.dialog.querySelector(`#input-option${optionId.replace('_', '-')}`);
        if (element) {
          const errorDiv = document.createElement('div');
          errorDiv.className = 'text-danger';
          errorDiv.textContent = errorText;
          element.after(errorDiv);
        }
      }
    }

    if (errors.recurring) {
      const recurringSelect = this.dialog.querySelector('select[name="recurring_id"]');
      if (recurringSelect) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'text-danger';
        errorDiv.textContent = errors.recurring;
        recurringSelect.after(errorDiv);
      }
    }

    if (errors.z_min_sum) {
      const productMax = this.dialog.querySelector('.product_max');
      if (productMax) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'text-danger';
        errorDiv.textContent = errors.z_min_sum;
        productMax.after(errorDiv);
      }
    }
  }

  masked(element, show) {
    // const element = this.dialog.querySelector(id);
    if (!element) return;
    
    // element.classList.toggle('mask-visible', show);
    element.classList.toggle('mask-hidden', !show);
  }
  initEventHandlers() {
    // Обработчики внутри попапа
    events.addHandlers(this.events, this.dialog, this)
  }

  async handleCheckout() {
    console.log('handleCheckout start')
    // Старый код - повторяющийся нужно убрать, не повторяющийся переписать на ванильном JS
    const content = this.dialog.querySelector(this.selectors.POPUP_ID);
    this.masked(content, true);
    
    try {
      const form = this.dialog.querySelector(this.selectors.PURCHASE_FORM);

      // Валидация телефона
      const phoneInput = form.querySelector('input[name="telephone"]');
      const phoneError = validatePhoneInput(phoneInput);
      if (phoneError) {
        this.showFieldError(phoneInput, phoneError);
        return;
      }
      
      // Валидация email, если поле присутствует
      const emailInput = form.querySelector('input[name="email"]');
      const emailError = validateEmailInput(emailInput);
      if (emailError) {
        this.showFieldError(emailInput, emailError);
        return;
      }

      const formData = new FormData(form);
      
      const response = await fetch(this.endpoints.MAKE_ORDER, {
        method: 'POST',
        body: formData
      });
      

      const json = await response.json();
      console.log(json)
      
      if (json.error) {
        this.handleErrors(json.error);
      } else if (json.output) {
        this.handleSuccess(json.output);
      }
    } catch (error) {
      console.error('Ошибка при оформлении заказа:', error);
    } finally {
      this.masked(content, false);
    }
  }

  handleSuccess(output) {
    const popup = this.dialog.querySelector(this.selectors.POPUP_CENTER)
    if (popup) popup.innerHTML = output;
    
    const checkoutBtn = this.dialog.querySelector(this.selectors.SUBMIT_BTN);
    if (checkoutBtn) checkoutBtn.remove();
    
    // this.updateCartStatus();
  }

}


export { BasePopup };
