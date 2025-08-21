/**
 * Базовый модуль для работы с отзывами и вопросами-ответами
 * @module FeedbackBase
 */

import { BaseModule } from '../core/base';
import { LoadingManager } from '../services/loading';
import { ToggleBoxManager } from '../services/animations';
import { LoaderMixin } from '../mixins/loader';
import { FormMixin } from '../mixins/form';

export class FeedbackBase extends BaseModule {
  constructor(config) {
    super(config);
    Object.assign(this, LoaderMixin, FormMixin);
  }

  init(container = document) {
    this.container = container.querySelector(this.selectors.container);
    if (!this.container) return;

    this.content = this.container.querySelector(this.selectors.content);
    if (!this.content) return;

    this.productId = this.content.dataset.productId;
    if (!this.productId) return;

    this.form = this.container.querySelector('form');
    this.submitButton = this.form.querySelector('[type="submit"]');

    this.loading = new LoadingManager(this.content);
    this.toggleBox = new ToggleBoxManager(this.container);

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
    await this.loadHtml(url, this.content);
    this.loading.hide();
  }

  async submitForm() {
    const url = `${this.config.endpoints.write}${this.productId}`;
    const data = {
      product_id: this.productId,
    };
    this.submit(this.form, url, data);
  }

  async afterSucces() {
    this.resetForm();
    this.toggleBox.close();
  }
}
