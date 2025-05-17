/**
 * Модуль работы с корзиной (ванильная JS версия)
 * @module Cart
 */

import { BaseModule } from './core/base';
import { cartPopup } from './popups/cart';

const CONFIG = {
  moduleName: 'cart',
  endpoints: {
    add: 'index.php?route=checkout/cart/add',
    info: 'index.php?route=common/cart/info'
  },
};

const SELECTORS = {
  total: '.cart-total, #cart-total-popup', // Элементы с общей суммой
  btns: [
    '.product-thumb.product_{product_id} .add-to-cart',       // Кнопки в карточках товаров
    '.product-info .add-to-cart',          // Кнопки на странице товара
    '.quickview .add-to-cart.pjid_{product_id}'              // Кнопки в быстром просмотре
  ],
  forms: {
    catalog: `.products_category .product_{product_id} .options`,
    catalog_mod: `.products_category_mod .product_{product_id} .options`,
    product: `.product_informationss .product-info`,
    popup_product: `#popup-view-wrapper .product-info`,
    module: `#{block_id} .product_{product_id} .options`
  },
  cartItems: '#cart > ul', // Список товаров в корзине
  add: 'add-to-cart',
};

class Cart extends BaseModule {
  constructor() {
    super(CONFIG, SELECTORS);
    this.init();
  }

  init() {
    if (this.initialized) return;
    this.bindEvents();
    this.initialized = true;
  }

  bindEvents() {
    document.addEventListener('click', (e) => {
      // Обработка кликов по кнопкам с классом add-to-cart
      const addButton = e.target.closest(`.${this.selectors.add}`);
      if (addButton) {
        e.preventDefault();
        const productId = addButton.dataset.productId;
        const action = addButton.dataset.action || 'product';
        const quantity = addButton.dataset.quantity || 1;
        const blockId = addButton.dataset.blockId || null;

        if (productId) this.add(productId, action, quantity, blockId, addButton);
      }
    });
  }

  /**
   * Добавление товара в корзину
   */
  add(productId, action, quantity = 1, blockId, btn) {
    this.showLoadingState(btn, true);
    
    const formElements = this.getFormElements(productId, action, blockId);
    const formData = this.buildFormData(formElements, productId, quantity);
    
    this.sendFormData(this.config.endpoints.add, formData)
      .then(json => this.handleAddResponse(json, productId))
      .catch(error => this.handleError(error))
      .finally(() => this.showLoadingState(btn, false));
  }

  /**
  * Получает селекторы формы в зависимости от типа действия.
  */
  getFormSelectors(action, productId, blockId) {
    switch (action) {
      case "catalog":
        return this.getSelectors(this.selectors.forms.catalog, { product_id: productId });
      case "catalog_mod":
        return this.getSelectors(this.selectors.forms.catalog_mod, { product_id: productId });
      case "product":
        return this.getSelectors(this.selectors.forms.product, { product_id: productId });
      case "popup_product":
        return this.getSelectors(this.selectors.forms.popup_product, { product_id: productId });
      case "module":
      case "module_in_product":
        return this.getSelectors(this.selectors.forms.module, { product_id: productId, block_id: blockId });
      default:
        return [];
    }
  }

  /**
   * Собирает все элементы формы по селекторам.
   */
  getFormElements(productId, action, blockId) {
    const selectors = this.getFormSelectors(action, productId, blockId);

    // Получаем массив всех элементов формы для всех селекторов
    const formElements = selectors.flatMap(selector => {
      // Создаем массив селекторов для разных типов полей
      const fieldSelectors = [
        `input[type="text"]`,
        `input[type="hidden"]`,
        `input[type="radio"]:checked`,
        `input[type="checkbox"]:checked`,
        `select`,
        ...(action === "product" || action === "popup_product" ? [`textarea`] : [])
      ];

      // Находим все элементы по этим селекторам
      //
      return fieldSelectors.flatMap(fieldSelector => 
          Array.from(document.querySelectorAll(`${selector} ${fieldSelector}`))
      ).filter(Boolean);
    }).filter(Boolean); // Дополнительная фильтрация на случай пустых значений
    return formElements;
  }

  /**
   * Сборка FormData из элементов формы
   */
  buildFormData(elements, productId, quantity) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    elements.forEach(element => {
      if (element.type === 'checkbox' || element.type === 'radio') {
        if (element.checked) formData.append(element.name, element.value);
      } else if (element.tagName === 'SELECT' && element.multiple) {
        Array.from(element.selectedOptions).forEach(option => {
          formData.append(`${element.name}[]`, option.value);
        });
      } else {
        formData.append(element.name, element.value);
      }
    });

    return formData;
  }

  /**
   * Обработка ответа после добавления в корзину
   */
  handleAddResponse(json, productId) {
    if (json.redirect) return window.location.assign(json.redirect);
    if (json.error) return this.showErrors(json.error);
    
    // Обновляем кнопки
    const updateParams = {
      baseClass: 'btn-cart',
      newClass: 'in-cart',
      newTitle: 'Перейти в корзину',
      classToRemove: this.selectors.add,
      newText: 'В корзине',
      // newUrl: 'index.php?route=checkout/cart'
    };
    this.updateButtons(productId, updateParams);

    this.updateCart(json);
    cartPopup.show();
  }

  /**
   * Обновление данных корзины
   */
  updateCart(json) {
    // Обновляем общую сумму
    this.updateTotalCount(json.total);
    
    // Обновляем список товаров
    fetch(this.config.endpoints.info + ' ul li')
      .then(response => response.text())
      .then(html => {
        const cartItems = document.querySelector(this.selectors.cartItems);
        if (cartItems) cartItems.innerHTML = html;
      });
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

export const cart = new Cart();
