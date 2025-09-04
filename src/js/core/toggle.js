import { BaseModule } from './base';
import { events } from '../events/events';
import { getCookie, addToCookieList, getlistsOfProducts } from '../cookie';

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

  init() {
    if (this.initialized) return;
    
    // Получаем списки
    const listsOfProducts = getlistsOfProducts();
    if (listsOfProducts) {
      // Обрабатываем данные для текущего модуля
      this.markProducts(listsOfProducts[this.config.moduleName] || []);
      this.updateTotalCount(listsOfProducts[this.config.moduleName].length || 0);
    }

    super.init();
  }

  markProducts(productIds, container = document) {
    productIds?.forEach(productId => {
      const selectors = this.getSelectors(this.selectors.btns, { product_id: productId });
      selectors?.forEach(selector => {
        container.querySelectorAll(selector).forEach(button => {
          if (!button.classList.contains(`in-${this.config.moduleName}`)) {
            this.updateButton(button);
          }
        });
      });
    });
  }

  bindEvents() {
    const eventName = `${this.config.moduleName}-toggle`;
    events.addHandlers({ [eventName]: 'toggle' }, document, this);
    super.bindEvents();
  }

  /**
   * Добавление и удаление товара
   */
  toggle(e, btn) {

    const { productId } = btn.dataset;
    if (!productId) return;

    // Добавляем в куки, обновляем количество
    const productsCount = this.addToCookieList(productId);
    this.updateTotalCount(productsCount);

    // Обновляем кнопки
    this.updateButtons(productId);

    // Генерируем событие
    // this.dispatchEvent('toggle', { productId: productId, data: json });
    return true;
  }

  addToCookieList(productId) {
    // В крзине этот метод переопределяется
    return addToCookieList(this.config.moduleName, productId);
  }

  /**
   * Обновляет состояние кнопок
   */
  updateButtons(productId) {
    let selectors = [];

    if (productId === undefined) selectors = [`.in-${this.config.moduleName}`];
    else selectors = this.getSelectors(this.selectors.btns, { product_id: productId });

    selectors?.forEach(selector => {
      document.querySelectorAll(selector).forEach(this.updateButton.bind(this))
    });
  }

  updateButton(button, forceState = false) {
    const { titleOut, textOut, actionIn, titleIn, textIn, moduleName } = this.config;
    const inList = !button.classList.contains(`in-${moduleName}`);
    
    button.classList.toggle(`in-${moduleName}`, inList);

    if (inList) {
      titleOut && button.setAttribute('title', titleOut);
      textOut && (button.innerHTML = textOut);
      actionIn && button.setAttribute('data-action', actionIn);
    } else {
      titleIn && button.setAttribute('title', titleIn);
      textIn && (button.innerHTML = textIn);
      button.setAttribute('data-action', `${moduleName}-toggle`);
    }
  }

  /**
   * Обновляет счетчик общего количества
   * @param {number} total - Новое значение счетчика
   */
  updateTotalCount(total) {
    const elements = document.querySelectorAll(`.${this.config.moduleName}-total`);
    if (!total || total < 1) total = '';
    else total = `<span>${total}</span>`;
    
    // elements.forEach(element => element.textContent = total);
    elements.forEach(element => element.innerHTML = total);
  }
}

