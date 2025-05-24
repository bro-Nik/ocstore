/**
 * Модуль попапа оформления заказа
 * @module PurchasePopup
 */

// import { BaseCartPopup } from './cart-base.js';
import { BaseCartPopup } from './cart-base.js';
import { cart } from '../cart.js';

const CONFIG = {
  selectors: {
    popupId: '#popup-purchase',
  },
  endpoints: {
    content: 'index.php?route=revolution/revpopuporder&revproduct_id=',
    submit: 'index.php?route=revolution/revpopuporder/make_order',
    update: 'index.php?route=product/product/update_prices',
  },
  globalEvents: {
    'purchase': 'show'
  },
};

class PurchasePopup extends BaseCartPopup {
  constructor() {
    super(CONFIG);
  }

  async updateCartItem(productId, quantity) {
    cart.updatePricesProduct(productId, quantity, this.dialog)
  }

  show(e, btn) {
    const productId = btn.dataset.productId || '';
    const url = `${this.endpoints.content}${productId}`;
    super.show(url);
  }
}

export const purchasePopup = new PurchasePopup();
