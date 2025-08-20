export const LoaderMixin = {
  async loadHtml(url, container, collback) {
    try {
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
  }
};
