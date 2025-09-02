// swiper
import { initCarouselSwipers } from './swiper';
// initProductSwipers();
initCarouselSwipers();

// mobile-menu
import { initMobilMenu } from './mmenu-light';
initMobilMenu();

import validator from './services/validations';
import compare from './core/compare';
import wishlist from './core/wishlist';
import cart from './core/cart';
import events from './events/events';
import modals from './modals/init';
import lazyElements from './lazy_loader/init';
import review from './feedback/review';
import answer from './feedback/answer';
import uiHelpers from './ui-helpers';
import { pageViewCounter } from './statistic';
import { productPage } from './pages/product';

import { initStars } from './elements';
initStars();


document.addEventListener('DOMContentLoaded', () => {

  // Показываем первую вкладку
  const firstTab = document.querySelector('.nav.nav-tabs li:first-child a');
  if (firstTab) {
    firstTab.click();
  }

	// Записываем url
	const site_url = document.querySelector('input[name="site_url"]');
  if (site_url) {
		site_url.value = window.location.href;
  }

  prepareLogo();
	showContent();
	pageViewCounter();
});


	
function showContent() {
  document.documentElement.classList.add('visible');
  
  // Удаляем стиль после анимации (опционально)
  setTimeout(() => {
    const styleEl = document.getElementById('preload-css');
    if (styleEl) styleEl.remove();
  }, 300);
}
// Fallback на случай проблем
// setTimeout(showContent, 3000);

var h_top3 = $('#top3').outerHeight();
// $('.main-content').css('padding-top', h_top3+25);

if (!localStorage.getItem('display')) {
	localStorage.setItem('display', 'grid');
}

// Скрол вверх
document.addEventListener('scroll', function() {
  const scrollTopWrapper = document.querySelector('.btn-scroll-top');
  scrollTopWrapper.classList.toggle('show', window.scrollY > 100);
});

const scrollTopButton = document.querySelector('.btn-scroll-top');
if (scrollTopButton) {
  scrollTopButton.addEventListener('click', function() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
}

// Функция форматирования числа
export function numberFormat(n) {
  return parseInt(Math.abs(n).toFixed(0)) + ''; 
}


export function priceFormat(n) {
  const t = ' '; // разделитель тысяч
  const s = ' ₽'; // символ валюты
  
  const i = parseInt(Math.abs(n)) + ''; 
  const j = (i.length > 3) ? i.length % 3 : 0; 
  
  return (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + s;
}



// Перенос описания категории
const footer = document.querySelector('.footer-category');
const desc = document.querySelector('.page_description');
if (desc) footer?.appendChild(desc);


// Temp
document.addEventListener('DOMContentLoaded', function() {
  const descriptions = document.querySelectorAll('.page_description');
  
  descriptions.forEach(desc => {
    const content = desc.querySelector('.description');
    if (!content) return;

    // Создаем элементы
    const fade = document.createElement('div');
    fade.className = 'description-fade';
    
    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'toggle-btn';
    toggleBtn.type = 'button';
    
    // Добавляем элементы в DOM
    content.appendChild(fade);
    desc.appendChild(toggleBtn);
    
    // Проверяем высоту текста
    const fullHeight = content.scrollHeight;
    const visibleHeight = content.clientHeight;
    
    if (fullHeight > visibleHeight + 5) { // +5 для погрешности
      // Добавляем обработчик
      toggleBtn.addEventListener('click', function() {
        content.classList.toggle('expanded');
      });
    } else {
      // Если текст короткий - удаляем лишние элементы
      fade.remove();
      toggleBtn.remove();
      content.style.maxHeight = 'none';
    }
  });
});

function prepareLogo() {
  var logoLinks = document.querySelectorAll('.logo-link');

  logoLinks.forEach(logoLink => {
    if (logoLink && window.location.pathname === '/') {
      const logoImg = logoLink.querySelector('img');
      if (logoImg) logoLink.outerHTML = logoImg.outerHTML;
    }
  })

}
