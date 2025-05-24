import { eventManager } from './events/event-manager';

class UiHelpers {

  constructor(config = {}) {
    this.initialized = false;
    this.init();
  }

  init() {
    if (this.initialized) return;
    this.bindEvents();
    this.initialized = true;
  }

  addEvent(event, selector, handler, options = {}) {
    eventManager.delegate(
      document, // контекст сохраняется в классе
      event,
      selector,
      handler.bind(this), // автоматический биндинг контекста
      options
    );
  }

  bindEvents() {
    this.addEvent('click', '[data-show-target]', this.showTarget);
  };

  showTarget(e, btn) {
    e.preventDefault();
    
    // Получаем селектор целевого элемента из атрибута
    const targetSelector = btn.getAttribute('data-show-target');
    const targetElement = document.querySelector(targetSelector);
    
    if (!targetElement) return;
    
    // Переключаем видимость целевого элемента
    targetElement.classList.toggle('temporarily-visible');
  };

}

export const uiHelpers = new UiHelpers();
