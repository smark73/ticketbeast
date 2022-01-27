var gulp     = require('gulp');
const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');


/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
 */


// function styles() {
//     return gulp.src('./assets/sass/**/*.scss')
//         .pipe(sass({ outputStyle: 'expanded' }).on('error', sass.logError))
//         .pipe(autoprefixer({ remove: false }))
//         .pipe(gulp.dest('./assets/css'))
//         .pipe(bsync.stream());
// }


// gulp.task('watch', ['styles']);



elixir((mix) => {
    mix.sass('app.scss')
       .webpack('app.js');
});

