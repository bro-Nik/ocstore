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

  async quantityChange(e, btn) {
    const action = btn.closest(this.selectors.quantityPlus) ? 'increase' : 'decrease';
    const container = btn.closest(this.selectors.quantityContainer);
    const input = container.querySelector(this.selectors.quantityInput);
    const productId = container.querySelector(this.selectors.productIdInput).value;
    
    let quantity = parseInt(input.value);
    quantity = action === 'increase' ? quantity + 1 : quantity - 1;
    
    if (quantity < 1) quantity = 1;
    input.value = quantity;

    cart.updatePricesProduct(e, btn)
  }

  async quantityChangeManual(e, input) {
    input.value = input.value.replace(/[^\d]/g, '');
    const quantity = parseInt(input.value) || 1;
    const productKey = input.closest('tr, .mobile-products-cart > div')
                          .querySelector(this.selectors.productIdInput).value;
    
    if (input.value) await cart.updatePricesProduct(e, input);
  }

  show(e, btn) {
    const productId = btn.dataset.productId || '';
    const url = `${this.endpoints.content}${productId}`;
    super.show(url);
  }
}

export const purchasePopup = new PurchasePopup();
