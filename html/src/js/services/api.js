/**
 * Отправка FormData на сервер
 * @param {string} url - Эндпоинт
 * @param {FormData} formData - Данные формы
 * @returns {Promise}
 */
export const postFormData = (url, formData) => {
  return fetch(url, {
    method: 'POST',
    body: new URLSearchParams(formData),
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    }
  }).then(response => {
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    return response.json();
  });
};
