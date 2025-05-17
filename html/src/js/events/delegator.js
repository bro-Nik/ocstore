/**
 * Универсальный обработчик делегированных событий
 * @param {Event} e - Объект события
 * @param {Object} handlers - Объект с обработчиками { 'action-name': 'handlerMethod' }
 * @param {Object} context - Контекст вызова (this)
 */
export function handleDelegateEvents(e, handlers, context) {
  for (const [action, handler] of Object.entries(handlers)) {
    const element = e.target.closest(`[data-action="${action}"]`);
    if (element && context[handler]) {
      e.preventDefault();
      context[handler].call(context, e, element);
      break;
    }
  }
}
