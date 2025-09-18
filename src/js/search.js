import { LoaderMixin } from './mixins/loader';

class AjaxSearch {
  constructor(config) {
    this.initialized = false;
    this.init();
    Object.assign(this, LoaderMixin);
  }
  init() {
    if (this.initialized) return;

    this.bigsearch = document.querySelector('header .bigsearch');
    this.input = this.bigsearch.querySelector('input[name=\'search\']');
    if (!this.input) return;
    this.searchButton = this.bigsearch.querySelector('.search-button');
    this.createDropdown();
    this.bindEvents();

    this.initialized = true;
  }

  bindEvents() {
    // document.addEventListener('click', this.handleClick.bind(this));
    
    // Функция обработки ввода
    let timeoutId = null;
    this.input.addEventListener('input', (e) => {
      clearTimeout(timeoutId);
      timeoutId = setTimeout(() => {
        const request = this.input.value;
        const filterCategoryId = document.querySelector('.search input[name=\'category_id\']')?.value || '';
        
        if (request.length >= 3) this.fetchAutocompleteData(request, filterCategoryId);
        else this.dropdownMenu.style.display = 'none';
      }, 500);
    });

    // Клик по полю поиска
    this.input.addEventListener('click', (e) => {
      if (this.dropdownMenu.textContent.trim() === '') {
        this.input.dispatchEvent(new Event('input'));
      } else {
        this.dropdownMenu.style.display = 'block';
      }
    });

    // Клик по кнопке поиска
    this.searchButton.addEventListener('click', (e) => {
      let url = (document.querySelector('base')?.getAttribute('href') || '') + 'index.php?route=product/search';
		  if (this.input.value) url += '&search=' + encodeURIComponent(this.input.value);

		  location = url;
    });

    // Обработка клавиш
    this.input.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.dropdownMenu.style.display = 'none';
      }
      if (e.key === 'Enter') {
        this.searchButton.click();
      }
    });

    // Закрытие dropdown при клике вне его области
    document.addEventListener('click', (e) => {
      if (!this.input.contains(e.target) && !this.dropdownMenu.contains(e.target)) {
        this.dropdownMenu.style.display = 'none';
      }
    });
  }

  createDropdown() {
    this.dropdownMenu = document.createElement('ul');
    this.dropdownMenu.className = 'dropdown-menu';
    this.input.parentNode.appendChild(this.dropdownMenu);
  }

  // Функция для получения данных автозаполнения
  async fetchAutocompleteData(request, categoryId) {
    const url = `index.php?route=common/search/ajaxLiveSearch&filter_name=${encodeURIComponent(request)}&filter_category_id=${categoryId}`;
    const json = await this.getJson(url);
    this.processAutocompleteResponse(json);
  }

  // Обработка ответа от сервера
  processAutocompleteResponse(items) {
    if (!items || !items.length) {
      this.dropdownMenu.style.display = 'none';
      return;
    }

    let autocompleteItems = {};
    let html = '';

	  // Группируем элементы по типам
    const groupedItems = {
      category: [],
      manufacturer: [],
      product: []
    };

    items.forEach(item => {
      if (item.type === 'category') {
        groupedItems.category.push(item);
      } else if (item.type === 'manufacturer') {
        groupedItems.manufacturer.push(item);
      } else {
        groupedItems.product.push(item);
      }
    });

	  // Генерируем HTML для каждой группы
    if (groupedItems.category.length > 0) {
      html += '<li class="group-header"><strong>Категории</strong></li>';
      groupedItems.category.forEach(item => {
        html += this.generateItemHtml(item);
      });
    }

	  if (groupedItems.manufacturer.length > 0) {
      html += '<li class="group-header"><strong>Производители</strong></li>';
      groupedItems.manufacturer.forEach(item => {
        html += this.generateItemHtml(item);
      });
    }

	  if (groupedItems.product.length > 0) {
      html += '<li class="group-header"><strong>Товары</strong></li>';
      groupedItems.product.forEach(item => {
        html += this.generateItemHtml(item);
      });
    }
      
    this.dropdownMenu.innerHTML = html;
    this.dropdownMenu.style.display = 'block';
  }

  generateItemHtml(item) {
    let itemHtml = '';
    if (item.product_id || true) {
      itemHtml += `<li><a href="${item.href}">`;
      itemHtml += '<div class="ajaxadvance">';
              
      if (item.image) {
        itemHtml += `<div class="image"><img title="${item.name}" src="${item.image}"/></div>`;
      }
                    
      itemHtml += '<div class="content">';
      itemHtml += `<div class="name">${item.name}</div>`;
                    
      if (item.price) {
        itemHtml += '<div class="price">';
        if (!item.special) {
          itemHtml += item.price;
        } else {
          itemHtml += `<span class="price-old" style="text-decoration: line-through; margin-right: 5px;">${item.price}</span> <span class="price-new" style="color: red;">${item.special}</span>`;
        }
        itemHtml += '</div>';
      }
                    
      itemHtml += '</div>';
      itemHtml += '</div></a></li>';
    }
    return itemHtml;
  }
}
    
export const ajaxSearch = new AjaxSearch();
