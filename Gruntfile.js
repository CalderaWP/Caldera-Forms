module.exports = function (grunt) {


    // Project configuration.
    grunt.initConfig({
        pkg     : grunt.file.readJSON( 'package.json' ),

        replace: {
            core: {
                src: [ 'caldera-core.php' ],
                overwrite: true,
                replacements: [{
                    from: /Version:\s*(.*)/,
                    to: "Version: <%= pkg.version %>"
                }, {
                    from: /define\(\s*'CFCORE_VER',\s*'(.*)'\s*\);/,
                    to: "define( 'CFCORE_VER', '<%= pkg.version %>' );"
                }]
            },
            version_reamdme_txt: {
                src: [ 'readme.txt' ],
                overwrite: true,
                replacements: [{
                    from: /Stable tag: (.*)/,
                    to: "Stable tag: <%= pkg.version %>"
                }]

            },

        },
        uglify: {
            core: {
                files: [{
                    expand: true,
                    cwd: 'assets/js',
                    src: '*.js',
                    dest: 'assets/build/js/',
                    ext: '.min.js'
                }]
            },
        },
        cssmin: {
            core: {
                files: [{
                    expand: true,
                    cwd: 'assets/css',
                    src: ['*.css', '!*.min.css'],
                    dest: 'assets/build/css',
                    ext: '.min.css'
                }]
            }
        },
        concat: {
            options: {
                separator: ' ',
                banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
                '<%= grunt.template.today("yyyy-mm-dd") %> */',
            },
            front_css: {
                src: [
                    'assets/build/css/caldera-grid.min.css',
                    'assets/build/css/caldera-alert.min.css',
                    'assets/build/css/caldera-form.min.css'
                 ],
                dest: 'assets/build/css/caldera-forms-front.min.css'
            }
        },
        watch: {
            scripts: {
                files: ['assets/js/src/**.js'],
                tasks: ['default'],
                options: {
                    spawn: false
                }
            }
        },
        copy: {
            fonts: {
                expand: true,
                cwd: 'assets/css/fonts/',
                src: '*',
                dest: 'assets/build/css/fonts/',
                flatten: true,
                filter: 'isFile'
            },
            i18n: {
                expand: true,
                cwd: 'assets/js/i18n/',
                src: '*',
                dest: 'assets/build/js/i18n/',
                flatten: true,
                filter: 'isFile'
            },
            images: {
                expand: true,
                cwd: 'assets/images',
                src: '*',
                dest: 'assets/build/images',
                flatten: true,
                filter: 'isFile'
            }
        }

    });

    //load modules
    grunt.loadNpmTasks( 'grunt-text-replace' );
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    //register default task
    grunt.registerTask( 'default',  [
        'uglify',
        'cssmin',
        'concat',
        'copy'
    ] );
    grunt.registerTask( 'version_number', [ 'replace' ] );


};
