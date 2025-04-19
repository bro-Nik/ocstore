/**
 * Анимация масштабирования элемента
 * @param {HTMLElement} element - DOM-элемент
 */

// Конфигурационные константы
const CONFIG = {
  fadeDuration: 200,
};

export const animateScale = (element) => {
  if (!element) return;

  element.style.transition = `transform ${CONFIG.fadeDuration}ms ease`;
  element.style.transform = 'scale(1.1)';

  setTimeout(() => {
    element.style.transform = 'scale(1)';
  }, CONFIG.fadeDuration);
};
