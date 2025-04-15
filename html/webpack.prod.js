const { merge } = require('webpack-merge');
const common = require('./webpack.common.js');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

module.exports = merge(common, {
  mode: 'production',
  devtool: false,
  output: {
    filename: 'js/[name].[contenthash:8].min.js',
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: 'css/styles.[contenthash:8].min.css',
    }),
    new WebpackManifestPlugin({
      fileName: 'manifest.json',
      publicPath: 'catalog/view/theme/revolution/',
    }),
    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: ['catalog/view/theme/revolution/js/*', 'catalog/view/theme/revolution/css/*'],
    }),
  ],
  optimization: {
    minimize: true,
    minimizer: [
      new TerserPlugin(),
      new CssMinimizerPlugin(),
    ],
    splitChunks: {
      chunks: 'all',
    },
  },
});
