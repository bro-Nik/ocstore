// swiper
import { initCarouselSwipers } from './swiper';
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
import { ajaxSearch } from './search';
import { menu } from './menu';
import { getCookie, setCookie } from './cookie';

document.addEventListener('DOMContentLoaded', () => {

	// Записываем url
	const site_url = document.querySelector('input[name="site_url"]');
  if (site_url) site_url.value = window.location.href;

	pageViewCounter();
	dynamicBackground();
  initStars();
  cookieConsent();
  initScrollTop();
});


function initScrollTop() {
  // Скрол вверх
  const btn = document.querySelector('.btn-scroll-top');
  if (!btn) return;

  document.addEventListener('scroll', function() {
    btn.classList.toggle('show', window.scrollY > 100);
  });

  btn.addEventListener('click', function() {
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

// Обработка динамического фона
function dynamicBackground(container = document) {
  container.querySelectorAll('.article-cart img').forEach(img => {
    const color = getDominantColor(img);
    img.closest('.article-cart').style.background = color;
  });
}
function getDominantColor(imageElement) {
  const darkenFactor = 0.7;
  const canvas = document.createElement('canvas');
  const ctx = canvas.getContext('2d');
  const size = 10;
  
  canvas.width = size;
  canvas.height = size;
  ctx.drawImage(imageElement, 0, 0, size, size);
  
  const imageData = ctx.getImageData(0, 0, size, size).data;
  let r = 0, g = 0, b = 0;
  const count = size * size;
  
  for (let i = 0; i < imageData.length; i += 4) {
      r += imageData[i];
      g += imageData[i + 1];
      b += imageData[i + 2];
  }
  
  // Усредняем и затемняем
  r = Math.floor(r / count * darkenFactor);
  g = Math.floor(g / count * darkenFactor);
  b = Math.floor(b / count * darkenFactor);
  
  return `rgb(${r}, ${g}, ${b})`;
}


function cookieConsent() {
	if(!getCookie('cookie')) {
	  const cookieBlock = document.querySelector('.bottom_cookie_block');
		cookieBlock.style.display = 'block';

    document.querySelector('.bottom_cookie_block_ok').addEventListener('click', function() {
		  cookieBlock.style.display = 'none';
      setCookie('cookie', 'true', 30);
    });
	}
}

document.addEventListener('click', function(e) {
  const btn = e.target.closest('[data-toggle="tab"]');
  if (btn) {
    e.preventDefault();
    activateTab(btn);
  }

  if (e.target.matches('[data-action="scroll-to-reviews"]')) {
    scrollToReviews(e.target)
  }
});

function activateTab(btn) {
  const tabsBtns = btn.closest('.nav-tabs');
  const targetPane = document.querySelector(btn.hash);
  if (!targetPane) return;
  const tabsContent = targetPane.closest('.tab-content');

  tabsBtns.querySelectorAll('[data-toggle="tab"].active').forEach(el => {
    el.classList.remove('active');
  });
  
  tabsContent.querySelectorAll('.tab-pane.active').forEach(el => {
    el.classList.remove('active');
  });
  
  btn.classList.add('active');
  targetPane.classList.add('active');
}

function scrollToReviews(btn) {
  const container = btn.closest('dialog') || document;
  container.querySelector('[href="#tab-review"]').click();
  container.querySelector('#tab-review')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
