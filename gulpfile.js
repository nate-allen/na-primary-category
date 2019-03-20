'use strict';

const gulp = require('gulp'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    minifycss = require('gulp-csso'),
    uglify = require('gulp-uglify-es').default,
    watch = require('gulp-watch'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    lineec = require('gulp-line-ending-corrector');

const AUTOPREFIXER_BROWSERS = [
    'last 2 version',
    '> 1%',
    'ie >= 9',
    'ie_mob >= 10',
    'ff >= 30',
    'chrome >= 34',
    'safari >= 7',
    'opera >= 23',
    'ios >= 7',
    'android >= 4',
    'bb >= 10'
];

/**
 * Concatenate JS files and minify them
 */
gulp.task( 'js', function() {
    return gulp.src(['./src/js/admin/*.js'])
        .pipe( concat('primary-category-admin.min.js') )
        .pipe( uglify() )
        .pipe( gulp.dest('./dist/js'));
});

/**
 * Run Sass processes and minify CSS file
 */
gulp.task( 'css', function() {
    return gulp.src(['./src/scss/admin/*.scss'])
        .pipe( sass({
            outputStyle: 'compressed'
        }).on('error', sass.logError))
        .pipe( autoprefixer( AUTOPREFIXER_BROWSERS ) )
        .pipe( minifycss() )
        .pipe( lineec() )
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe( gulp.dest('./dist/css') );
});

/**
 * Watch for file changes and run processes
 */
gulp.task( 'watch', gulp.series(gulp.parallel('css', 'js'), function() {
    gulp.watch('./src/scss/**/*.scss', gulp.series('css'));
    gulp.watch(['./src/js/admin/*.js','./src/js/frontend/*.js'], gulp.series('js'));
}));

/**
 * Default Gulp task
 */
gulp.task( 'default', gulp.series(gulp.parallel('css', 'js')));