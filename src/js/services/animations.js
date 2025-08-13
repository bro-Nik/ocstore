/**
 * Сервис для управления раскрывающимися блоками
 */

const BASE_CONFIG = {
  duration: 400,
};

export class ToggleBoxManager {
  constructor(container, config = {}) {
    this.config = { ...BASE_CONFIG, ...config };
    this.initialized = false;
    this.options = BASE_CONFIG;
    this.container = container;
    this.init();
  }
  init () {
    if (this.initialized) return;

    this.initElements();
    this.bindEvents();
    this.initialized = true;
  }

  initElements() {
    this.openBtn = this.container.querySelector('.open-box');
    this.closeBtn = this.container.querySelector('.close-box');
    this.contentBox = this.container.querySelector('.content-box');
    this.input = this.container.querySelector('input, textarea');

    if (!this.openBtn || !this.contentBox) return;

    // Инициализация начального состояния
    this.contentBox.style.display = 'none';
    this.contentBox.style.overflow = 'hidden';
    this.contentBox.style.height = '0';
    this.contentBox.style.transition = `height ${this.options.duration}ms ease`;
  }

  bindEvents() {
    if (!this.openBtn || !this.contentBox) return;

    this.openBtn.addEventListener('click', this.handleOpen.bind(this));
    
    if (this.closeBtn) {
      this.closeBtn.addEventListener('click', this.handleClose.bind(this));
    }
  }

  handleOpen(e) {
    if (e) e.preventDefault();

    // Скрываем кнопку открытия
    this.openBtn.style.display = 'none';

    // Анимация открытия
    this.slideDown(this.contentBox, this.options.duration, () => {
      this.input?.focus();
      // Прокрутка к открытому блоку
      this.contentBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  }

  handleClose(e) {
    if (e) e.preventDefault();

    // Анимация закрытия
    this.slideUp(this.contentBox, this.options.duration);

    // Показываем кнопку открытия
    this.openBtn.style.display = 'block';
  }

  // Вспомогательные методы анимации
  slideDown(element, duration, callback) {
    element.style.display = 'block';
    
    const targetHeight = element.scrollHeight + 'px';
    element.style.height = targetHeight;

    setTimeout(() => {
      if (callback) callback();
      element.style.height = 'auto';
    }, duration);
  }

  slideUp(element, duration, callback) {
    element.style.height = '0';
    element.style.display = 'none';
  }

  // Публичные методы для программного управления
  open() {
    this.handleOpen();
  }

  close() {
    this.handleClose();
  }

  toggle() {
    if (this.contentBox.style.display === 'none') {
      this.open();
    } else {
      this.close();
    }
  }
}
