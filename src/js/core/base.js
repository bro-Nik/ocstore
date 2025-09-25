/**
 * Базовый класс для модулей
 * @class BaseModule
 */

import { LoadingManager } from '../services/loading';
import { NotificationManager } from '../services/notifications';
import { LoaderMixin } from '../mixins/loader';
import { FormMixin } from '../mixins/form';
import { eventManager } from '../events/event-manager';
import { events } from '../events/events';

const BASE_CONFIG = {};

export class BaseModule {

  constructor(config = {}) {
    this.config = { ...BASE_CONFIG, ...config };
    this.selectors = this.config.selectors;
    this.endpoints = this.config.endpoints;
    this.events = this.config.events;
    this.dialog = null;
    this.content = null;
    this.loading = null;
    this.initialized = false;
    this.notifications = new NotificationManager(this.config.moduleName);

    Object.assign(this, LoaderMixin, FormMixin);

    // this.init();
  }

  init() {
    if (this.initialized) return;
    this.bindEvents();
    this.initialized = true;
  }

  bindEvents() {
    events.addHandlers(this.config.globalEvents, document, this);
  };

  showLoading(btn) {
    const loading = new LoadingManager(btn);
    loading.show();
  };

  hideLoading(btn) {
    const loading = new LoadingManager(btn);
    loading.hide();
  };

  /**
   * Обработка ошибок
   * @param {Error} error - Объект ошибки
   */
  handleError(error) {
    console.error(`[${this.moduleName}] Error:`, error);
    this.dispatchEvent('error', { error: error.message });
  };


  /**
   * Отправка данных формы
   * @param {string} url - Эндпоинт
   * @param {FormData} formData - Данные формы
   * @returns {Promise}
   */
  sendFormData(url, formData) {
    return this.postFormData(url, formData);
  }

  /**
   * Генерация кастомных событий
   * @param {string} eventName - Имя события
   * @param {Object} detail - Дополнительные данные
   */
  dispatchEvent(eventName, detail = {}) {
    const event = new CustomEvent(`${this.config.moduleName}-events:${eventName}`, {
      detail,
      bubbles: true,
      cancelable: true
    });
    document.dispatchEvent(event);
  }

  event(event, selector, handler, options = {}) {
    eventManager.delegate(
      document, // контекст сохраняется в классе
      event,
      selector,
      handler.bind(this), // автоматический биндинг контекста
      options
    );
  }
}
