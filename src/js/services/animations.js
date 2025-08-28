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
    this.hiddenContent = this.container.querySelector('.hidden-content-box');
    this.switchableContent = this.container.querySelector('.switchable-content-box');

    if (!this.openBtn || !this.hiddenContent) return;

    this.input = this.hiddenContent.querySelector('input, textarea');

    // Инициализация начального состояния
    this.hiddenContent.style.display = 'none';
    this.hiddenContent.style.overflow = 'hidden';
    this.hiddenContent.style.height = '0';
    this.hiddenContent.style.transition = `height ${this.options.duration}ms ease`;
  }

  bindEvents() {
    if (!this.openBtn || !this.hiddenContent) return;

    this.openBtn.addEventListener('click', this.handleOpen.bind(this));
    
    if (this.closeBtn) {
      this.closeBtn.addEventListener('click', this.handleClose.bind(this));
    }
  }

  handleOpen(e) {
    if (e) e.preventDefault();

    // Скрываем кнопку открытия
    this.openBtn.style.display = 'none';

    // Скрываем переключаемый контент
    if (this.switchableContent) {
      this.slideUp(this.switchableContent, this.options.duration);
    }

    // Анимация открытия
    this.slideDown(this.hiddenContent, this.options.duration, () => {
      this.input?.focus();
      // Прокрутка к открытому блоку
      this.hiddenContent.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  }

  handleClose(e) {
    if (e) e.preventDefault();

    // Анимация закрытия
    this.slideUp(this.hiddenContent, this.options.duration);

    if (this.switchableContent) {
      this.slideDown(this.switchableContent, this.options.duration);
    }

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
    if (this.hiddenContent.style.display === 'none') {
      this.open();
    } else {
      this.close();
    }
  }
}
