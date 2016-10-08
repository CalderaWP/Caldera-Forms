'use strict';
var gulp = require('gulp'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    sass = require('gulp-sass'),
    minify = require('gulp-minify-css'),
    sourcemaps = require('gulp-sourcemaps'),
    watch = require('gulp-watch');

var adminJs = [
    'assets/js/admin.js',
    'assets/js/conditionals.js',
    'assets/js/fields.js',
    'assets/js/front-end-script-init.js',
    'assets/js/parsely.js',
    'assets/js/wp-baldrick-full.js'
];

var editorJs = [
    'assets/js/admin.js',
    'assets/js/edit.js',
    'assets/js/fields.js',
    'assets/js/wp-baldrick-full.js'
];

var frontEndJS = [

];

gulp.task('adminJs', function(){
    return gulp.src(adminJs)
        .pipe(concat({ path:'admin-scripts.min.js' }))
        .pipe(uglify())
        .pipe(gulp.dest('./assets/build/js'));
});

gulp.task('editorJs', function(){
    return gulp.src(editorJs)
        .pipe(concat({ path:'editor-scripts.min.js' }))
        .pipe(uglify())
        .pipe(gulp.dest('./assets/build/js'));
});


gulp.task('default', [ 'adminJs', 'editorJs'], function(){

    //gulp.watch( jsFileList, ['adminJs'] );
});