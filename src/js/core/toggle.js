import { BaseModule } from './base';
import { events } from '../events/events';

const TOGGLE_CONFIG = {
//   moduleName: '',
//   textIn: '',
//   titleIn: '',
//   textOut: '',
//   titleOut: '',
//   actionIn: '',
//   actionOut: '',
//   endpoints: {
//     toggle: '',
//   },
};

export class ToggleModule extends BaseModule {

  constructor(config = {}) {
    super({ ...TOGGLE_CONFIG, ...config });
  }

  bindEvents() {
    const eventName = `${this.config.moduleName}-toggle`;
    events.addHandlers({ [eventName]: 'toggle' }, document, this);
    super.bindEvents();
  }

  /**
   * Добавление и удаление товара
   * @param {number} product_id - ID товара
   * @param {string} brand - Бренд товара
   * @param {HTMLElement} btn - Кнопка для управления состоянием
   */
  toggle(e, btn, formData = null) {
    this.showLoading(btn);

    const productId = btn.dataset.productId;
    if (!productId) return;
    const brand = btn.dataset.brand;
    
    formData = formData !== null ? formData : new FormData();
    formData.append('product_id', productId);
    if (brand) formData.append('brand', brand);

    this.sendFormData(this.config.endpoints.toggle, formData)
      .then(json => this.handleToggleResponse(json, productId))
      .catch(error => this.handleError(error))
      .finally(() => this.hideLoading(btn));
  }


  handleToggleResponse(json, productId) {
    if (json.redirect) return window.location.assign(json.redirect);
    if (json.error) return this.showErrors(json.error);
    if (!json.success) return;

    // Обновляем общее количество товаров
    this.updateTotalCount(json.total);

    // Обновляем кнопки
    this.updateButtons(productId);

    // Генерируем событие
    this.dispatchEvent('toggle', { productId: productId, data: json });
  }

  /**
   * Обновляет состояние кнопок
   * @param {string|number} productId - ID товара
   * @param {Object} params - Параметры обновления
   * @param {string} params.baseClass - Базовый CSS-класс
   * @param {string} params.newClass - Новый CSS-класс
   * @param {string} params.newTitle - Текст для title
   * @param {string} [params.classToRemove] - Класс для удаления
   */
  updateButtons(productId) {
    let selectors = [];

    if (productId === undefined) selectors = [`.in-${this.config.moduleName}`];
    else selectors = this.getSelectors(this.selectors.btns, { product_id: productId });

    selectors.forEach(selector => {
      document.querySelectorAll(selector).forEach(button => {
        this.updateButton(button);
      });
    });
  }

  updateButton(button) {
    const inListSelector = `in-${this.config.moduleName}`
    const inList = button.classList.contains(inListSelector)

    if (inList) {
      // Удаляем класс принадлежности к списку
      button.classList.remove(inListSelector);

      // Обновляем атрибуты
      button.setAttribute('title', this.config.titleIn || '');
      button.setAttribute('data-original-title', this.config.titleIn || '');
      button.setAttribute('data-toggle', 'tooltip');

      // Меняем action
      button.setAttribute('data-action', `${this.config.moduleName}-toggle`);

      // Меняем текст
      if (this.config.textIn) button.innerHTML = this.config.textIn;

    } else {
      // Сбрасываем и добавляем классы
      button.classList.add(inListSelector);

      // Обновляем атрибуты
      button.setAttribute('title', this.config.titleOut || '');
      button.setAttribute('data-original-title', this.config.titleOut || '');
      button.setAttribute('data-toggle', 'tooltip');

      // Меняем action
      button.setAttribute('data-action', this.config.actionIn || `${this.config.moduleName}-toggle`);

      // Меняем текст
      if (this.config.textOut) button.innerHTML = this.config.textOut;
    }
  }

  /**
   * Обновляет счетчик общего количества
   * @param {number} total - Новое значение счетчика
   */
  updateTotalCount(total) {
    const elements = document.querySelectorAll(`.${this.config.moduleName}-total`);
    
    if (elements) {
      elements.forEach(element => {
        element.textContent = total;
      });
    }
  }

  /**
   * Отображение ошибок
   */
  showErrors(error) {
    if (error.option) {
      for (const i in error.option) {
        const inputOption = document.getElementById(`input-option${i}`);
        if (inputOption) {
          const errorSpan = document.createElement('span');
          errorSpan.className = 'error bg-danger';
          errorSpan.textContent = error.option[i];
          inputOption.parentNode.insertBefore(errorSpan, inputOption);
          
          if (window.innerWidth < 768) {
            window.scrollTo({
              top: errorSpan.offsetTop - 40,
              behavior: 'smooth'
            });
          }
        }
      }
    }
  }

}

