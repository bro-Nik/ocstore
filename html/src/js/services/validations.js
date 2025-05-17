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
export function validateNumberInput(input) {
  input.value = input.value.replace(/[^\d,]/g, '');
}

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
  console.log(cleanValue)
  if (!validated) {
    return 'Введите корректный номер телефона';
  }
}
