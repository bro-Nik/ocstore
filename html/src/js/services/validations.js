import { createError } from './dom';


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


// export function initFormValidation() {
//   document.addEventListener('click', function(e) {
//     const submitButton = e.target.closest('[data-action="submit"]');
//     if (!submitButton) return;
//
//     const form = submitButton.closest('form');
//     if (!form) return;
//     
//     // Если валидация не прошла, останавливаем дальнейшее выполнение
//     return validateForm(form)
//   }, true); // Используем capture phase для раннего перехвата события
// };

export function validateForm(form) {
  let isValid = true;

  // Валидация телефона
  const phoneInput = form.querySelector('input[name="telephone"]');
  const phoneError = validatePhoneInput(phoneInput);
  if (phoneError) {
    showFieldError(phoneInput, phoneError);
    isValid = false;
  }
  
  // Валидация email, если поле присутствует
  const emailInput = form.querySelector('input[name="email"]');
  const emailError = validateEmailInput(emailInput);
  if (emailError) {
    showFieldError(emailInput, emailError);
    isValid = false;
  }
  return isValid;
}



/**
  * Показать ошибку для конкретного поля
  * @param {HTMLElement} field - Поле ввода
  * @param {string} message - Текст ошибки
  */
function showFieldError(field, message) {
  // Удаляем предыдущую ошибку
  const existingError = field.nextElementSibling;
  if (existingError && existingError.classList.contains('text-danger')) {
    existingError.remove();
  }
  
  field.classList.add('error_style');
  field.after(createError(message));
  
  // Прокручиваем к полю с ошибкой
  field.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
