const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  entry: {
    main: ['./src/js/main.js', './src/scss/main.scss'],
  },
  output: {
    filename: 'js/[name].js',
    path: path.resolve(__dirname, 'catalog/view/'),
  },
  resolve: {
    extensions: ['.js', '.mjs'],
  },
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader'],
      },
      {
        test: /\.m?js$/, // Обрабатывает и .js, и .mjs
        include: [
          path.resolve(__dirname, 'src/js'),
          path.resolve(__dirname, 'src/swiper'),
        ],
      },
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: 'css/styles.css',
    }),
  ],
  optimization: {
    usedExports: true, // Включить tree-shaking
    // splitChunks: {
    //   chunks: 'all',
    //   minSize: 10000, // Минимальный размер для выноса в отдельный файл
    //   cacheGroups: {
    //     swiper: {
    //       test: /[\\/]node_modules[\\/]swiper[\\/]/,
    //       name: 'swiper',
    //       chunks: 'all',
    //       enforce: true, // Важно для выноса Swiper отдельно
    //       priority: 10, // Высокий приоритет
    //     },
    //     mmenuLight: {
    //       test: /[\\/]html[\\/]src[\\/]mmenu-light[\\/]/,
    //       name: 'mmenu-light',
    //       chunks: 'all',
    //       enforce: true,
    //       priority: 10, // Можно установить такой же приоритет как у swiper
    //     },
    //   },
    // },
  },
};
