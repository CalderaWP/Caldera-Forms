module.exports = function (grunt) {
    files_list = [
        '**',
        '!.idea',
        '!.distignore',
        '!.git/**',
        '!ISSUE_TEMPLATE',
        '!bin/**',
        '!node_modules/**',
        '!build/**',
        '!sources/**',
        '!tests/**',
        '!vendor/**',
        '!.gitattributes',
        '!.gitignore',
        '!.gitmodules',
        '!.travis.yml',
        '!composer.lock',
        '!CONTRIBUTING.md',
        '!Gruntfile.js',
        '!package.json',
        '!phpunit.xml.dist'
    ];

    require( 'load-grunt-tasks' )( grunt );

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

            }
        },
        uglify: {
            core: {
                files: [{
                    sourceMap: true,
                    expand: true,
                    cwd: 'assets/js',
                    src: '*.js',
                    dest: 'assets/build/js/',
                    ext: '.min.js'
                }]
            },
            vue: {
                files: [{
                    sourceMap: true,
                    expand: true,
                    cwd: 'assets/js/vue',
                    src: '*.js',
                    dest: 'assets/build/js/vue',
                    ext: '.min.js'
                }]
            },
            api: {
                files: [{
                    sourceMap: true,
                    expand: true,
                    cwd: 'assets/js/api',
                    src: '*.js',
                    dest: 'assets/build/js/api',
                    ext: '.min.js'
                }]
            },
            viewer: {
                files: [{
                    sourceMap: true,
                    expand: true,
                    cwd: 'assets/js/viewer',
                    src: '*.js',
                    dest: 'assets/build/js/viewer',
                    ext: '.min.js'
                }]
            }
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
                separator: "\n",
                banner: '/*! GENERATED SOURCE FILE '
                + '<%= pkg.name %> - v<%= pkg.version %> - ' +
                '<%= grunt.template.today("yyyy-mm-dd") %> */',
            },
            front_css: {
                src: [
                    'assets/build/css/caldera-grid.min.css',
                    'assets/build/css/caldera-alert.min.css',
                    'assets/build/css/caldera-form.min.css',
                    'assets/build/css/fields.min.css'
                 ],
                dest: 'assets/css/caldera-forms-front.css'
            },

        },
        watch: {
            scripts: {
                files: [
                    'assets/js/*.js',
                    'assets/css/*.css',
                    'assets/js/api/*.js',
                    'assets/js/form-editor/*.js',
                    'assets/js/viewer/*.js'
                ],
                tasks: ['concat:editor', 'uglify' ],
                options: {
                    spawn: false,
                },
            },
            css: {
                files: ['assets/css/*.css'],
                tasks: ['default'],
                options: {
                    spawn: false,
                }
            },
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
            },
            build: {
                expand: true,
                options: {
                    mode:true
                },
                src:  files_list,
                dest: 'build/<%= pkg.version %>',
            }
        },

        clean: {
            post_build: [
                'build'
            ]
        },


        mkdir: {
            build: {
                options: {
                    mode: 0755,
                    create: [ 'build' ]
                }
            }
        }

    });




    grunt.registerTask( 'buildCopy', [ 'copy:i18n', 'copy:fonts', 'copy:images'] );
    //register default task
    grunt.registerTask( 'default',  [
        'concat',
        'uglify',
        'cssmin',
        'buildCopy'
    ] );

    grunt.registerTask( 'js',  [
        'uglify',
        'concat'
    ] );

    grunt.registerTask( 'version_number', [ 'replace' ] );
    grunt.registerTask( 'build', [  'version_number', 'default', 'mkdir:build', 'copy:build' ] );



};
