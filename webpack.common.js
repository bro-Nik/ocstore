const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  entry: {
    main: ['./src/js/main.js', './src/scss/main.scss'],
    lazy: './src/scss/lazy.scss',
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
      filename: 'css/[name].css',
    }),
  ],
  optimization: {
    usedExports: true, // Включить tree-shaking
  },
};
