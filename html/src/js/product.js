const BASE_CONFIG = {};
const BASE_ENDPOINTS = {};
const BASE_SELECTORS = {};
const BASE_EVENT_HANDLERS = {};


class Product {
  constructor() {
    this.init();
  }

  init() {
    if (this.initialized) return;
    this.bindEvents();
    this.initialized = true;
  }

  bindEvents() {
    // document.addEventListener('click', (e) => {
      // Выбор опции
      // const option = e.target.closest('.product-info input');
      // if (option) {
      //   e.preventDefault();
      //   // this.handleQuantityChange(e)
      //   // this.updateCartItem(0, 0);
      //   // const productId = quickOrderBtn.dataset.productId || '';
      //   // // if (productId) this.showQuickOrderPopup(productId);
      //   // this.show(productId);
      //   this.update_prices_product();
      //   // this.validateQuantityInput(e.target);
    //   }
    // });

  };


}

export const productServices = new Product();
