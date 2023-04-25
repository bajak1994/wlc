const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const ESLintWebpackPlugin = require('eslint-webpack-plugin');
const StylelintWebpackPlugin = require('stylelint-webpack-plugin');

module.exports = {
  entry: {
    main: './src/js/index.js',
  },
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: '[name].js'
  },
  mode: 'production',
  devtool: 'source-map',
  watch: false,
  optimization: {
    minimizer: [
      new TerserPlugin()
    ]
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules|vendor/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        }
      },
      {
        test: /\.scss$/i,
        exclude: /node_modules|vendor/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          'sass-loader',
        ],
      },
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: '[name].css'
    }),
    new ESLintWebpackPlugin({
      files: 'src/js/*.js'
    }),
    new StylelintWebpackPlugin({
      files: 'src/scss/*.scss'
    })
  ],
  resolve: {
    alias: {
      '@styles': path.resolve(__dirname, 'src/scss/')
    }
  }
};
