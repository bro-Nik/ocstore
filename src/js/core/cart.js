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
  titleIn: 'Добавить в корзину',
  textOut: 'В корзине',
  titleOut: 'Перейти в корзину',
  actionIn: 'open-popup-cart',
  endpoints: {
    toggle: 'index.php?route=checkout/cart/add',
  },
  selectors: {
    removeButton: '.remove button',
    quantityInput: '#revcart_upd .plus-minus',
    productIdInput: '#revcart_upd input[name="product_id"]',
    quantityPlus: '#revcart_upd .btn-plus button',
    quantityMinus: '#revcart_upd .btn-minus button',
    quantityContainer: '#revcart_upd .number',
    cartContent: '.rev_cart',
    btns: [
      '.btn-cart[data-product-id="{product_id}"]',
    ],
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
    return addToCartCookie(productId, quantity, options);
  }
}

export const cart = new Cart();
