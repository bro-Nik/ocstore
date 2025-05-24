export function createError(text) {
  const errorDiv = createElement('div', '', 'text-danger')
  errorDiv.textContent = text;
  return errorDiv
}

// Создание элементов
export const createElement = (tag, id = '', classes = '', attributes = {}) => {
  const el = document.createElement(tag);

  id = id.startsWith('#') ? id.slice(1) : id;
  if (id) el.id = id;

  classes = classes.replace('.', '')
  if (classes) el.className = classes;
  Object.entries(attributes).forEach(([key, value]) => el.setAttribute(key, value));
  return el;
};

// Добавление/удаление классов
export const toggleClass = (element, className, force) => {
  element.classList.toggle(className, force);
};
