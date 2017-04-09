let gulp = require('gulp');
let minify = require('gulp-minify');
let webpack = require('gulp-webpack');
let concat = require( 'gulp-concat' );
let  watch = require('gulp-watch');

const formEditor =  'assets/js/form-editor/*.js';

const vue = [
        'assets/js/vue/vue.js',
        'assets/js/vue/vue-filters.js'
    ];


const viewer = [
        './assets/js/api/*.js',
        './assets/js/viewer/*.js'
];

const front = [
    './assets/js/fields.js',
    './assets/js/field-config.js',
    './assets/js/frontend-script-init.js',
];

const minOpts = {
    ext: '.min.js',
    noSource: true,
    mangle: true,
    compress: true
};

const buildDest = 'assets/build/js';
const jsSrcDir = 'assets/js';


gulp.task('editor', function() {

    gulp.src(formEditor)
        .pipe(concat('form-builder.js'))
        .pipe(gulp.dest(jsSrcDir ));

    return;
    return gulp.src( formEditor )
        .pipe(webpack())
        .pipe(gulp.dest(jsSrcDir + '/form-builder.js'));
});

gulp.task( 'vue', function(){
    gulp.src(vue)
        .pipe(minify(minOpts))
        .pipe(gulp.dest(buildDest + '/vue'));
    gulp.src(vue)
        .pipe(concat('vue.js'))
        .pipe(gulp.dest(jsSrcDir ));
});

gulp.task( 'viewer', function(){
    gulp.src(vue)
        .pipe(minify(viewer))
        .pipe(gulp.dest(buildDest + '/viewer'));
    gulp.src(vue)
        .pipe(concat('entry-viewer-2.js'))
        .pipe(gulp.dest(jsSrcDir ));
});


gulp.task( 'formFront', function(){
    gulp.src(vue)
        .pipe(minify(viewer))
        .pipe(gulp.dest(buildDest + '/viewer'));
    gulp.src(vue)
        .pipe(concat('caldera-forms-front.js'))
        .pipe(gulp.dest(jsSrcDir ));
});

gulp.task('preBuildJS', [ 'vue', 'viewer', 'formFront']);

gulp.task('buildJS', function () {
    gulp.src('assets/js/*.js')
        .pipe(minify(minOpts))
        .pipe(gulp.dest(buildDest))
});


gulp.task('js', [ 'preBuildJS', 'buildJS' ]);

gulp.task( 'default', [ 'js' ] );

gulp.task('watch', function(){
    gulp.watch('assets/*.js', ['js']);
    gulp.watch( vue, [ 'vue' ]  );
    gulp.watch( formEditor, ['editor'] );
    gulp.watch( front, ['front' ])
});
