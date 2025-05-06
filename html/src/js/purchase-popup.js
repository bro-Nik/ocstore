/**
 * Модуль попапа оформления заказа
 * @module PurchasePopup
 */

const CONFIG = {
  popupRemovalDelay: 170,
  scrollbarAdjustment: 8.5,
  mobileMarginAdjustment: 17,
  fadeDuration: 70
};

const SELECTORS = {
  pageFader: '#pagefader2',
  absoluteCart: '#top3.absolutpo',
  mobileCart: '#top #cart_mobi',
  tooltip: '.tooltip'
};

export class PurchasePopup {
  constructor() {
    this.init();
  }

  init() {
    this.bindEvents();
  }

  bindEvents() {
    // Можно добавить обработчики событий
  }

  /**
   * Показать попап оформления заказа
   * @param {string} productId - ID товара
   */
  show(productId) {
    this.adjustLayout();
    this.hideTooltips();
    
    $.magnificPopup.open({
      removalDelay: CONFIG.popupRemovalDelay,
      callbacks: {
        beforeOpen: function() {
          this.st.mainClass = 'mfp-zoom-in';
        },
        open: () => {
          this.showLoadingState(true);
        },
        close: () => {
          this.showLoadingState(false);
          this.resetScrollAdjustments();
        }
      },
      tLoading: '',
      items: {
        src: `index.php?route=revolution/revpopuporder&revproduct_id=${productId}`,
        type: 'ajax'
      }
    });
  }

  /**
   * Корректировка макета при скроллбаре
   */
  adjustLayout() {
    const hasScrollbar = document.body.scrollHeight > document.body.clientHeight;
    if (hasScrollbar) {
      const absoluteCart = document.querySelector(SELECTORS.absoluteCart);
      if (absoluteCart) absoluteCart.style.right = `${CONFIG.scrollbarAdjustment}px`;
      
      if (window.innerWidth < 768) {
        const mobileCart = document.querySelector(SELECTORS.mobileCart);
        if (mobileCart) mobileCart.style.marginRight = `${CONFIG.mobileMarginAdjustment}px`;
      }
    }
  }

  /**
   * Сброс корректировок
   */
  resetScrollAdjustments() {
    const absoluteCart = document.querySelector(SELECTORS.absoluteCart);
    if (absoluteCart) absoluteCart.style.right = '';
    
    if (window.innerWidth < 768) {
      const mobileCart = document.querySelector(SELECTORS.mobileCart);
      if (mobileCart) mobileCart.style.marginRight = '';
    }
  }

  /**
   * Управление состоянием загрузки
   */
  showLoadingState(show) {
    const pageFader = document.querySelector(SELECTORS.pageFader);
    if (!pageFader) return;

    if (show) {
      pageFader.style.display = 'block';
      pageFader.style.transition = `opacity ${CONFIG.fadeDuration}ms`;
      pageFader.style.opacity = '1';
    } else {
      pageFader.style.opacity = '0';
      setTimeout(() => {
        pageFader.style.display = 'none';
      }, CONFIG.fadeDuration);
    }
  }

  /**
   * Скрыть подсказки
   */
  hideTooltips() {
    const tooltips = document.querySelectorAll(SELECTORS.tooltip);
    tooltips.forEach(tooltip => {
      tooltip.style.display = 'none';
    });
  }
}
