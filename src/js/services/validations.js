import { createElement } from './dom';

class Validator {
  constructor() {
    this.rules = {
      name: { min: 3, max: 25 },
      text: { min: 15, max: 3000 },
      rating: { checked: true },
      email: { min: 5, max: 96, pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ },
      phone: { min: 18, max: 18, pattern: /^\+?[\d\s\-\(\)]+$/ },
      agree_privacy_policy: { checked: true },
    };
    
    this.messages = {
      required: 'Поле обязательно для заполнения',
      len: 'Допустимая длина от {min} до {max} символов',
      pattern: 'Некорректный формат',
      checked: 'Выберите значение',
      phone: 'Введите номер с кодом города',
      agree_privacy_policy: 'Для обработки нужно дать согласие'
    };

    this.fields = [ 'phone', 'email', 'name', 'text', 'rating', 'agree_privacy_policy'];

    this.errorTextClass = 'validation-error-text';
    this.errorElementClass = 'validation-error-element';

    this.init();
  }

  init() {
    if (this.initialized) return;
    this.bindEvents();
    this.initialized = true;
  }

  bindEvents() {
    // Обработчики для кнопок переключения вида
    document.addEventListener('input', (e) => {
      // Валидация числовых полей
      if (e.target.matches('input[name="number"]')) {
        this.realTimeCheckNumberInput(e.target);
      }
      // Валидация полей телефона
      if (e.target.matches('input[name="phone"]')) {
        this.realTimeCheckPhoneInput(e.target);
      }
    });
  }

  clearNotifications(form) {
    // Очистка сообщений
    form.querySelectorAll(`.${this.errorTextClass}`).forEach(e => e.remove());
    form.querySelectorAll(`.${this.errorElementClass}`).forEach(e => e.classList.remove(this.errorElementClass));
  }

  validateForm(form) {
    this.clearNotifications(form);

    var valid = true;
    this.fields.forEach(name => {
      const element = form.querySelector(`[name="${name}"]`);
      if (element) {
        const error = this.validateField(name, element);
        if (error) {
          this.showError(element, error)
          valid = false;
        }
      }
    });
    return valid;
  }

  showError(element, error) {
    element = this.getAvailableElement(element);
    element.classList.add(this.errorElementClass);
    element.after(this.createError(error));
  }

  getAvailableElement(element) {
    if (window.getComputedStyle(element).display === 'none') {
      element = this.getAvailableElement(element.parentNode);
    }
    return element;
  }

  validateField(name, input) {
    const rules = this.rules[name];
    if (!rules) return null;

    // Проверка чекбоксов
    if (rules.checked) {
      const checked = !!input.parentNode.querySelector(`[name="${name}"]:checked`);
      if (input.required && rules.checked != checked) {
        return this.messages?.[name] || this.messages.checked;
      }
    }
    
    // Проверка на обязательность
    if (input.required && !input.value.trim()) {
      return this.messages?.[name] || this.messages.required;
    }
    
    // Пропускаем необязательные пустые поля
    if (!input.required && !input.value.trim()) {
      return null;
    }
    
    // Проверка длины
    const length = input.value.length;
    if (rules.min && rules.max) {
      if (length < rules.min || length > rules.max) {
        return this.messages?.[name] || this.messages.len.replace('{min}', rules.min).replace('{max}', rules.max);
      }
    }
  
    // Проверка по регулярному выражению
    if (rules.pattern && !rules.pattern.test(input.value)) {
      return this.messages.pattern;
    }
    
    return null;
  }

  createError(text) {
    const errorDiv = createElement('div', '', this.errorTextClass)
    errorDiv.textContent = text;
    return errorDiv
  }

  realTimeCheckNumberInput(input) {
    input.value = input.value.replace(/[^\d,]/g, '');
  }

  realTimeCheckPhoneInput(input) {
    // Оставляем только цифры
    let digits = input.value.replace(/\D/g, '');
    
    // Добавляем 7 в начало, если номер не начинается с 7 или 8
    if (!/^[78]/.test(digits)) digits = '7' + digits;
    
    // Форматируем номер по маске +7 (XXX) XXX-XX-XX
    input.value = digits.replace(/^(\d)(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/, 
      (_, a, b, c, d, e) => 
        `+7${b ? ` (${b}` : ''}${c ? `) ${c}` : ''}${d ? `-${d}` : ''}${e ? `-${e}` : ''}`
    );
  };
}

export const validator = new Validator();
