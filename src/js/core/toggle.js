import { BaseModule } from './base';
import { events } from '../events/events';
import { addToCookieList, getlistsOfProducts } from '../cookie';

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
    productIds.forEach(productId => {
      this.updateButtons(productId, container);
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
    const productId = btn.closest('[data-product-id]')?.dataset.productId || 0;
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
  updateButtons(productId, container = document) {
    let btns = [];
    if (productId === undefined) btns = container.querySelectorAll(`.in-${this.config.moduleName}`);
    else btns = container.querySelectorAll(`.product-card[data-product-id="${productId}"] .${this.config.moduleName}`);

    btns?.forEach(btn => {
      this.updateButton(btn);
    });
  }

  updateButton(btn, forceState = false) {
    const { moduleName } = this.config;
    const inList = !btn.classList.contains(`in-${moduleName}`);
    const productName = btn.closest('[data-product-name]')?.dataset.productName || '';
    
    btn.classList.toggle(`in-${moduleName}`, inList);

    if (inList) {
      const { actionOut, textOut, ariaLabelOut  } = this.config;
      textOut && (btn.innerHTML = textOut);
      actionOut && btn.setAttribute('data-action', actionOut);
      ariaLabelOut && btn.setAttribute('aria-label', ariaLabelOut);
    } else {
      const { textIn, ariaLabelIn  } = this.config;
      textIn && (btn.innerHTML = textIn);
      ariaLabelIn && btn.setAttribute('aria-label', ariaLabelIn);
      btn.setAttribute('data-action', `${moduleName}-toggle`);
    }
    this.updateSvg(btn, inList);
  }

  updateSvg(btn, inList) {
    btn.querySelectorAll('.toggle')?.forEach(svg => {
      const svgInList = svg.classList.contains('in-list');
      if ((svgInList && inList) || (!svgInList && !inList)) {
        svg.style.display = 'block';
      } else  {
        svg.style.display = 'none';
      }
    })
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

