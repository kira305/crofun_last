let mix = require('laravel-mix');

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

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/test.scss', 'public/css')
   .sass('resources/assets/scss/table_master.scss', 'public/css')
   .sass('resources/assets/scss/customer_view.scss', 'public/css')
   .sass('resources/assets/scss/customer_create.scss', 'public/css')
   .sass('resources/assets/scss/customer_edit.scss', 'public/css')
   .sass('resources/assets/scss/credit_search.scss', 'public/css');
