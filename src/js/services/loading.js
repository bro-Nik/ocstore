import { createElement } from '../services/dom';

/**
  * Показывает/скрывает состояние загрузки
  */
export class LoadingManager {
  constructor(targetElement) {
    this.target = targetElement;
  }
  
  show() {
    // Overlay (перекрывает весь контент)
    const maskedDiv = createElement('div', '', 'masked');

    // Индикатор загрузки (центрируется поверх overlay)
    const loadingDiv = createElement('div', '', 'loading');
    
    this.target.append(maskedDiv, loadingDiv);
  }
  
  hide() {
    // setTimeout(() => {
      const maskedElements = this.target.querySelectorAll('.masked, .loading');
      maskedElements.forEach(el => el.remove());
    // }, 1000);
  }
}
