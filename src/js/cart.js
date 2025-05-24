/**
 * Модуль работы с корзиной (ванильная JS версия)
 * @module Cart
 */

import { ToggleModule } from './core/toggle';
import { cartPopup } from './modals/cart';
import { priceFormat, numberFormat } from './main';

const CONFIG = {
  moduleName: 'cart',
  textIn: 'Купить',
  titleIn: 'Добавить в корзину',
  textOut: 'В корзине',
  titleOut: 'Перейти в корзину',
  actionIn: 'open-popup-cart',
  endpoints: {
    toggle: 'index.php?route=checkout/cart/add',
    info: 'index.php?route=common/cart/info'
  },
  selectors: {
    removeButton: '.remove button',
    btns: [
      '.product-thumb.product_{product_id} .btn-cart',       // Кнопки в карточках товаров
      '.product-info .btn-cart',          // Кнопки на странице товара
      '.quickview .btn-cart.pjid_{product_id}'              // Кнопки в быстром просмотре
    ],
    forms: {
      catalog: `.products_category .product_{product_id} .options`,
      catalog_mod: `.products_category_mod .product_{product_id} .options`,
      product: `.product_informationss.product-info`,
      popup_product: `#popup-view-wrapper .product-info`,
      module: `#{block_id} .product_{product_id} .options`
    },
    cartItems: '#cart > ul', // Список товаров в корзине
  },
  globalEvents: {
    'update_prices_product': 'updatePricesProduct'
  }
};


class Cart extends ToggleModule {
  constructor() {
    super(CONFIG);
  }


  // bindEvents() {
  //   super.bindEvents();
  //   document.addEventListener('click', (e) => {
      // Обработка кликов для изменения цены
      // const updateBuyButton = e.target.closest('[data-action="update_options_buy"]');
      // if (updateBuyButton) {
      //   const productId = updateBuyButton.dataset.productId;
      //   const optionId = updateBuyButton.dataset.optionId;
      //   const option = updateBuyButton.dataset.option;
      //
      //   if (productId) this.updateOptionsBuy(productId, optionId, option);
      // }
      //
      //
  //
  //   });
  // };
  
  /**
   * Добавление товара в корзину
   */
  toggle(e, btn) {
    const productId = btn.dataset.productId;
    if (!productId) return;
    const module = btn.dataset.module || 'product';
    const quantity = btn.dataset.productMinimum || 1;
    const blockId = btn.dataset.blockId || null;
    
    const formElements = this.getFormElements(productId, module, blockId);
    const formData = this.buildFormData(formElements, productId, quantity);
    super.toggle(e, btn, formData)
  }

  /**
  * Получает селекторы формы в зависимости от типа действия.
  */
  getFormSelectors(module, productId, blockId) {
    switch (module) {
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
  getFormElements(productId, module, blockId) {
    const selectors = this.getFormSelectors(module, productId, blockId);

    // Получаем массив всех элементов формы для всех селекторов
    const formElements = selectors.flatMap(selector => {
      // Создаем массив селекторов для разных типов полей
      const fieldSelectors = [
        `input[type="text"]`,
        `input[type="hidden"]`,
        `input[type="radio"]:checked`,
        `input[type="checkbox"]:checked`,
        `select`,
        ...(module === "product" || module === "popup_product" ? [`textarea`] : [])
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
    // handleAddResponse
  handleToggleResponse(json, productId) {
    super.handleToggleResponse(json, productId);

    this.updateCart(json);
    cartPopup.show();
  }

  /**
   * Обновление данных корзины
   */
  updateCart(json) {
    const cartItems = document.querySelector(this.selectors.cartItems);
    this.loadHtml(this.config.endpoints.info, cartItems)
  }


  updatePricesProduct(e, btn) {
    const box = document.querySelector('.product-info.product_informationss');
    this.showLoading(box);

    var productId = 0;
    var quantity = 0;
    var element = null;

    const form = btn.closest('form')
    if (form) {
      // Форма быстрый заказ
      productId = form.querySelector('input[name="product_id"]').value;
      quantity = form.querySelector('input[name="quantity"]').value;
      element = document.querySelector('#popup-purchase');
    } else {
      // страница товара
      productId = btn.dataset.productId;
      quantity = btn.dataset.productMinimum || 1;
      element = box;
    }

    const formElements = element.querySelectorAll(`
      .product-info.product_informationss input[type="text"],
      .product-info.product_informationss input[type="hidden"],
      .product-info.product_informationss input[type="radio"]:checked,
      .product-info.product_informationss input[type="checkbox"]:checked,
      .product-info.product_informationss select,
      .product-info.product_informationss textarea
    `);
    
    const formData = new FormData();
    formElements.forEach(el => {
      if (el.name) {
        formData.append(el.name, el.value);
      }
    });
    
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    
    fetch('index.php?route=product/product/update_prices', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(json => {
      element.querySelectorAll('.product_informationss .pr_quantity').forEach(el => {
        el.textContent = numberFormat(json.option_quantity, product_id);
      });
      
      element.querySelectorAll('.product_informationss .pr_points').forEach(el => {
        el.textContent = number_Format(json.points, product_id);
      });
      
      element.querySelectorAll('.product_informationss .pr_model').forEach(el => {
        el.textContent = json.opt_model;
      });
      
      element.querySelectorAll('.update_price, .update_special').forEach(el => {
        el.textContent = priceFormat(json.special_n);
      });
      this.hideLoading(box);
    })
    .catch(error => console.error('Ошибка:', error));
  }

  // Функция для обновления опций при покупке в один клик
  updateOptionsBuy(product_id, opt_id, option) {
    const optionInput = document.querySelector(`.product-info.product_informationss .options_buy .pro_${option} input[name="option[${opt_id}]"]`);
    if (optionInput) {
      optionInput.value = option;
    }
    
    const formElements = document.querySelectorAll(`
      .product-info.product_informationss .options_buy .pro_${option} input[type="text"],
      .product-info.product_informationss .options_buy .pro_${option} input[type="hidden"],
      .product-info.product_informationss .options_buy .pro_${option} input[type="radio"]:checked,
      .product-info.product_informationss .options_buy .pro_${option} input[type="checkbox"]:checked,
      .product-info.product_informationss .options_buy .pro_${option} select,
      .product-info.product_informationss .options_buy .pro_${option} textarea`);
    
    const formData = new FormData();
    formElements.forEach(el => {
      if (el.name) {
        formData.append(el.name, el.value);
      }
    });
    
    formData.append('product_id', product_id);
    
    fetch('index.php?route=product/product/update_prices', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(json => {
      document.querySelectorAll('.product_informationss .pr_quantity').forEach(el => {
        el.textContent = number_format(json.option_quantity, product_id);
      });
      
      document.querySelectorAll('.product_informationss .pr_points').forEach(el => {
        el.textContent = number_format(json.points, product_id);
      });
      
      document.querySelectorAll('.product_informationss .pr_model').forEach(el => {
        el.textContent = json.opt_model;
      });
    })
    .catch(error => console.error('Ошибка:', error));
  }
}

export const cart = new Cart();
