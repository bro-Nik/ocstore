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
