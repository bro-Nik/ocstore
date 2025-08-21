export const LoaderMixin = {
  async loadHtml(url, container, collback) {
    try {
      await new Promise(resolve => setTimeout(resolve, 300));

      const response = await fetch(url);
      
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      
      const html = await response.text();
      
      container.innerHTML = html;
      
      // Дополнительные методы после вставки
      if (collback) {
        setTimeout(() => {
          collback();
        }, 100);
      }
        
    } catch (error) {
        console.error('Error loading:', error);
    }
  },

  async getJson(url) {
    try {
      const response = await fetch(url);
      if (!response.ok) throw new Error('Network response was not ok');
      
      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        throw new Error('Invalid content type');
      }
      
      const data = await response.json();
      return data;
      throw new Error(data.error || 'Unknown error');
    } catch (error) {
      console.error('Error fetching session data:', error);
      return null;
    }
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
