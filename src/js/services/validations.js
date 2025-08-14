import { createError, createElement } from './dom';


/**
  * Валидация email
  * @param {string} email - Email для проверки
  * @returns {boolean} - Валиден ли email
  */
export function validateEmailInput(input) {
  if (!input) return;
  const value = input.value
  if (!value && !input.required) return; // Пропускаем если поле пустое (если не обязательное)
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const validated = re.test(value)
  if (!validated) {
    return 'Введите корректный Email';
  }
}

/**
* Валидация телефона
* @param {element} input - Поле ввода телефона
*/
export function handleNumberInput(input) {
  input.value = input.value.replace(/[^\d,]/g, '');
}

export function handlePhoneInput(input) {

  // Удаляем все нецифровые символы
  input.value = input.value.replace(/[^\d,]/g, '');
  
  // Если номер начинается не с 7 или 8, добавляем +7
  if (!input.value.startsWith('7') && !input.value.startsWith('8')) {
    input.value = '7' + input.value;
  }
  
  // Форматируем номер
  let formattedValue = '+7';
  
  if (input.value.length > 1) {
    formattedValue += ' (' + input.value.substring(1, 4);
  }
  if (input.value.length > 3) {
    formattedValue += ') ' + input.value.substring(4, 7);
  }
  if (input.value.length > 7) {
    formattedValue += '-' + input.value.substring(7, 9);
  }
  if (input.value.length > 9) {
    formattedValue += '-' + input.value.substring(9, 11);
  }
  
  // Устанавливаем отформатированное значение
  input.value = formattedValue;
};

/**
* Валидация телефона
* @param {string} phone - Номер телефона для проверки
* @returns {boolean} - Валиден ли номер
*/
export function validatePhoneInput(input) {
  if (!input) return;
  const value = input.value.trim();
  if (!value && !input.required) return; // Пропускаем если поле пустое (если не обязательное)

  // Удаляем все нецифровые символы
  const cleanValue = value.replace(/\D/g, '');
  
  // строгая проверка (пример для российских номеров):
  // const re = /^(\+7|7|8)?[\s\-]?\(?[0-9]{3}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/;
  // const validated = re.test(value)
  const validated = cleanValue.length == 11
  if (!validated) {
    return 'Введите корректный номер телефона';
  }
}

export function validateForm(form) {
  let validList = Arrya();

  // Валидация телефона
  validList.push(validateFiels(form, 'input[name="telephone"]', validatePhoneInput));
  
  // Валидация email, если поле присутствует
  validList.push(validateFiels(form, 'input[name="email"]', validateEmailInput));

  return !validList.includes(false);
}

export function validateFiels(form, selector, validator) {
  const input = form.querySelector(selector);
  if (!input) return true;

  const error = validator(input);
  if (error) {
    showFieldError(input, error);
    return false;
  }
  return true;
}


const ERROR_ELEMENT_CLASS = 'error_style';
const ERROR_TEXT_CLASS = 'text-danger';
const SUCCES_ELEMENT_CLASS = 'succes_style';
const SUCCES_TEXT_CLASS = 'text-succes';
const TEXT_CLASS_ID = 'validation-text';
const ELEMENT_CLASS_ID = 'validation-element';

/**
  * Показать ошибку для конкретного поля
  * @param {HTMLElement} field - Поле ввода
  * @param {string} message - Текст ошибки
  */
export function showFieldError(field, message, scroll = true) {
  if (!field) return;
  if (window.getComputedStyle(field).display === 'none') {
    field = field.parentNode;
  }

  // Удаляем предыдущую ошибку
  const existingError = field.nextElementSibling;
  if (existingError && existingError.classList.contains(ERROR_TEXT_CLASS)) {
    existingError.remove();
  }
  
  field.classList.add(ERROR_ELEMENT_CLASS, ELEMENT_CLASS_ID);
  field.after(createError(message));
  
  // Прокручиваем к полю с ошибкой
  if (scroll) {
    field.scrollIntoView({ behavior: 'smooth', block: 'center' });
    field.focus();
  }
}


export function showFieldSucces(field, message) {
  if (!field) return;
  if (window.getComputedStyle(field).display === 'none') {
    field = field.parentNode;
  }
  
  field.classList.add(SUCCES_ELEMENT_CLASS, ELEMENT_CLASS_ID);
}


class Validator {
  constructor() {
    this.rules = {
      name: { min: 3, max: 25 },
      text: { min: 15, max: 3000 },
      rating: { min: 1, max: 5 },
      email: { min: 5, max: 96, pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ },
      phone: { min: 5, max: 32, pattern: /^\+?[\d\s\-\(\)]+$/ }
    };
    
    this.messages = {
      required: 'Поле обязательно для заполнения',
      len: 'Допустимая длина от {min} до {max} символов',
      pattern: 'Некорректный формат'
    };

    this.fields = [ 'telephone', 'email', 'name', 'text', 'rating', ];

    this.errorTextClass = 'validation-error-text';
    this.errorElementClass = 'validation-error-element';
    this.succesElementClass = 'validation-succes-element';
  }

  clearNotifications(form) {
    // Очистка сообщений
    form.querySelectorAll(`.${this.errorTextClass}`).forEach(e => e.remove());
    form.querySelectorAll(`.${this.errorElementClass}, .${this.succesElementClass}`).forEach(e => {
      e.classList.remove(this.errorElementClass, this.succesElementClass);
    });
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
        } else {
          this.showSucces(element)
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
  showSucces(element) {
    element = this.getAvailableElement(element);
    element.classList.add(this.succesElementClass);
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

    // Специальная проверка для radio-кнопок
    if (input.type === 'radio') {
      const isChecked = input.parentNode.querySelector(`[name="${name}"]:checked`);
      
      if (input.required && !isChecked) {
        return this.messages.required;
      }
    }
    
    // Проверка на обязательность
    if (input.required && !input.value.trim()) {
      return this.messages.required;
    }
    
    // Пропускаем необязательные пустые поля
    if (!input.required && !input.value.trim()) {
      return null;
    }
    
    const length = input.value.length;
    
    // Проверка длины
    if ((rules.min && length < rules.min) || (rules.max && length > rules.max)) {
      return this.messages.len.replace('{min}', rules.min).replace('{max}', rules.max);
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
}

export const validator = new Validator();
