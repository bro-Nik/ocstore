$(document).ready(function() {
  // Reorderable drag-and-drop lists
  $('tbody.sorting .input-group-btn').prepend('<span data-toggle="tooltip" title="" class="btn btn-success btn-sm handle"><i class="fa fa-hand-grab-o"></i></span>');
  // console.log($('tbody.sorting .input-group-btn')); // Проверка

  $('table tbody').sortable({
    handle: '.handle',
    chosenClass: 'handle-active',
    onEnd: function (evt) {
      var orderIndex = 1;
      $($(evt.item).parent().find('input[name*="sort_order"]')).each(function() {
        $(this).val(orderIndex);
        orderIndex++;
      });
    }
  });
});


document.addEventListener('DOMContentLoaded', () => {
  initImportJsonMetaData();
  initSummernoteCounter();
  initToggleSidebar();
});

// Импорт и экспорт мета данных JSON
function initImportJsonMetaData() {

  // Обработчики событий
  const importBtn = document.getElementById('json-import-btn');
  if (importBtn) {
    importBtn.addEventListener('click', function() {
      $('#jsonModal').modal('show');
    });
  }

  const exportBtn = document.getElementById('json-export-btn');
  if (exportBtn) {
    exportBtn.addEventListener('click', function() {
      exportJsonData();
    });
  }

  const importSubmitBtn = document.getElementById('importJsonBtn');
  if (importSubmitBtn) {
    importSubmitBtn.addEventListener('click', function() {
      importJsonData();
    });
  }
}
    
// Функция для импорта JSON данных
function importJsonData() {
  const jsonInput = document.getElementById('jsonInput').value;
  
  if (!jsonInput.trim()) {
    alert('Пожалуйста, введите JSON данные');
    return;
  }
  
  try {
    const data = JSON.parse(jsonInput);
    
    // Заполняем поля
    fillFields(data);
    
    // Закрываем модальное окно
    $('#jsonModal').modal('hide');
    
    // Показываем уведомление об успехе
    showNotification('Данные успешно импортированы!', 'success');

    // Инициализируем tooltips
    // if (typeof $().tooltip === 'function') {
    //   $('[data-toggle="tooltip"]').tooltip();
    // }
      
  } catch (error) {
    alert('Ошибка парсинга JSON: ' + error.message);
    console.error('JSON parse error:', error);
  }
}

// Функция заполнения полей
function fillFields(data) {
  const selectors = getSelectors();

  const fields = [
    { selector: selectors.h1, value: data.H1 || '' },
    { selector: selectors.title, value: data.Title || '' },
    { selector: selectors.descriprion, value: data.Description || '' },
  ];
    
  fields.forEach(field => {
    const element = document.querySelector(field.selector);
    // if (element) element.value = field.value;
    if (element) {
      const clone = element.cloneNode(true);
      clone.removeAttribute('id');
      clone.removeAttribute('name');
      clone.style.opacity = '0.7';
      clone.style.border = '2px dashed #ccc';

      clone.value = element.value;
      clone.disabled = true;

      element.parentNode.insertBefore(clone, element.nextSibling);

      element.value = field.value;
    }
  });

  importToSummernote(selectors.text, data.Text);
}

function importToSummernote(selector, text) {
  if (typeof $().summernote !== 'function') return;
  const summernoteElement = document.querySelector(selector);
  const oldContent = $(summernoteElement).summernote('code');

  const editorContainerParent = summernoteElement.parentNode;
  const editorContainer = editorContainerParent.querySelector('.note-editor');
  if (!editorContainer) return;

  const label = editorContainerParent.parentNode.querySelector('label');
  console.log(label)
  if (label) label.remove();

  editorContainerParent.style.cssText = `
      width: 100%;
      display: flex;
  `;

  // Меняем стили редактора - уменьшаем ширину до 50%
  editorContainer.style.cssText = `
      width: 50%;
      float: left;
      margin-right: 20px;
`;

  const editorContainerBox = editorContainer.querySelector('.note-editable');
  editorContainerBox.style.cssText = `
      height: unset;
`;
        
  // Создаем контейнер для старого контента
  const panelHeadingHeight = editorContainer.querySelector('.panel-heading').offsetHeight;
  const oldContentContainer = document.createElement('div');
  oldContentContainer.style.cssText = `
      width: calc(50% - 20px);
      height: 100%;
      float: left;
      padding: ${panelHeadingHeight}px 10px 10px 10px;
      background: #fff9e6;
      border: 1px solid #ffd166;
      border-radius: 8px;
      box-sizing: border-box;
  `;
  oldContentContainer.innerHTML = `
    <div style="line-height: 1.6;">${oldContent}</div>

    <div style="margin-top: 10px; font-size: 12px; color: #6c757d;">
      Символов: ${oldContent.length} | Слов: ${oldContent.split(/\s+/).filter(word => word.length > 0).length}
    </div>
  `;
        
  // Вставляем контейнер со старым контентом после редактора
  editorContainer.insertAdjacentElement('afterend', oldContentContainer);
  
  // Обновляем редактор с новым содержимым
  $(summernoteElement).summernote('code', text || '');
  
  // Добавляем очистку float после контейнеров
  const clearFix = document.createElement('div');
  clearFix.style.clear = 'both';
  editorContainer.after(clearFix);
}

function exportJsonData() {
  try {
    // Получаем данные из полей
    const jsonData = getFormData();
    const jsonString = JSON.stringify(jsonData, null, 2);
    
    // Копируем в буфер обмена
    copyToClipboard(jsonString);
    
    // Показываем уведомление об успехе
    showNotification('JSON данные скопированы в буфер обмена!', 'success');
    
  } catch (error) {
    alert('Ошибка при экспорте JSON: ' + error.message);
    console.error('JSON export error:', error);
  }
}

function getSelectors() {
  const content = document.getElementById('content');

  // Страница фильтра
  if (content && content.getAttribute('class') == 'ocf-page') {
    return { h1: '#input-heading-title-1',
             title: '#input-meta-title-1',
             descriprion: '#input-meta-description-1',
             text: '#input-description-bottom-1'};
  }

  // Стандартные страницы
    return { h1: '#input-meta-h11',
             title: '#input-meta-title1',
             descriprion: '#input-meta-description1',
             text: '#input-description1'};
}

function getFormData() {
  const selectors = getSelectors();

  // Получаем содержимое текстового редактора (если используется Summernote)
  let textContent = '';
  if (typeof $().summernote === 'function') {
    textContent = $(selectors.text).summernote('code');
  } else {
    // Альтернативный способ, если Summernote не используется
    const textElement = document.querySelector(selectors.text);
    if (textElement) textContent = textElement.value;
  }
  
  return {
    "H1": document.querySelector(selectors.h1)?.value || '',
    "Title": document.querySelector(selectors.title)?.value || '',
    "Description": document.querySelector(selectors.descriprion)?.value || '',
    "Text": textContent
  };
}

function copyToClipboard(text) {
  // Создаем временный textarea элемент
  const textarea = document.createElement('textarea');
  textarea.value = text;
  textarea.style.position = 'fixed';
  textarea.style.opacity = '0';
  
  document.body.appendChild(textarea);
  textarea.select();
  
  try {
    // Пытаемся использовать современный Clipboard API
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text);
    } else {
      // Fallback для старых браузеров
      document.execCommand('copy');
    }
  } catch (err) {
    console.error('Не удалось скопировать текст: ', err);
    throw new Error('Не удалось скопировать в буфер обмена');
  } finally {
    // Удаляем временный элемент
    document.body.removeChild(textarea);
  }
}
    
// Функция показа уведомлений
function showNotification(message, type = 'success') {
  // Создаем элемент уведомления
  const notification = document.createElement('div');
  notification.className = `alert alert-${type} alert-dismissible`;
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
  `;
  notification.innerHTML = `
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    ${message}
  `;
    
  document.body.appendChild(notification);
    
  // Автоматически скрываем через 3 секунды
  setTimeout(() => {
    if (notification.parentNode) {
      notification.parentNode.removeChild(notification);
    }
  }, 3000);
}

// Подсчет символов и слов в редакторе
function initSummernoteCounter() {
  $('[data-toggle="summernote"]').each(function() {
    summernotePrintCount(this);
  });

  $('[data-toggle="summernote"]').on('summernote.change', function() {
    summernotePrintCount(this);
  });
}
function summernotePrintCount(summernoteEl) {
  const content = $(summernoteEl).summernote('code');
  const cleanText = content.replace(/<[^>]*>/g, '').trim();
  const charCount = cleanText.length;
  const wordCount = cleanText.split(/\s+/).filter(word => word.length > 0).length;

  let counter = summernoteEl.parentNode.querySelector('.character-counter');
  if (!counter) {
    counter = document.createElement('div');
    counter.className = 'character-counter';
    counter.style.margin = '10px 0 0 10px';
    counter.style.fontSize = '12px';
    counter.style.color = '#6c757d';
  }
  counter.textContent = `Символов: ${charCount} | Слов: ${wordCount}`;

  const insertTo = summernoteEl.parentNode.querySelector('.note-editing-area');
  insertTo.insertAdjacentElement('afterend', counter);
}

function initToggleSidebar() {
  const isCollapsed = localStorage.getItem('leftColumnCollapsed') === 'true';
  const leftColumn = document.getElementById('column-left');
  
  if (isCollapsed) leftColumn.classList.add('collapsed');
}

function toggleSidebar() {
  const leftColumn = document.getElementById('column-left');
  leftColumn.classList.toggle('collapsed');
  
  // Сохраняем состояние в localStorage
  const isCollapsed = leftColumn.classList.contains('collapsed');
  localStorage.setItem('leftColumnCollapsed', isCollapsed);
}

