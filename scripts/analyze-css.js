const { PurgeCSS } = require('purgecss');
const fs = require('fs');
const path = require('path');

async function removeUnusedCSS() {
  try {
    // const cssFile = './src/scss/stylesheet.css';
    // const cssFile = './src/scss/stylesheet_my.css';
    const cssFile = './src/scss/bootstrap.css';
    const cssContent = fs.readFileSync(cssFile, 'utf8');

    // Создаем backup на всякий случай
    const backupFile = cssFile + '.backup';
    fs.writeFileSync(backupFile, cssContent);
    console.log(`✅ Backup создан: ${backupFile}`);

    const purgeCSSResults = await new PurgeCSS().purge({
      content: [
        './src/**/*.js',
        // './catalog/**/*.twig',
        './catalog/view/theme/category/*.twig',
        './catalog/view/theme/common/**/*.twig',
        './catalog/view/theme/macro/**/*.twig',
        './catalog/view/theme/modals/**/*.twig',
        './catalog/view/theme/partials/**/*.twig',
        './catalog/view/theme/product/**/*.twig',
        './catalog/view/theme/revolution/**/*.twig',
      ],
      css: [
        {
          raw: cssContent,
          extension: 'css'
        }
      ],
      safelist: {
        standard: ['active', 'show', 'hidden', 'collapse', 'open', 'loading'],
        deep: [/modal/, /tooltip/, /dropdown/, /swiper/],
        greedy: [/swiper/, /mm-/]
      },
      // rejected: true // Не удаляет, только показывает что будет удалено
    });

    // Получаем очищенный CSS
    const cleanedCSS = purgeCSSResults[0].css;

    // Сохраняем очищенный CSS
    fs.writeFileSync(cssFile, cleanedCSS);

    // Для отчета сравним оригинал и результат
    const originalSize = cssContent.length;
    const newSize = cleanedCSS.length;
    const savedBytes = originalSize - newSize;
    const savedPercent = Math.round((savedBytes / originalSize) * 100);

    console.log(`✅ Удалено ${savedBytes} байт (${savedPercent}%)`);
    console.log(`✅ Очищенный файл сохранен: ${cssFile}`);

  } catch (error) {
    console.error('❌ Ошибка при удалении CSS:', error);
  }
}

removeUnusedCSS();
