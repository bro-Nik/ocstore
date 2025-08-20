import { LoaderMixin } from '../mixins/loader';
import { ObserverMixin } from '../mixins/observer';

const BASE_CONFIG = {
  selectors: {},
  endpoints: {},
};

class LazyLoaderBase {
  constructor(config = {}) {
    this.config = {
      ...BASE_CONFIG, ...config,
      selectors: { ...BASE_CONFIG.selectors, ...config.selectors },
      endpoints: { ...BASE_CONFIG.endpoints, ...config.endpoints },
    };

    this.selectors = this.config.selectors;
    this.endpoints = this.config.endpoints;
    this.container = document.getElementById(this.selectors.containerId);
    this.loaded = false;
    this.observer = null;

    Object.assign(this, LoaderMixin, ObserverMixin);
    
    if (this.container) {
      this.initObserver(this.container);
    }
  }


  observerHandler() {
    if (this.loaded) return;
    this.loadHtml(this.endpoints.content, this.container, () => {
      this.loaded = true;
      this.afterLoad();
    });
  }

  afterLoad() {
    // Метод для переопределения в дочерних классах
  }
}


export { LazyLoaderBase };
