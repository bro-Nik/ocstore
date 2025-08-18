import { createElement } from '../services/dom';

/**
  * Показывает уведомления
  */
const ID_CONTAINER = '#toast-container';

export class NotificationManager {
  constructor(moduleName) {
    this.container = document.body.querySelector(ID_CONTAINER);
    if (!this.container) {
      this.container = document.body.append(createElement('div', ID_CONTAINER));
    }
    this.toastsSelector = moduleName ? `.toast-${moduleName}` : '';
  }
  showList(messages) {
    for (const message of messages) {
      this.show(message.text, message.category);
    }
  }
  
  show(message, category) {
    if (!this.container) return;
    // this.clear();

    if (Array.isArray(message) && message.length > 0) {
      for (const m of message) {
        this.show(m.text, m.category);
      }
      return;
    }

    var bgClass, icon;
    switch(category){
    case 'error':
      bgClass = 'bg-danger';
      icon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle text-white" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"></path><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"></path></svg>`
      break;
    case 'warning':
      bgClass = 'bg-warning';
      icon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle text-white" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"></path><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"></path></svg>`
      break;
    case 'success':
      // bgClass = 'bg-success';
      bgClass = 'bg-primary';
      icon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle text-white" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/></svg>`
      break;
    default:
      bgClass = 'bg-primary';
      icon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle text-white" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/></svg>`
    }

    const notification = createElement('div', '', this.toastsSelector);
    
    notification.innerHTML = `
      <div class="toast alert-success" role="status" aria-live="polite">
        <div class="toast-icon ${bgClass}">${icon}</div>
        <div class="toast-body toast-message">${message}</div>
        <button type="button" class="btn-close toast-close" aria-label="Закрыть"></button>
      </div>
    `;

    this.container.insertBefore(notification, this.container.firstChild);

    // Автоматическое скрытие через 5 секунд
    const timer = setTimeout(() => {
      notification.style.opacity = '0';
      notification.style.transition = 'opacity 0.5s ease';
      setTimeout(() => notification.remove(), 500);
    }, 5000);
    
    // Обработчик закрытия по кнопке
    notification.querySelector('.toast-close').addEventListener('click', () => {
      clearTimeout(timer);
      notification.remove();
    });
  }

  clear() {
    const notifications = this.container.querySelectorAll(this.toastsSelector || '.toast');
    for (const notification of notifications) {
      notification.remove();
    }
  }
}
