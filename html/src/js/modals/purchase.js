/**
 * Модуль попапа оформления заказа
 * @module PurchasePopup
 */

// import { BaseCartPopup } from './cart-base.js';
import { BasePopup } from './base.js';

const SELECTORS = {
  POPUP_ID: '#popup-purchase',
  PURCHASE_FORM: '#purchase-form',
  PRODUCT_ID_INPUT: 'input[name="product_id"]',
};

const ENDPOINTS = {
  CONTENT: 'index.php?route=revolution/revpopuporder&revproduct_id=',
  MAKE_ORDER: 'index.php?route=revolution/revpopuporder/make_order',
  CART_STATUS: 'index.php?route=product/product/update_prices',
  UPDATE: 'index.php?route=product/product/update_prices',
};

const EVENTS = {
  'submit': 'handleCheckout',
  // 's': 'updateProductPrices'
};


// class PurchasePopup extends BaseCartPopup {
class PurchasePopup extends BasePopup {
  constructor() {
    super(SELECTORS, ENDPOINTS, {}, EVENTS);
    this.cache = {};
    this.init();
  }
  // init() {
  //   this.bindEvents();
  // }

  async show(product_id) {
    this.prepareBeforeOpen();
    this.showLoadingState(true);

    // if (this.cache[product_id]) {
    //   this.dialog.querySelector(this.selectors.POPUP_ID).innerHTML = this.cache[product_id];
    //   this.dialog.showModal();
    //   return;
    // }

    try {
      const response = await fetch(`${this.endpoints.CONTENT}${product_id}`);
      const html = await response.text();
      
      this.dialog.querySelector(this.selectors.POPUP_ID).innerHTML = html;

      this.initPopupHandlers();
      this.initEventHandlers();
      this.dialog.showModal();
      this.cache[product_id] = html;
    } finally {
      this.showLoadingState(false);
    }
  }

  initPopupHandlers() {
    //   console.log('initPopupHandlers')
    // const form = this.dialog.querySelector('#purchase-form');
    // if (form) {
    //   form.addEventListener('change', (e) => {
    //     e.preventDefault();
    //     console.log('change')
    //   })
    // }
    // this.dialog.addEventListener('click', (e) => {

    //   // Выбор опции
    //   const option = e.target.closest('.product-info input');
    //   if (option) {
    //     e.preventDefault();
    //     // this.handleQuantityChange(e)
    //     this.updateCartItem(0, 0);
    //     // const productId = quickOrderBtn.dataset.productId || '';
    //     // // if (productId) this.showQuickOrderPopup(productId);
    //     // this.show(productId);
    //   }
    // });
    // });

    // this.dialog.addEventListener('change', (e) => {
    //     e.preventDefault();
    //   console.log('change')

      // Выбор опции
      // const option = e.target.closest('.product-info input');
      // if (option) {
      //   e.preventDefault();
      //   // this.handleQuantityChange(e)
      //   this.updateCartItem(0, 0);
      //   // const productId = quickOrderBtn.dataset.productId || '';
      //   // // if (productId) this.showQuickOrderPopup(productId);
      //   // this.show(productId);
      // }
    // });

  }

  bindEvents() {
    document.addEventListener('click', (e) => {
      // Обработка кликов по кнопкам быстрого заказа
      const quickOrderBtn = e.target.closest('.quick-order-btn');
      if (quickOrderBtn) {
        e.preventDefault();
        const productId = quickOrderBtn.dataset.productId || '';
        // if (productId) this.showQuickOrderPopup(productId);
        this.show(productId);
      }

      // Выбор опции
      // const option = e.target.closest('.product-info input');
      // if (option) {
      //   e.preventDefault();
      //   // this.handleQuantityChange(e)
      //   this.updateCartItem(0, 0);
      //   // const productId = quickOrderBtn.dataset.productId || '';
      //   // // if (productId) this.showQuickOrderPopup(productId);
      //   // this.show(productId);
      // }
    });
  }
  /**
  * Обновляет цены товара в попапе заказа
  * @param {number} quantity - Количество товара
  */

  // async updateCartItem(product_id, quantity) {
  //   const form = document.querySelector('#purchase-form');
  //   if (!form) return;
  //
  //   // Собираем данные формы
  //   const formData = new FormData();
  //   
  //   // Добавляем стандартные поля
  //   const fieldsToAdd = [
  //     'input[type="hidden"]',
  //     'input[type="text"]:not(.all_quantity)',
  //     'input[type="radio"]:checked',
  //     'input[type="checkbox"]:checked',
  //     'select',
  //     'textarea'
  //   ];
  //
  //   fieldsToAdd.forEach(selector => {
  //     form.querySelectorAll(selector).forEach(field => {
  //       if (field.name) {
  //         if (field.multiple) {
  //           Array.from(field.selectedOptions).forEach(option => {
  //             formData.append(`${field.name}[]`, option.value);
  //           });
  //         } else {
  //           formData.append(field.name, field.value);
  //         }
  //       }
  //     });
  //   });
  //
  //   // Добавляем количество
  //   formData.append('quantity', quantity);
  //
  //   try {
  //     const response = await fetch(this.endpoints.UPDATE, {
  //       method: 'POST',
  //       body: formData
  //     });
  //     
  //     const data = await response.json();
  //
  //     // Обновляем изображение если есть опции
  //     if (data.opt_image_2) {
  //       const img = form.querySelector('.image img');
  //       if (img) {
  //         img.src = data.opt_image_2;
  //         img.style.width = '100px';
  //         img.style.height = '100px';
  //       }
  //     }
  //
  //     // Обновляем цены
  //     if (data.price_n) {
  //       const price = data.special_n || data.price_n;
  //       this.updatePriceDisplay(price, '#main-price');
  //
  //       if (data.special_n) {
  //         this.updatePriceDisplay(data.special_n, '#special-price');
  //       }
  //     }
  //   } catch (error) {
  //     console.error('Ошибка при обновлении цен:', error);
  //   }
  // }

  /**
  * Обновляет отображение цены с анимацией
  * @param {number} newPrice - Новая цена
  * @param {string} selector - Селектор элемента цены
  */
//   updatePriceDisplay(newPrice, selector) {
//     const priceElements = document.querySelectorAll(selector);
//     if (!priceElements.length) return;
//
//     const animate = true; // Можно вынести в конфиг
//     
//     priceElements.forEach(el => {
//       // el.textContent = this.priceFormat(newPrice);
//       el.textContent = this.priceFormat(newPrice);
//     });
//   }
//
//   /**
//   * Форматирование цены (аналог price_format из PHP)
//   */
//   priceFormat(price) {
//   // Преобразуем в число, если передана строка
//   const number = typeof price === 'string' ? parseFloat(price) : price;
//   
//   // Проверяем, является ли число целым
//   const isInteger = Number.isInteger(number);
//   
//   return new Intl.NumberFormat('ru-RU', {
//     style: 'currency',
//     currency: 'RUB',
//     minimumFractionDigits: isInteger ? 0 : 2,
//     maximumFractionDigits: isInteger ? 0 : 2
//   }).format(number); // Заменяем запятую на точку при необходимости
// }

  // Функция обновления цен продукта
  // update_prices_product(product_id, minimumvalue) {
  // console.log('this')
  //   const quantity = 1;
  //   const formElements = document.querySelectorAll('.product-info.product_informationss input[type="text"], .product-info.product_informationss input[type="hidden"], .product-info.product_informationss input[type="radio"]:checked, .product-info.product_informationss input[type="checkbox"]:checked, .product-info.product_informationss select, .product-info.product_informationss textarea');
  //   
  //   const formData = new FormData();
  //   formElements.forEach(el => {
  //     if (el.name) {
  //       formData.append(el.name, el.value);
  //     }
  //   });
  //   
  //   formData.append('product_id', product_id);
  //   formData.append('quantity', quantity);
  //   
  //   fetch('index.php?route=product/product/update_prices', {
  //     method: 'POST',
  //     body: formData
  //   })
  //   .then(response => response.json())
  //   .then(json => {
  //     document.querySelectorAll('.product_informationss .pr_quantity').forEach(el => {
  //       el.textContent = number_format(json.option_quantity, product_id);
  //     });
  //     
  //     document.querySelectorAll('.product_informationss .pr_points').forEach(el => {
  //       el.textContent = number_format(json.points, product_id);
  //     });
  //     
  //     document.querySelectorAll('.product_informationss .pr_model').forEach(el => {
  //       el.textContent = json.opt_model;
  //     });
  //     
  //     document.querySelectorAll('.product_informationss .update_price, .product_informationss .update_special').forEach(el => {
  //       el.textContent = price_format(json.special_n);
  //     });
  //   })
  //   .catch(error => console.error('Ошибка:', error));
  // }

/**
 * Валидирует и обновляет значение количества товара
 * @param {HTMLInputElement} inputElement - Элемент ввода количества
 * @param {string} action - Действие: '+', '-' или '='
 * @param {number} minimum - Минимальное количество
 * @param {boolean} quantityDependency - Флаг зависимости количества (q_zavisimost)
 */
// validateQuantityInput(inputElement, action, minimum, quantityDependency) {
//     // Очищаем ввод от всего кроме цифр и запятых
//     console.log(inputElement)
//     inputElement.value = inputElement.value.replace(/[^\d,]/g, '');
//     
//     // Получаем максимальное значение
//     const maxInput = document.querySelector('input.product_max');
//     let maximum = maxInput ? parseInt(maxInput.value) : 9999;
//     if (maximum < 1) maximum = 9999;
//     
//     // Основной элемент количества
//     const quantityInput = document.querySelector('input.all_quantity');
//     if (!quantityInput) return;
//     
//     let quantity = parseInt(quantityInput.value) || minimum;
//     
//     // Если поле пустое - устанавливаем минимальное значение
//     if (inputElement.value === '') {
//         inputElement.value = minimum;
//         quantity = minimum;
//     }
//     
//     // Обработка действий в зависимости от quantityDependency
//     if (quantityDependency) {
//         if (action === '+' && quantity < maximum) {
//             quantity += 1;
//         } else if (action === '-' && quantity > minimum) {
//             quantity -= 1;
//         } else if (action === '=' && quantity < maximum) {
//             // Ничего не меняем, только валидация
//         }
//         
//         // Проверка границ
//         if (quantity < 1 || quantity < minimum) {
//             quantity = minimum;
//         } else if (quantity > maximum) {
//             quantity = maximum;
//         }
//     } else {
//         if (action === '+') {
//             quantity += 1;
//         } else if (action === '-' && quantity > minimum) {
//             quantity -= 1;
//         }
//         
//         // Проверка только нижней границы
//         if (quantity < 1 || quantity < minimum) {
//             quantity = minimum;
//         }
//     }
//     
//     // Обновляем значения
//     quantityInput.value = quantity;
//     inputElement.value = quantity;
//     
//     // Обновляем цены
//     this.updateProductPrices(quantity); // Предполагается, что эта функция уже реализована
// }


}

export const purchasePopup = new PurchasePopup();
