let mix = require('laravel-mix');


mix.js('app/resources/js/app.js', 'public/js')
    .sass('app/resources/scss/app.scss', 'public/css');


if (mix.inProduction()) {
    //mix.version();
} else {
    mix.sourceMaps();
}
