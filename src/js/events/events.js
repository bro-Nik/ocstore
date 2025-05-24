import { eventManager } from './event-manager';

class EventSystem {
  constructor() {
    this.globalHandlers = {};
  }

  addHandlers(handlers = [], element = document, context = null) {
    Object.entries(handlers).forEach(([action, method]) => {
      eventManager.delegate(
        element,
        'click',
        `[data-action="${action}"]`,
        (e, target) => {
          // e.preventDefault();
          if (context && context[method]) {
            context[method].call(context, e, target);
          }
        }
      );
    });
  }
}

export const events = new EventSystem();
