const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/alpinejs.cdn.min.js', 'public/js/alpinejs.cdn.min.js')
    .copy("resources/css/buttons.bootstrap5.css", "public/css/buttons.bootstrap5.css")
    .copy("resources/css/dataTables.bootstrap5.css", "public/css/dataTables.bootstrap5.css")
    .copy("resources/css/toastr.css", "public/css/toastr.css")
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);
