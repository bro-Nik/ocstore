/**
 * Модуль работы с отображением товаров (ванильная JS версия)
 * @module ProductView
 */

import { BaseModule } from './core/base';

const CONFIG = {
  moduleName: 'productView',
  endpoints: {
    // Можно добавить эндпоинты, если нужно сохранять предпочтения на сервере
  },
  localStorageKey: 'productDisplayMode'
};

const SELECTORS = {
  buttons: {
    list: '#list-view',
    grid: '#grid-view',
    price: '#price-view'
  },
  content: '#content',
  productsCategory: '.products_category',
  productGrid: '.product-grid',
  productList: '.product-list',
  productPrice: '.product-price',
  clearfix: '.product-grid > .clearfix',
  cartButton: '.product-list .cart > a, .product-view .cart > a',
  productThumb: '.product-thumb',
  productLayout: '.product-layout',
  revSliderItem: '.rev_slider .item',
  descriptionOptions: '.description_options',
  columns: {
    left: '#column-left',
    right: '#column-right'
  }
};

const CLASSES = {
  listView: 'product-layout product-list col-xs-12',
  priceView: 'product-layout product-price col-xs-12',
  gridView: {
    twoColumns: 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12',
    oneColumn: 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12',
    default: 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12',
    mediumScreen: 'product-layout product-grid col-lg-4 col-md-4 col-sm-4 col-xs-6'
  },
  newLine: 'new_line',
  viewListOptions: 'view_list_options'
};

class ProductView extends BaseModule {
  constructor() {
    super(CONFIG, SELECTORS);
    this.init();
  }

  init() {
    if (this.initialized) return;
    this.bindEvents();
    this.setInitialView();
    this.initialized = true;
  }

  bindEvents() {
    // Обработчики для кнопок переключения вида
    document.addEventListener('click', (e) => {
      const listBtn = e.target.closest(this.selectors.buttons.list);
      const gridBtn = e.target.closest(this.selectors.buttons.grid);
      const priceBtn = e.target.closest(this.selectors.buttons.price);

      if (listBtn) {
        e.preventDefault();
        this.listView();
      } else if (gridBtn) {
        e.preventDefault();
        this.gridView();
      } else if (priceBtn) {
        e.preventDefault();
        this.priceView();
      }
    });

    // Обработчик изменения размера окна
    window.addEventListener('resize', () => {
      if (window.innerWidth < 767) {
        this.gridView();
      } else {
        this.adjustLayout();
      }
    });
  }

  setInitialView() {
    const savedView = localStorage.getItem(CONFIG.localStorageKey) || 'grid';
    
    switch(savedView) {
      case 'list':
        this.listView();
        break;
      case 'price':
        this.priceView();
        break;
      default:
        this.gridView();
    }
  }

  updateActiveButton(view) {
    Object.values(this.selectors.buttons).forEach(selector => {
      const btn = document.querySelector(selector);
      if (btn) btn.classList.remove('active');
    });

    const activeBtn = document.querySelector(this.selectors.buttons[view]);
    if (activeBtn) activeBtn.classList.add('active');
  }

  listView() {
    // Очистка clearfix элементов
    this.clearClearfix();

    // Установка классов для list view
    this.setViewClasses(CLASSES.listView);

    // Настройка кнопок корзины
    this.setupCartButtons();

    // Сброс высоты элементов
    this.resetHeights([
      '.product-list .product-thumb h4',
      '.product-list .product-thumb .product_buttons',
      '.product-list .product-thumb .description_options'
    ]);

    // Добавление специального класса для описания
    this.toggleDescriptionOptionsClass(true);

    // Обновление интерфейса
    this.updateActiveButton('list');
    localStorage.setItem(CONFIG.localStorageKey, 'list');
    this.dispatchEvent('viewChanged', { mode: 'list' });
  }

  gridView() {
    // Определение количества колонок
    const cols = this.getColumnCount();
    let gridClass = CLASSES.gridView.default;

    if (cols === 2) {
      gridClass = CLASSES.gridView.twoColumns;
    } else if (cols === 1) {
      gridClass = CLASSES.gridView.oneColumn;
    }

    // Адаптация для средних экранов
    if (window.innerWidth > 294 && window.innerWidth < 975) {
      gridClass = CLASSES.gridView.mediumScreen;
    }

    // Установка классов
    this.setViewClasses(gridClass);

    // Сброс стилей
    this.resetGridStyles();

    // Проверка ширины элементов
    this.checkElementWidths();

    // Выравнивание высоты элементов
    this.alignElementsHeight();

    // Обновление интерфейса
    this.updateActiveButton('grid');
    localStorage.setItem(CONFIG.localStorageKey, 'grid');
    this.dispatchEvent('viewChanged', { mode: 'grid' });
  }

  priceView() {
    // Очистка clearfix элементов
    this.clearClearfix();

    // Установка классов для price view
    this.setViewClasses(CLASSES.priceView);

    // Настройка кнопок корзины
    this.setupCartButtons();

    // Сброс высоты элементов
    this.resetHeights([
      '.product-price .product-thumb h4',
      '.product-price .product-thumb .product_buttons',
      '.product-price .product-thumb .description_options'
    ]);

    // Удаление специального класса для описания
    this.toggleDescriptionOptionsClass(false);

    // Обновление интерфейса
    this.updateActiveButton('price');
    localStorage.setItem(CONFIG.localStorageKey, 'price');
    this.dispatchEvent('viewChanged', { mode: 'price' });
  }

  // Вспомогательные методы
  clearClearfix() {
    document.querySelectorAll(this.selectors.clearfix).forEach(el => el.remove());
  }

  setViewClasses(className) {
    document.querySelectorAll(`${this.selectors.productList}, ${this.selectors.productPrice}, ${this.selectors.productGrid}`)
      .forEach(el => el.className = className);
  }

  setupCartButtons() {
    document.querySelectorAll(this.selectors.cartButton).forEach(el => {
      el.dataset.toggle = 'none';
      el.title = '';
    });
  }

  resetHeights(selectors) {
    selectors.forEach(selector => {
      document.querySelectorAll(selector).forEach(el => {
        el.style.height = 'initial';
      });
    });
  }

  toggleDescriptionOptionsClass(add) {
    const action = add ? 'add' : 'remove';
    document.querySelectorAll(`${this.selectors.descriptionOptions}`).forEach(el => {
      el.classList[action](CLASSES.viewListOptions);
    });
  }

  getColumnCount() {
    return document.querySelectorAll(`${this.selectors.columns.left}, ${this.selectors.columns.right}`).length;
  }

  resetGridStyles() {
    document.querySelectorAll('.product-grid .product-thumb .caption').forEach(el => {
      el.style.marginLeft = 'initial';
    });

    document.querySelectorAll('.product-grid .product-thumb .description_options').forEach(el => {
      el.classList.remove(CLASSES.viewListOptions);
    });
  }

  checkElementWidths() {
    const productGridWidth = document.querySelector(`${this.selectors.productLayout} ${this.selectors.productThumb}`)?.offsetWidth || 0;
    const productItemWidth = document.querySelector(`${this.selectors.revSliderItem} ${this.selectors.productThumb}`)?.offsetWidth || 0;

    const shouldAddNewLine = productGridWidth < 240 || productItemWidth < 240;

    document.querySelectorAll(`${this.selectors.productLayout}, ${this.selectors.revSliderItem}`).forEach(el => {
      el.classList.toggle(CLASSES.newLine, shouldAddNewLine);
    });
  }

  alignElementsHeight() {
    const elementsToAlign = [
      '.product-grid .product-thumb h4',
      '.product-grid .product-thumb .price',
      '.product-grid .product-thumb .product_buttons'
    ];

    elementsToAlign.forEach(selector => {
      this.alignHeight(selector);
    });

    setTimeout(() => {
      this.alignHeight('.product-grid .product-thumb .description_options');
    }, 300);
  }

  alignHeight(selector) {
    let maxHeight = 0;
    const elements = document.querySelectorAll(selector);
    
    elements.forEach(el => el.style.height = 'initial');
    elements.forEach(el => {
      const height = el.offsetHeight;
      if (height > maxHeight) maxHeight = height;
    });
    
    if (maxHeight > 0) {
      elements.forEach(el => el.style.height = `${maxHeight}px`);
    }
  }

  adjustLayout() {
    // Настройка affix для top3
    if (window.innerWidth > 768) {
      const topHeight = document.querySelector('#top')?.offsetHeight || 0;
      const top2Height = document.querySelector('#top2')?.offsetHeight || 0;
      const menu2Height = document.querySelector('html.common-home #menu2.inhome')?.offsetHeight || 0;
    }

    this.checkElementWidths();
    this.alignElementsHeight();
  }
}

export const productView = new ProductView();
//
// function list_view(){
// 	$('#content .products_category .product-grid > .clearfix').remove();
// 	$('#content .products_category .product-grid, #content .products_category .product-price').attr('class', 'product-layout product-list col-xs-12');
// 	$('#content .product-list .cart > a').attr('data-toggle', 'none');
// 	$('#content .product-list .cart > a').attr('title', '');
// 	$(document).ready(function() {
// 		var w_list_img = $('.product-list .product-thumb .image').outerWidth();
//
// 	});
// 	$('.product-list .product-thumb h4').css('height', 'initial');
// 	$('.product-list .product-thumb .product_buttons').css('height', 'initial');
// 	$('.product-list .product-thumb .caption').css('margin-left', 'px');
// 	$('.product-list .product-thumb .description_options').addClass('view_list_options');
// 	$('.product-list .product-thumb .description_options').css('height', 'initial');
// 	$('.product-layout.product-list').css('height', 'initial');
// 	$('#grid-view, #price-view').removeClass('active');
// 	$('#list-view').addClass('active');
// 	localStorage.setItem('display', 'list');
// }
// function grid_view(){
// 	var cols = $('#column-right, #column-left').length;
// 	if (cols == 2) {
// 		$('#content .product-list, #content .product-price').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
// 	} else if (cols == 1) {
// 		$('#content .product-list, #content .product-price').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
// 	} else {
// 		$('#content .product-list, #content .product-price').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
// 	}
// 	
//
// 		if ($(window).width() > 294 && $(window).width() < 975) {
// 			$('#content .product-layout.product-grid').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-4 col-xs-6');
// 		}
// 	
//
// 	$('.product-grid .product-thumb .caption').css('margin-left', 'initial');
// 	$('.product-grid .product-thumb .description_options').removeClass('view_list_options');
// 	var product_grid_width = $('.product-layout .product-thumb').outerWidth();
// 	var product_item_width = $('.rev_slider .item .product-thumb').outerWidth();
// 	if (product_grid_width < 240) {
// 		$('.product-layout').addClass('new_line');
// 		$('.rev_slider .item').addClass('new_line');
// 	} else {
// 		$('.product-layout').removeClass('new_line');
// 		$('.rev_slider .item').removeClass('new_line');
// 	}
// 	if (product_item_width < 240) {
// 		$('.rev_slider .item').addClass('new_line');
// 	} else {
// 		$('.rev_slider .item').removeClass('new_line');
// 	}
// 	
//
// 	max_height_div('.product-grid .product-thumb h4');
// 	max_height_div('.product-grid .product-thumb .price');
// 	max_height_div('.product-grid .product-thumb .product_buttons');
// 	
//
// 	setTimeout(function() {
// 		max_height_div('.product-grid .product-thumb .description_options');
// 	}, 300);
// 	
//
// 	$('#list-view, #price-view').removeClass('active');
// 	$('#grid-view').addClass('active');
// 	localStorage.setItem('display', 'grid');
// }
// function price_view(){
// 	$('#content .products_category .product-grid > .clearfix').remove();
// 	$('#content .products_category .product-list, #content .products_category .product-grid').attr('class', 'product-layout product-price col-xs-12');
// 	$('#content .product-view .cart > a').attr('data-toggle', 'none');
// 	$('#content .product-view .cart > a').attr('title', '');
// 	$('.product-price .product-thumb h4').css('height', 'initial');
// 	$('.product-price .product-thumb .caption').css('margin-left', 'initial');
// 	$('.product-price .product-thumb .product_buttons').css('height', 'initial');
// 	$('.product-price .product-thumb .description_options').removeClass('view_list_options');
// 	$('.product-price .product-thumb .description_options').css('height', 'initial');
// 	$('.product-layout.product-price').css('height', 'initial');
// 	$('#list-view, #grid-view').removeClass('active');
// 	$('#price-view').addClass('active');
// 	localStorage.setItem('display', 'price');
// }
// $('#list-view').click(function() {
// 	list_view();
// });
// $('#grid-view').click(function() {
// 	grid_view();
// });
// $('#price-view').click(function() {
// 	price_view();
// });
//
//
// 	if($(window).width() > 768) {
// 		$('#top3').affix({
// 			offset: {
// 					top: $('#top').outerHeight()+$('#top2').outerHeight()+$('html.common-home #menu2.inhome').outerHeight()
// 			}
// 		});
// 	}
//
// $(function() {
// 	if (localStorage.getItem('display') == 'list') {
// 		list_view();
// 	} else if (localStorage.getItem('display') == 'price') {
// 		price_view();
// 	} else if (localStorage.getItem('display') == 'grid') {
// 		grid_view();
// 	} else {
// 		
//
// 			grid_view();
// 		
//
// 	}
// 	
//
// 	podgon_fona();
// 	$(window).resize(podgon_fona);
// });
// function podgon_fona() {
// 	toggle_ellipses();
// 	var h_top5 = $('.inhome #menu2').outerHeight();
// 	if (h_top5) {
// 		$('#top5').css('min-height', h_top5+20);
// 	}
// 	
//
// 	var m2inh = $('html.common-home #menu2.inhome').outerHeight();
// 	$('html.common-home #menu2.inhome .podmenu2').css('height', m2inh);
// 	var m2inhw = $('html.common-home #menu2_button').outerWidth();
// 	$('html.common-home #menu2.inhome .podmenu2').css('min-width', m2inhw-0.5);
//
// 		var h_top3 = $('#top3').outerHeight();
//
// 		$('.main-content').css('padding-top', h_top3+25);
//
// 		$('#top3').addClass('absolutpo');
//
// 		if($(window).width() < 767) {
// 			grid_view();
// 		}
//
// 		if ($(window).width() > 294 && $(window).width() < 975) {
// 			$('#content .product-layout.product-grid').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-4 col-xs-6');
// 		}
//
// 	var product_grid_width = $('.product-layout .product-thumb').outerWidth();
// 	var product_item_width = $('.rev_slider .item .product-thumb').outerWidth();
// 	if (product_grid_width < 240) {
// 		$('.product-layout').addClass('new_line');
// 		$('.rev_slider .item').addClass('new_line');
// 	} else {
// 		$('.product-layout').removeClass('new_line');
// 		$('.rev_slider .item').removeClass('new_line');
// 	}
// 	if (product_item_width < 240) {
// 		$('.rev_slider .item').addClass('new_line');
// 	} else {
// 		$('.rev_slider .item').removeClass('new_line');
// 	}
// 	max_height_div('.product-grid .product-thumb h4');
// 	max_height_div('.product-grid .product-thumb .price');
// 	max_height_div('.product-grid .product-thumb .product_buttons');
// 	
//
// 	setTimeout(function() {
// 		max_height_div('.product-grid .product-thumb .description_options');
// 	}, 300);
// 	
//
// 	max_height_div('#content .refine_categories.clearfix a > span');
// }
