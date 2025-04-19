/**
 * Базовый класс для модулей
 * @class BaseModule
 */

import { animateScale } from '../services/animations';
import { postFormData } from '../services/api';

export class BaseModule {
  constructor(config, selectors) {
    this.config = config;
    this.selectors = selectors;
    this.initialized = false;
  }

  /**
   * Показывает/скрывает состояние загрузки
   * @param {HTMLElement} element - Элемент для изменения
   * @param {boolean} show - Показать или скрыть
   */
  showLoadingState(element, show) {
    if (!element) return;
    element.classList.toggle('btn-loading', show);
  }

  /**
   * Анимация элемента (масштабирование)
   * @param {HTMLElement} element - Элемент для анимации
   */
  animateElement(element) {
    animateScale(element);
  }

  /**
   * Обновляет счетчик общего количества
   * @param {number} total - Новое значение счетчика
   */
  updateTotalCount(total) {
    const totalElement = document.querySelector(this.selectors.total);
    if (totalElement) {
      totalElement.textContent = total;
      this.animateElement(totalElement);
    }
  }

  /**
   * Обработка ошибок
   * @param {Error} error - Объект ошибки
   */
  handleError(error) {
    console.error(`[${this.moduleName}] Error:`, error);
    this.dispatchEvent('error', { error: error.message });
  };

  /**
   * Обновляет состояние кнопок
   * @param {string|number} productId - ID товара
   * @param {Object} params - Параметры обновления
   * @param {string} params.baseClass - Базовый CSS-класс
   * @param {string} params.newClass - Новый CSS-класс
   * @param {string} params.newTitle - Текст для title
   * @param {string} [params.classToRemove] - Класс для удаления
   */
  updateButtons(productId, { baseClass, newClass, newTitle, classToRemove, newText, newUrl }) {
    const selectors = this.getSelectors(this.selectors.btns, { product_id: productId });
    selectors.forEach(selector => {
      document.querySelectorAll(selector).forEach(button => {
        // Удаляем класс (если передан)
        if (classToRemove) button.classList.remove(classToRemove);

        // Сбрасываем и добавляем классы
        button.className = baseClass;
        if (newClass) button.classList.add(newClass);

        // Обновляем атрибуты
        button.setAttribute('title', newTitle);
        button.setAttribute('data-original-title', newTitle);
        button.setAttribute('data-toggle', 'tooltip');

        // Меняем текст, если нужно
        button.innerHTML = newText;

        // Ставим ссылку
        if (newUrl) {
          button.onclick = (e) => {
            e.preventDefault();
            window.location.href = newUrl;
          };
        }

        // Анимация
        this.animateElement(button);
      });
    });
  }

  /**
   * Отправка данных формы
   * @param {string} url - Эндпоинт
   * @param {FormData} formData - Данные формы
   * @returns {Promise}
   */
  sendFormData(url, formData) {
    return postFormData(url, formData);
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

  getSelectors(selectors, replacements = {}) {
    // Нормализуем входные данные в массив
    if (!selectors) return [];
    selectors = Array.isArray(selectors) ? selectors : [selectors];

    return selectors.map(selector => {
      let result = selector;
      for (const [id, value] of Object.entries(replacements)) {
        if (value === undefined || value === null) continue;
        result = result.replace(new RegExp(`{${id}}`, 'g'), value);
      }
      return result;
    });
  }
}
