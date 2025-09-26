import Swiper from 'swiper/core';
import { Navigation, Thumbs } from 'swiper/modules';

export function initProductSwipers(container = document) {
  const swiperEl = container.querySelector('.swiper');
  if (!swiperEl) return;

  const slidesCount = swiperEl.querySelectorAll('.swiper-slide').length;
  const isMobile = window.innerWidth <= 767;

  let productThumbsSwiper = null;
  if (!isMobile) {
    productThumbsSwiper = new Swiper(".product_thumbs_swiper", {
      loop: slidesCount > 4,
      spaceBetween: 10,
      slidesPerView: 4,
      freeMode: true,
      watchSlidesProgress: true,
    });
  }

  const productMainSwiper = new Swiper(".product_main_swiper", {
    modules: isMobile ? [Navigation] : [Navigation, Thumbs],
    loop: slidesCount > 1,
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
    let maxWidth = 0;

    // Находим максимальную ширину
    const slide = carouselEl.querySelector('.swiper-slide');
    if (slide) maxWidth = slide.offsetWidth;

    const oneSlide = maxWidth + 15 >= carouselEl.offsetWidth;
    // Базовые настройки для всех каруселей
    const defaultConfig = {
      slidesPerView: oneSlide ? 1 : 'auto',
      freeMode: true, // Для плавного скольжения
      spaceBetween: oneSlide ? 0 : 10,
      // spaceBetween: 10, // Отступ между слайдами
      // slidesOffsetBefore: 10,
      // slidesOffsetAfter: 10,
      // slidesOffsetBefore/After
    };
    
    // Индивидуальные настройки из data-атрибутов
    // const customConfig = {
    //   autoplay: carouselEl.dataset.autoplay ? {
    //     delay: parseInt(carouselEl.dataset.autoplay),
    //     disableOnInteraction: false
    //   } : false,
    //   slidesPerView: carouselEl.dataset.slides ? parseInt(carouselEl.dataset.slides) : defaultConfig.slidesPerView
    // };

    // Индивидуальные настройки из json data-swiper-config
    // const jsonConfig = JSON.parse(carouselEl.dataset.swiperConfig  || '{}');
    
    // Инициализация Swiper
    swipers[`swiper${index}`] = new Swiper(carouselEl, { ...defaultConfig });
    // Инициализация Swiper с объединенными настройками
    // swipers[`swiper${index}`] = new Swiper(carouselEl, {
    //   ...defaultConfig,
    //   ...customConfig,
    //   ...jsonConfig
    // });
  });
  
  return swipers;
}
