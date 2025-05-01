import Swiper from 'swiper/core';
import { Navigation, Thumbs } from 'swiper/modules';

export function initProductSwipers() {
  var productThumbsSwiper = new Swiper(".product_thumbs_swiper", {
    loop: true,
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

export function initHomeSwipers() {
  // Находим и удаляем скелетон
  const skeleton = document.querySelector('.skeleton-slide');
  if (skeleton) skeleton.remove();

  const revSwiper = new Swiper('#revslideshow', {
    slidesPerView: 'auto',
    spaceBetween: 10,
    loop: true,
    // autoplay: {% if (autoscroll) %}{ 
    //   delay: {{ autoscroll }}*1000,
    //   disableOnInteraction: false,
    // }{% else %}false{% endif %},
    // breakpoints: {
    //   375: { slidesPerView: 1 },
    //   750: { slidesPerView: 2 },
    //   970: { slidesPerView: 4 },
    //   1170: { slidesPerView: 4 }
    // }
  });

  return { revSwiper };
}

export function initCarouselSwipers() {
  const swipers = {};
  
  // Проходим по всем каруселям на странице
  document.querySelectorAll('.swiper-carousel').forEach((carouselEl, index) => {
    // Удаляем скелетон для текущей карусели (если есть)
    const skeleton = carouselEl.querySelector('.skeleton-slide');
    if (skeleton) skeleton.remove();
    
    // Базовые настройки для всех каруселей
    const defaultConfig = {
      slidesPerView: 'auto', // Автоподбор количества видимых слайдов
      freeMode: true, // Для плавного скольжения
      spaceBetween: 10, // Отступ между слайдами
      // resistanceRatio: 15,
      // slidesOffsetBefore: 15,
      // slidesOffsetAfter: 15,
      // breakpoints: {
      //   // Дополнительные брейкпоинты при необходимости
      //   768: {
      //     slidesPerView: 'auto',
      //     // spaceBetween: 30
      //   }
      // }
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
