import { LoadingManager } from '../services/loading';
import { validator } from '../services/validations';
import { NotificationManager } from '../services/notifications';


export const FormMixin = {
  async submit(form, url, data = {}) {
    if (!form) return;

    const notifications = new NotificationManager('Forms');
    const formLoading = new LoadingManager(form);
    const submitButton = form.querySelector('[type=submit]');
    try {
      formLoading.show();

      if (submitButton) {
        submitButton.disabled = true;
      }

      if (!validator.validateForm(form)) return;
      validator.clearNotifications(form);

      const formData = new FormData(form);
      for (const item of Object.keys(data)) {
        formData.append(item, data[item]);
      }

      const response = await fetch(url, {
        method: 'POST',
        body: new URLSearchParams(formData)
      });
      
      if (!response.ok) throw new Error('Network response was not ok');
      
      notifications.clear();
      const json = await response.json();

      if (json.toasts) {
        notifications.show(json.toasts);
      }

      if (json.success) {
        notifications.show(json.success, 'success');
        this.afterSucces();
      }
      return json;

    } catch (error) {
      console.error('Ошибка:', error);
      notifications.show('Произошла ошибка при отправке', 'error');
    } finally {
      if (submitButton) {
        submitButton.disabled = false;
      }
      formLoading.hide();
    }
  },

  resetForm(form) {
    if (!form) return;
    
    form.reset();
    
    // Дополнительные сбросы для специфических элементов
    const stars = form.querySelectorAll('.stars .glyphicon');
    if (stars) {
      stars.forEach(star => {
        star.classList.remove('glyphicon-star');
        star.classList.add('glyphicon-star-empty');
      });
    }
    
    // Фокусировка на первом поле
    const firstInput = form.querySelector('input, textarea, select');
    if (firstInput) firstInput.focus();
  },

  async postFormData(url, formData) {
    // return fetch(url, {
    //   method: 'POST',
    //   body: formData
    // });
    //
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
  }
};
