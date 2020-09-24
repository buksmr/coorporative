const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix
  .options({
    postCss: [
      require('autoprefixer'),
    ],
  })
mix.js('resources/js/app.js', 'public/js')
	.sass('resources/sass/theme.scss', 'public/css');

mix.webpackConfig({ devtool: 'cheap-module-eval-sourcemap' });
mix.sourceMaps(false, 'source-map')
