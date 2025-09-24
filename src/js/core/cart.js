/**
 * Модуль работы с корзиной (ванильная JS версия)
 * @module Cart
 */

import { ToggleModule } from './toggle';
import { cartPopup } from '../modals/cart';
import { priceFormat, numberFormat } from '../main';
import { eventManager } from '../events/event-manager';
import { addToCartCookie, getCookie } from '../cookie';

const CONFIG = {
  moduleName: 'cart',
  textIn: 'Купить',
  textOut: 'В корзине',
  actionOut: 'open-popup-cart',
  ariaLabelIn: 'Добавить в корзину',
  ariaLabelOut: 'Открыть корзину',
  endpoints: {
    toggle: 'index.php?route=checkout/cart/add',
  },
  selectors: {
    removeButton: '.remove button',
    quantityInput: '#revcart_upd .plus-minus',
  },
  globalEvents: {
    'update_prices_product': 'updateProductPrice'
  }
};


class Cart extends ToggleModule {
  constructor() {
    super(CONFIG);
  }

  /**
   * Добавление товара в корзину
   */
  toggle(e, btn) {
    if (super.toggle(e, btn)) {
      cartPopup.show();
    }
  }

  addToCookieList(productId, quantity, options) {
    if (!options) {
      const infoBox = document.querySelector(`.product-card.main-product[data-product-id="${productId}"]`);
      const selectedOptions = infoBox?.querySelectorAll('input[type="checkbox"]:checked');
      if (selectedOptions) options = Array.from(selectedOptions).map(option => option.dataset.optionId);
    }
    return addToCartCookie(productId, quantity, options);
  }
}

export const cart = new Cart();
