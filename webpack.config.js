const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
var HtmlWebpackPlugin = require('html-webpack-plugin');

module.exports = {
  entry: './resources/js/app.js',
  output: {
    path: path.resolve(__dirname, 'public'),
    filename: 'js/app.js'
  },
  module: {
    rules: [

      {
        test: /\.vue$/,
        loader: 'vue-loader'
      },

      {
        test: /\.html$/i,
        loader: 'html-loader',
      },
      // this will apply to both plain `.js` files
      // AND `<script>` blocks in `.vue` files
      {
        test: /\.js$/,
        loader: 'babel-loader'
      },
      // this will apply to both plain `.css` files
      // AND `<style>` blocks in `.vue` files
      {
        test: /\.css$/,
        use: [
          'vue-style-loader',
          'css-loader'
        ]
      },

       {
  test: /\.(png|jpe?g|gif|svg)$/,
  use: [
    {
      loader: 'file-loader',
      options: {
        name: '[name].[contenthash].[ext]',
        outputPath: 'static/img',
        esModule: false // <- here
      }
    }
  ]
},
    {
      test: /\.scss$/,
      use: [
        MiniCssExtractPlugin.loader,
        {
          loader: 'css-loader'
        },
        {
          loader: 'sass-loader',
          options: {
            sourceMap: true,
            // options...
          }
        }
      ]
    }]
  },
  plugins: [
    new VueLoaderPlugin(),
    new HtmlWebpackPlugin(),
    new MiniCssExtractPlugin({
      filename: 'css/mystyles.css'
    }),
  ]
};