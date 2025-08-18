export function initStars(container = document) {
  // Работа со свздами рейтинга
  const stars = container.querySelectorAll('.rat-star');
  
  stars.forEach(star => {
    // Hover эффект
    star.addEventListener('mouseover', () => {
      let prev = star;
      while (prev = prev.previousElementSibling) {
        if (prev.classList.contains('rat-star')) prev.classList.add('active');
      }
      star.classList.add('active');
    });
    
    star.addEventListener('mouseout', () => {
      stars.forEach(s => s.classList.remove('active'));
    });
    
    // Клик с выбором оценки
    star.addEventListener('click', () => {
      // Снимаем все отметки
      stars.forEach(s => s.classList.remove('checked'));
      
      // Отмечаем текущую и предыдущие звезды
      let current = star;
      while (current) {
        if (current.classList.contains('rat-star')) {
          current.classList.add('checked');
        }
        current = current.previousElementSibling;
      }
      
      // Отмечаем соответствующий input
      const inputId = star.getAttribute('for');
      if (inputId) {
        const input = document.getElementById(inputId);
        if (input) input.checked = true;
      }
    });
  });
}
