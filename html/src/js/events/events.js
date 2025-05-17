import { handleDelegateEvents } from './delegator';

const GLOBAL_EVENT_HANDLERS = {
};

class Events {
  constructor() {
    this.handlers = { ...GLOBAL_EVENT_HANDLERS };
    this.init();
  }

  init() {
    if (this.initialized) return;
    this.bindEvents();
    this.initialized = true;
  }

  /**
   * Добавляет новые обработчики событий
   * @param {Object} newHandlers - { 'action-name': 'handlerMethod' }
   */
  addHandlers(newHandlers, element, context) {
    this.handlers = { ...this.handlers, ...newHandlers };
    this.bindEvents(newHandlers, element, context);
  }

  bindEvents(handlers, element, context) {
    if (!element) return;
    element.addEventListener('click', (e) => {
      handleDelegateEvents(e, handlers, context);
    });
  }

}

export const events = new Events();
