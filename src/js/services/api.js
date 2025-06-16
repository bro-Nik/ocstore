export class ApiService {
  async loadHtml(url, element) {
    if (element) {
      const response = await fetch(url);
      element.innerHTML = await response.text();
      return response;
    }
  }

  async loadJson(url) {
    const response = await fetch(url);
    return response.json();
  }

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
}

export const apiService = new ApiService();
