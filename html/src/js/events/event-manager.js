import { throttle, debounce } from '../services/utils';

class EventManager {
  constructor() {
    this.handlers = new Map();
  }

  // Для обычных событий
  on(element, event, handler, options = {}) {
    const wrappedHandler = options.throttle 
      ? throttle(handler, options.throttle)
      : options.debounce
      ? debounce(handler, options.debounce)
      : handler;
    
    element.addEventListener(event, wrappedHandler);
    this._storeHandler(element, event, wrappedHandler);
  }

  // Для делегированных событий
  delegate(parent, event, selector, handler, options = {}) {
    const wrappedHandler = (e) => {
      const target = e.target.closest(selector);
      if (target) handler(e, target);
    };
    
    this.on(parent, event, wrappedHandler, options);
  }

  // Отмена конкретного типа событий
  off(element, event) {
    if (!this.handlers.has(element)) return;
    
    const elementHandlers = this.handlers.get(element);
    if (!elementHandlers.has(event)) return;

    for (const handler of elementHandlers.get(event)) {
      element.removeEventListener(event, handler);
    }
    
    elementHandlers.delete(event);
  }

  // Отмена всех событий элемента
  offAll(element) {
    if (!this.handlers.has(element)) return;

    const elementHandlers = this.handlers.get(element);
    
    // Проверяем, что это объект (не null/undefined)
    if (!elementHandlers || typeof elementHandlers !== 'object') {
      this.handlers.delete(element);
      return;
    }

    // Безопасная итерация по событиям
    const events = Object.keys(elementHandlers);
    for (const event of events) {
      const handlers = elementHandlers[event];
      
      // Проверяем, что handlers - это Set/Array
      if (handlers && typeof handlers.forEach === 'function') {
        handlers.forEach(handler => {
          element.removeEventListener(event, handler);
        });
      }
      
      delete elementHandlers[event];
    }

    this.handlers.delete(element);
  }

  _storeHandler(element, event, handler) {
    if (!this.handlers.has(element)) {
      this.handlers.set(element, {});
    }
    if (!this.handlers.get(element)[event]) {
      this.handlers.get(element)[event] = [];
    }
    this.handlers.get(element)[event].push(handler);
  }
}

export const eventManager = new EventManager();
