import Swiper from 'swiper/core';
import { Navigation, Thumbs } from 'swiper/modules';

export function initProductSwipers() {
  const swiperEl = document.querySelector('.swiper');
  if (!swiperEl) return;
  const slidesCount = swiperEl.querySelectorAll('.swiper-slide').length;

  var productThumbsSwiper = new Swiper(".product_thumbs_swiper", {
    loop: slidesCount > 4,
    spaceBetween: 10,
    slidesPerView: 4,
    freeMode: true,
    watchSlidesProgress: true,
  });

  var productMainSwiper = new Swiper(".product_main_swiper", {
    modules: [Navigation, Thumbs],
    loop: true,
    spaceBetween: 10,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    thumbs: {
      swiper: productThumbsSwiper,
    },
  });

  return { productThumbsSwiper, productMainSwiper };
}

export function initCarouselSwipers(container = document) {
  const swipers = {};
  
  // Проходим по всем каруселям на странице
  container.querySelectorAll('.swiper-carousel').forEach((carouselEl, index) => {
    
    // Базовые настройки для всех каруселей
    const defaultConfig = {
      slidesPerView: 'auto', // Автоподбор количества видимых слайдов
      freeMode: true, // Для плавного скольжения
      spaceBetween: 10, // Отступ между слайдами
    };
    
    // Индивидуальные настройки из data-атрибутов
    const customConfig = {
      autoplay: carouselEl.dataset.autoplay ? {
        delay: parseInt(carouselEl.dataset.autoplay),
        disableOnInteraction: false
      } : false,
      slidesPerView: carouselEl.dataset.slides ? parseInt(carouselEl.dataset.slides) : defaultConfig.slidesPerView
    };

    // Индивидуальные настройки из json data-swiper-config
    const jsonConfig = JSON.parse(carouselEl.dataset.swiperConfig  || '{}');
    
    // Инициализация Swiper с объединенными настройками
    swipers[`swiper${index}`] = new Swiper(carouselEl, {
      ...defaultConfig,
      ...customConfig,
      ...jsonConfig
    });
  });
  
  return swipers;
}
