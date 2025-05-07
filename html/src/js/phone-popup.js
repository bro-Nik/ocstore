/**
 * Модуль попапа телефона
 * @module PhonePopup
 */

const CONFIG = {
  popupRemovalDelay: 170,
  scrollbarAdjustment: 8.5,
  mobileMarginAdjustment: 17,
  fadeDuration: 70
};

const SELECTORS = {
  pageFader: '#pagefader2',
  absoluteElement: '#top3.absolutpo',
  mobileElement: '#top #cart_mobi',
  tooltips: '.tooltip',
  dropdownMenu: '.dropdown-menu.dop_contss',
  body: 'body'
};

class PhonePopup {
  constructor() {
    this.init();
  }

  init() {
    this.bindEvents();
  }

  bindEvents() {
    // Можно добавить обработчики событий, специфичные для попапа
  }

  /**
   * Показать попап телефона
   */
  show() {
    this.adjustLayout();
    this.hideTooltips();
    
    window.$.magnificPopup.open({
      removalDelay: CONFIG.popupRemovalDelay,
      callbacks: {
        beforeOpen: () => this.beforeOpen(),
        open: () => this.onOpen(),
        close: () => this.onClose()
      },
      tLoading: '',
      items: {
        src: 'index.php?route=revolution/revpopupphone',
        type: 'ajax'
      }
    });
  }

  /**
   * Корректировка макета при появлении скроллбара
   */
  adjustLayout() {
    const hasScrollbar = document.body.scrollHeight > document.body.clientHeight;
    if (hasScrollbar) {
      const absoluteElement = document.querySelector(SELECTORS.absoluteElement);
      if (absoluteElement) absoluteElement.style.right = `${CONFIG.scrollbarAdjustment}px`;
      
      if (window.innerWidth < 768) {
        const mobileElement = document.querySelector(SELECTORS.mobileElement);
        if (mobileElement) mobileElement.style.marginRight = `${CONFIG.mobileMarginAdjustment}px`;
      }
    }
  }

  /**
   * Скрыть все tooltips
   */
  hideTooltips() {
    document.querySelectorAll(SELECTORS.tooltips).forEach(tooltip => {
      tooltip.style.display = 'none';
    });
  }

  /**
   * Действия перед открытием попапа
   */
  beforeOpen() {
    // this.st.mainClass = 'mfp-zoom-in';
    const dropdownMenu = document.querySelector(SELECTORS.dropdownMenu);
    if (dropdownMenu) {
      dropdownMenu.style.transition = `opacity ${CONFIG.fadeDuration}ms`;
      dropdownMenu.style.opacity = '0';
      setTimeout(() => {
        dropdownMenu.style.display = 'none';
      }, CONFIG.fadeDuration);
    }
  }

  /**
   * Действия при открытии попапа
   */
  onOpen() {
    document.querySelector(SELECTORS.body).classList.add('razmiv2');
    const pageFader = document.querySelector(SELECTORS.pageFader);
    if (pageFader) {
      pageFader.style.display = 'block';
      pageFader.style.transition = `opacity ${CONFIG.fadeDuration}ms`;
      pageFader.style.opacity = '1';
    }
  }

  /**
   * Действия при закрытии попапа
   */
  onClose() {
    document.querySelector(SELECTORS.body).classList.remove('razmiv2');
    this.resetLayoutAdjustments();
    
    const pageFader = document.querySelector(SELECTORS.pageFader);
    if (pageFader) {
      pageFader.style.opacity = '0';
      setTimeout(() => {
        pageFader.style.display = 'none';
      }, CONFIG.fadeDuration);
    }

    const dropdownMenu = document.querySelector(SELECTORS.dropdownMenu);
    if (dropdownMenu) {
      dropdownMenu.style.display = '';
      dropdownMenu.style.opacity = '';
    }
  }

  /**
   * Сброс корректировок макета
   */
  resetLayoutAdjustments() {
    const absoluteElement = document.querySelector(SELECTORS.absoluteElement);
    if (absoluteElement) absoluteElement.style.right = '';
    
    if (window.innerWidth < 768) {
      const mobileElement = document.querySelector(SELECTORS.mobileElement);
      if (mobileElement) mobileElement.style.marginRight = '';
    }
  }
}

export { PhonePopup };
