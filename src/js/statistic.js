import { addToCookieList } from './cookie';

export async function pageViewCounter(container = document) {
  // Отправка данных для счетчика посещения страницы
  const counterEl = container.querySelector('#counter_data');
  if (!counterEl) return;

  const { type, id } = counterEl.dataset;
  addToCookieList('viewed', id, false, 10);
  // Используем navigator.sendBeacon для большей надежности
  if (navigator.sendBeacon) {
    // Проверяем, что это реальный пользователь
    if (!navigator.userAgent.includes('bot') && !navigator.webdriver && window.outerWidth > 100) {
      const data = new URLSearchParams();
      data.append('type', type);
      data.append('id', id);
      
      navigator.sendBeacon('index.php?route=api/statistic/pageViewCounter', data);
    }
  }
}
