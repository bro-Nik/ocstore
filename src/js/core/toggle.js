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

const SESSION_CACHE_KEY = 'products_lists_session_cache';
const CACHE_EXPIRATION = 5 * 60 * 1000; // 5 минут в миллисекундах

export class ToggleModule extends BaseModule {

  constructor(config = {}) {
    super({ ...TOGGLE_CONFIG, ...config });
  }

  async init() {
    if (this.initialized) return;
    
    // Загружаем данные сессии при инициализации
    await this.loadSessionData();
    super.init();
  }

  async loadSessionData() {
    try {
      const sessionData = await this.getSessionData();
      if (!sessionData) return;

      // Обрабатываем данные для текущего модуля
      switch (this.config.moduleName) {
        case 'wishlist':
          this.markProducts(sessionData.wishlist || []);
          this.updateTotalCount(sessionData.wishlist.length || 0);
          break;
        case 'compare':
          this.markProducts(sessionData.compare || []);
          this.updateTotalCount(sessionData.compare.length || 0);
          break;
        case 'cart':
          this.markProducts(sessionData.cart || []);
          this.updateTotalCount(sessionData.cart.length || 0);
          break;
      }
    } catch (error) {
      console.error(`Error loading ${this.config.moduleName} session data:`, error);
    }
  }

  async getSessionData() {
    // Проверяем кэш
    const cached = localStorage.getItem(SESSION_CACHE_KEY);
    if (cached) {
      const { data, timestamp } = JSON.parse(cached);
      if (Date.now() - timestamp < CACHE_EXPIRATION) {
        return data;
      }
    }
    
    const data = await this.getJson('index.php?route=api/session&nocache=' + Date.now());
    if (data.success) {
      const cacheData = {
        data: data.data,
        timestamp: Date.now()
      };
      localStorage.setItem(SESSION_CACHE_KEY, JSON.stringify(cacheData));
      return data.data;
    }
  }

  async updateSessionData(moduleName, productId) {
    // Проверяем кэш
    const cached = localStorage.getItem(SESSION_CACHE_KEY);
    if (!cached) return;

    const { data, timestamp } = JSON.parse(cached);

    // Проверяем срок действия кэша
    if (Date.now() - timestamp > CACHE_EXPIRATION) return;

    const moduleData = data[moduleName] || [];
    const index = moduleData.indexOf(productId);
    if (index >= 0) {
      // Удаляем productId из массива
      moduleData.splice(index, 1);
    } else {
      // Добавляем productId в массив
      moduleData.push(productId);
    }
    data[moduleName] = moduleData;

    // Меняем кэш
    const cacheData = { data: data, timestamp: timestamp };
    localStorage.setItem(SESSION_CACHE_KEY, JSON.stringify(cacheData));
  }

  markProducts(productIds) {
    productIds.forEach(productId => {
      const selectors = this.getSelectors(this.selectors.btns, { product_id: productId });
      if (selectors) {
        selectors.forEach(selector => {
          document.querySelectorAll(selector).forEach(button => {
            if (!button.classList.contains(`in-${this.config.moduleName}`)) {
              this.updateButton(button);
            }
          });
        });
      }
    });
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

    // Обновляем кэш
    this.updateSessionData(this.config.moduleName, productId);
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

  updateButton(button, forceState = false) {
    const inListSelector = `in-${this.config.moduleName}`
    const inList = !button.classList.contains(inListSelector)

    if (inList) {
      button.classList.add(inListSelector);
      if (this.config.titleOut) button.setAttribute('title', this.config.titleOut);
      if (this.config.textOut) button.innerHTML = this.config.textOut;
      if (this.config.actionIn) button.setAttribute('data-action', this.config.actionIn);
    } else {
      button.classList.remove(inListSelector);
      if (this.config.titleIn) button.setAttribute('title', this.config.titleIn);
      if (this.config.textIn) button.innerHTML = this.config.textIn;
      button.setAttribute('data-action', `${this.config.moduleName}-toggle`);
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

