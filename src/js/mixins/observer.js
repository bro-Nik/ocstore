export const ObserverMixin = {
  initObserver(container) {
    if (!container) {
      console.error('Container not found for observer');
      return;
    }
    if (!this.observerHandler) {
      console.error('Not found observerHandler');
      return;
    }
    if (this.loadingCondition) {
      if (!this.loadingCondition()) return;
    }

    const placeholder = document.createElement('div');
    placeholder.className = 'lazy-placeholder';
    container.appendChild(placeholder);

    const options = {
      root: null,
      rootMargin: '200px',
      threshold: 0.1
    };
    
    this.observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          this.observerHandler();
          this.observer.unobserve(entry.target);
        }
      });
    }, options);
    
    this.observer.observe(placeholder);
  },

  disconnectObserver() {
    if (this.observer) {
      this.observer.disconnect();
    }
  }
};
