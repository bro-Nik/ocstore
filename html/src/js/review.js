/**
 * Модуль работы с отзывами
 * @module Review
 */

import { BaseModule } from './core/base';

const CONFIG = {
  moduleName: 'review',
  endpoints: {
    load: 'index.php?route=product/product/review&product_id=',
    writeReview: 'index.php?route=product/product/write&product_id=',
    writeAnswer: 'index.php?route=revolution/revstorereview/writeanswer&product_id=',
  },
};

const SELECTORS = {
  reviewContainer: '#review',
  reviewForm: '.form-review',
  answerForm: '.form-answers',
  pagination: '.pagination a',
  reviewButton: '#button-review',
  answerButton: '#button-answers'
};

class Review extends BaseModule {
  constructor() {
    super(CONFIG, SELECTORS);
    this.productId = null;
    this.container = null;
    this.initialized = false;
    this.init();
  }

  init() {
    if (this.initialized) return;
    this.bindEvents();
    this.initialized = true;
  }

  bindEvents() {
    document.addEventListener('DOMContentLoaded', () => this.loadReviews());
    document.addEventListener('click', this.handleClick.bind(this));
  }

  handleClick(e) {
    // Обработчик пагинации отзывов
    const paginationLink = e.target.closest(this.selectors.pagination);
    if (paginationLink && this.container) {
      e.preventDefault();
      this.fadeOutAndLoad(paginationLink.href);
      return;
    }

    // Обработчик отправки отзыва
    if (e.target.closest(this.selectors.reviewButton)) {
      e.preventDefault();
      this.submitReview();
      return;
    }

    // Обработчик отправки ответа на отзыв
    if (e.target.closest(this.selectors.answerButton)) {
      e.preventDefault();
      this.submitAnswer();
    }
  }

  async fadeOutAndLoad(url) {
    try {
      this.container.style.opacity = '0';
      this.container.style.transition = 'opacity 0.3s ease';
      
      await new Promise(resolve => setTimeout(resolve, 300));
      await this.loadContent(url);
      
      this.container.style.opacity = '1';
    } catch (error) {
      console.error('Ошибка при загрузке контента:', error);
      this.container.style.opacity = '1';
      this.showNotification('alert-danger', 'Не удалось загрузить отзывы');
    }
  }

  async loadContent(url) {
    try {
      const response = await fetch(url);
      if (!response.ok) throw new Error('Network response was not ok');
      
      const html = await response.text();
      if (this.container) {
        this.container.innerHTML = html;
        // Повторно инициализируем контейнер после загрузки нового контента
        this.container = document.querySelector(this.selectors.reviewContainer);
      }
    } catch (error) {
      console.error('Ошибка загрузки:', error);
      throw error;
    }
  }

  loadReviews() {
    this.container = document.querySelector(this.selectors.reviewContainer);
    if (!this.container) return;
    
    this.productId = this.container.dataset.productId;
    const url = `${this.config.endpoints.load}${this.productId}`;
    this.loadContent(url);
  }

  async submitForm(formSelector, url) {
    const form = document.querySelector(formSelector);
    const button = document.querySelector(`${formSelector} [type="submit"]`);
    
    if (!form || !button) return;

    try {
      button.disabled = true;
      const formData = new FormData(form);
      formData.append('product_id', this.productId);

      const response = await fetch(url, {
        method: 'POST',
        body: new URLSearchParams(formData)
      });
      
      if (!response.ok) throw new Error('Network response was not ok');
      

      this.clearNotifications();
      const json = await response.json();

      if (json.error) {
        this.showNotification('alert-danger', json.error);
      }

      if (json.success) {
        this.showNotification('alert-success', json.success);
        this.resetForm(formSelector);
      }

    } catch (error) {
      console.error('Ошибка:', error);
      this.showNotification('alert-danger', 'Произошла ошибка при отправке');
    } finally {
      button.disabled = false;
    }
  }

  // Функция отправки отзыва
  async submitReview() {
    await this.submitForm(
      this.selectors.reviewForm,
      `${this.config.endpoints.writeReview}${this.productId}`
    );
  }

  // Функция отправки ответа на отзыв
  async submitAnswer() {
    await this.submitForm(
      this.selectors.answerForm,
      `${this.config.endpoints.writeAnswer}${this.productId}`
    );
  }

  clearNotifications() {
    document.querySelectorAll('.alert-success, .alert-danger').forEach(el => el.remove());
  }

  showNotification(className, message) {
    if (!this.container) return;
    
    const notification = document.createElement('div');
    notification.className = `alert ${className} alert-dismissible`;
    notification.innerHTML = `
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      ${message}
    `;
    
    this.container.insertBefore(notification, this.container.querySelector('form'));
    
    // Автоматическое скрытие через 5 секунд
    const timer = setTimeout(() => {
      notification.style.opacity = '0';
      notification.style.transition = 'opacity 0.5s ease';
      setTimeout(() => notification.remove(), 150);
    }, 5000);
    
    // Обработчик закрытия по кнопке
    notification.querySelector('.close').addEventListener('click', () => {
      clearTimeout(timer);
      notification.remove();
    });
  }

  resetForm(formSelector) {
    const form = document.querySelector(formSelector);
    if (!form) return;
    
    form.reset();
    
    // Дополнительные сбросы для специфических элементов
    const stars = form.querySelectorAll('.stars .glyphicon');
    stars.forEach(star => {
      star.classList.remove('glyphicon-star');
      star.classList.add('glyphicon-star-empty');
    });
    
    // Фокусировка на первом поле
    const firstInput = form.querySelector('input, textarea, select');
    if (firstInput) firstInput.focus();
  }
}

export const review = new Review();
