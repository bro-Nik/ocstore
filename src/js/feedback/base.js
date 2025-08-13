/**
 * Базовый модуль для работы с отзывами и вопросами-ответами
 * @module FeedbackBase
 */

import { BaseModule } from '../core/base';
import { LoadingManager } from '../services/loading';
import { ToggleBoxManager } from '../services/animations';
import { showFieldsValidation } from '../services/validations';

export class FeedbackBase extends BaseModule {
  constructor(config) {
    super(config);
  }

  init() {
    this.container = document.querySelector(this.selectors.container);
    if (!this.container) return;

    this.content = this.container.querySelector(this.selectors.content);
    if (!this.content) return;

    this.productId = this.content.dataset.productId;
    if (!this.productId) return;

    this.submitButton = this.container.querySelector('[type="submit"]');
    this.form = this.container.querySelector('form');

    this.loading = new LoadingManager(this.content);
    this.tiggleBox = new ToggleBoxManager(this.container);

    super.init();
  }

  bindEvents() {
    document.addEventListener('click', this.handleClick.bind(this));
  }

  handleClick(e) {
    // Обработчик загрузки контента
    const triggerBtn = e.target.closest(this.selectors.loadTrigger);
    if (triggerBtn && triggerBtn.dataset.count > 0 && this.container.querySelector('.review-list') == null) {
      this.loadContent(`${this.config.endpoints.load}${this.productId}`);
      return;
    }

    // Обработчик пагинации
    const paginationLink = e.target.closest(this.selectors.pagination);
    if (paginationLink && this.container) {
      e.preventDefault();
      this.loadContent(paginationLink.href);
      return;
    }

    // Обработчик отправки формы
    if (e.target == this.submitButton) {
      e.preventDefault();
      this.submitForm();
    }
  }
  async loadContent(url) {
    this.loading.show();
    try {
      await new Promise(resolve => setTimeout(resolve, 300));
      
      const response = await fetch(url);
      if (!response.ok) throw new Error('Network response was not ok');
      
      const html = await response.text();

      if (this.content) {
        this.content.innerHTML = html;
        // Повторно инициализируем контейнер после загрузки нового контента
        this.content = document.querySelector(this.selectors.content);
      }
      
    } catch (error) {
      console.error('Ошибка при загрузке контента:', error);
      this.notifications.show('Не удалось загрузить данные', 'error');
    }
    this.loading.hide();
  }

  async submitForm() {
    if (!this.form || !this.submitButton) return;

    const formLoading = new LoadingManager(this.form);
    try {
      formLoading.show();
      this.submitButton.disabled = true;
      const formData = new FormData(this.form);
      formData.append('product_id', this.productId);

      const response = await fetch(`${this.config.endpoints.write}${this.productId}`, {
        method: 'POST',
        body: new URLSearchParams(formData)
      });
      
      if (!response.ok) throw new Error('Network response was not ok');
      
      this.notifications.clear();
      const json = await response.json();

      if (json.validated_fields) {
        showFieldsValidation(json.validated_fields, this.form);
      }

      if (json.success) {
        this.notifications.show(json.success, 'success');
        this.resetForm();
        this.tiggleBox.close();
      }

    } catch (error) {
      console.error('Ошибка:', error);
      this.notifications.show('Произошла ошибка при отправке', 'error');
    } finally {
      this.submitButton.disabled = false;
      formLoading.hide();
    }
  }

  resetForm() {
    if (!this.form) return;
    
    this.form.reset();
    
    // Дополнительные сбросы для специфических элементов
    const stars = this.form.querySelectorAll('.stars .glyphicon');
    if (stars) {
      stars.forEach(star => {
        star.classList.remove('glyphicon-star');
        star.classList.add('glyphicon-star-empty');
      });
    }
    
    // Фокусировка на первом поле
    const firstInput = this.form.querySelector('input, textarea, select');
    if (firstInput) firstInput.focus();
  }
}
