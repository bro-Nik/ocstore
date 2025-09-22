const { merge } = require('webpack-merge');
const common = require('./webpack.common.js');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
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
      filename: 'css/[name].[contenthash:8].min.css',
    }),
    new WebpackManifestPlugin({
      fileName: 'manifest.json',
      publicPath: 'catalog/view/',
    }),
    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: [
        'catalog/view/js/*.min.js',
        'catalog/view/css/*.min.css',
        'catalog/view/js/*.js.map',
        'catalog/view/css/*.css.map'
      ],
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
