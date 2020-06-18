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
        '!.gitattributes',
        '!.gitignore',
        '!.gitmodules',
        '!.travis.yml',
        '!composer.lock',
        '!CONTRIBUTING.md',
        '!Gruntfile.js',
        '!package.json',
        '!package-lock.json',
        '!phpunit.xml.dist',
        '!ui/blocks/cform/node_modules/**',
        '!ui/blocks/cform/package.json',
        '!ui/blocks/cform/package-lock.json',
        '!ui/blocks/cform/webpack.config.js',
        '!includes/cf-pro-client/client/**',
        '!includes/cf-pro-client/client/dist/favicon.io',
        '!includes/cf-pro-client/node_modules/**',
        '!includes/cf-pro-client/DEV-README.MD',
        '!includes/cf-pro-client/README.md',
        '!includes/cf-pro-client/composer.json',
        '!includes/cf-pro-client/composer.lock',
        '!includes/cf-pro-client/package-lock.json',
        '!includes/cf-pro-client/package.json',
        '!includes/cf-pro-client/build/**',
        //Exclude client dir, most of it we don't need
        '!clients/**',
        '!src/**',
        '!Dockerfile',
        '!.env',
        '!db-error.php',
        '!webpack.config.js',
        '!docker-compose.yml',
        '!wp-content/**',
        '!wordpress/**',
        '!cypress/**',
        '!contributing/**',
        '!cypress.json',
        '!webpack.blocks.js',
        '!webpack.clients.js',
        '!phpunit-integration.xml.dist',
        '!phpunit-unit.xml.dist',
        '!phpunit.xml.dist',
        '!yarn.lock'
    ];

    //Include webpacked clients
    [
        'admin',
		'blocks',
        'components',
        'form-builder',
        'functions',
		'privacy',
        'pro',
        'render',
        'state',
        'viewer'
    ].forEach( (client) => {
       files_list.push( `clients/${client}/build/index.min.js` );
       files_list.push( `clients/${client}/build/style.min.css` );
       files_list.push( `clients/${client}/build/index.min.asset.json` );
       files_list.push( `clients/${client}/build/index.min.asset.php` );
    });

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
            },
            state: {
                files: [{
                    sourceMap: true,
                    expand: true,
                    cwd: 'assets/js/state',
                    src: '*.js',
                    dest: 'assets/build/js/state',
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
            vue: {
                src: [
                    'assets/js/vue/vue.js',
                    'assets/js/vue/status-component.js',
                    'assets/js/vue/vue-filters.js'
                ],
                dest: 'assets/js/vue.js'
            },
            entry: {
                src: [
                    'assets/js/api/client.js',
                    'assets/js/api/stores.js',
                    'assets/js/viewer/viewer.js',
                    'assets/js/viewer/init.js'
                ],
                dest: 'assets/js/entry-viewer-2.js'
            },
            form: {
                src: [
                    'assets/js/ajax-core.js',
                    'assets/js/conditionals.js',
                    'assets/js/state/events.js',
                    'assets/js/state/state.js',
                    'assets/js/inputmask.js',
                    'assets/js/fields.js',
                    'assets/js/field-config.js',
                    'assets/js/frontend-script-init.js',
                ],
                dest: 'assets/js/caldera-forms-front.js'
            },
            parsley: {
                src: [
                    'assets/js/parsley.js',
                    'assets/js/parsley-aria.js'
                ],
                dest: 'assets/js/parsley.min.js'
            }
        },
        watch: {
            scripts: {
                files: [
                    'assets/js/*.js',
					'assets/js/state/*.js',
                    'assets/css/*.css'
                ],
                tasks: ['default'],
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
            entry: {
                files: [
                    'assets/js/api/client.js',
                    'assets/js/api/stores.js',
                    'assets/js/viewer/viewer.js',
                    'assets/js/viewer/init.js'
                ],
                tasks: ['concat:entry', 'uglify'],
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
                    mode: '0755',
                    create: [ 'build' ]
                }
            }
        },


        exec: {
            deleteVendor: 'rm -rf vendor',
            composerDist: 'composer clearcache && rm -rf vendor && composer update --prefer-dist --no-dev --optimize-autoloader --ignore-platform-reqs'
        }

    });

    grunt.registerTask( 'buildCopy', [ 'copy:i18n', 'copy:fonts', 'copy:images'] );
    //register default task
    grunt.registerTask( 'default',  [
        'js',
        'cssmin',
        'buildCopy'
    ] );

    grunt.registerTask( 'js',  [
		'concat',
        'uglify'
    ] );

    grunt.registerTask( 'version_number', [ 'replace' ] );
    grunt.registerTask( 'build', [  'version_number', 'default',  'make' ] );
    grunt.registerTask( 'make', [
        'exec:deleteVendor',
        'exec:composerDist',
        'mkdir:build',
        'copy:build'
    ] );

};
