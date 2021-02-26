<?php
/**
 * Utility functions for rendering assets
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Render_Assets
{

	/**
	 * Array of loaded script/style slugs
	 *
	 * @since 1.4.3
	 *
	 * @var array
	 */
	protected static $loaded;

	/**
	 * Array of registered script/style slugs
	 *
	 * @since 1.4.3
	 *
	 * @var array
	 */
	protected static $registered;

	/**
	 * Array of enqueued script/style slugs
	 *
	 * @since 1.4.3
	 *
	 * @var array
	 */
	protected static $enqueued;

	/**
	 * Local language code
	 *
	 * @since 1.5.0.5
	 *
	 * @var string
	 */
	protected static $locale;

	/**
	 * Tracks if CF_API_DATA has been outputted or not
	 *
	 * @since 1.5.7
	 *
	 * @var array
	 */
	protected static $api_data_localized;

    /**
     * Stores modified-time for generated assets
     *
     * @since 1.8.5
     *
     * @var int
     */
	protected static $client_modified_time;


    /**
     * The array from webpack manifest
     *
     * @sicne 1.8.6
     *
     * @var array
     */
	protected static $webpack_asset_manifest;

	/**
	 * Enqueue styles for field type
	 *
	 * @since 1.4.3
	 *
	 * @param array $field_types Field types in form
	 * @param array $field Current field
	 */
	public static function enqueue_field_scripts($field_types, $field)
	{
		self::maybe_register();

		$type = Caldera_Forms_Field_Util::get_type($field);
		if (in_array($type, ['credit_card_number'])) {
			self::enqueue_style('font');
		}

		if (!empty($field_types[ $field[ 'type' ] ][ 'styles' ])) {
			foreach ($field_types[ $field[ 'type' ] ][ 'styles' ] as $style) {
				self::enqueue_style($style);
			}
		}


		if (!empty($field_types[ $field[ 'type' ] ][ 'scripts' ])) {
			$depts = self::field_script_dependencies();
			foreach ($field_types[ $field[ 'type' ] ][ 'scripts' ] as $script) {
				self::enqueue_script($script, $depts);
			}

		}

	}


	/**
	 * Load the optional styles based on settings
	 *
	 * @since 1.4.3
	 */
	public static function optional_style_includes()
	{
		self::maybe_register();

		$style_includes = self::get_style_includes();

		$all = true;
		foreach ($style_includes as $style_include => $use) {
			if (false == $use) {
				$all = false;
			}
			self::enqueue_style(self::make_style_slug($style_include));

		}

		if ($all) {
			self::enqueue_style('front');
			foreach (array_keys($style_includes) as $style_include) {
				wp_dequeue_style(self::make_slug($style_include));
			}
		}


	}

	/**
	 * Make slug for script or style
	 *
	 * @since 1.4.3
	 *
	 * @param string $script Base name
	 *
	 * @return string
	 */
	public static function make_slug($script)
	{
		if (0 === strpos($script, 'cf-')) {
			$slug = $script;
		} elseif (wp_script_is('cf-' . $script, 'registered')) {
			$slug = 'cf-' . $script;
		} elseif (wp_script_is('cf-' . $script . '-scripts', 'registered')) {
			$slug = 'cf-' . $script . '-scripts';
		} elseif (wp_style_is('cf-' . $script, 'registered')) {
			$slug = 'cf-' . $script;
		} elseif (wp_style_is('cf-' . $script . '-styles', 'registered')) {
			$slug = 'cf-' . $script . '-styles';
		} else {
			$slug = 'cf-' . sanitize_key(basename($script));
		}

		return $slug;
	}

	/**
	 * Make slug for a CSS
	 *
	 * @since 1.4.3
	 *
	 * @param string $style Base name
	 *
	 * @return string
	 */
	protected static function make_style_slug($style)
	{
		if (0 === strpos($style, 'cf-')) {
			$slug = $style;
		} elseif (wp_style_is('cf-' . $style . '-styles', 'registered')) {
			$slug = 'cf-' . $style . '-styles';
		} elseif (wp_style_is('cf-' . $style, 'registered')) {
			$slug = 'cf-' . $style;
		} else {
			$slug = 'cf-' . sanitize_key(basename($style));
		}

		return $slug;
	}

	/**
	 * Check if we've tracked loading this script/style yet
	 *
	 * @since 1.4.3
	 *
	 * @param string $slug Slug of script as formed by self::make_slug()
	 * @param string $type Optional. js, the default, or css.
	 *
	 * @return bool
	 */
	protected static function is_loaded($slug, $type = 'js')
	{
		if (empty(self::$loaded)) {
			self::$loaded = array_fill_keys(['js', 'css'], []);
		}

		return isset(self::$loaded[ $type ][ $slug ]);

	}

	/**
	 * Get which of the optional styles are to be used
	 *
	 * @since 1.4.4
	 *
	 * @return array
	 */
	public static function get_style_includes()
	{
		$style_includes = get_option('_caldera_forms_styleincludes');

		/**
		 * Disable/enable including of front-end styles
		 *
		 * @since unknown
		 *
		 * @param array $style_includes To include or not. Default is value of option "_caldera_forms_styleincludes"
		 */
		$style_includes = apply_filters('caldera_forms_get_style_includes', $style_includes);
		$style_includes = wp_parse_args($style_includes, [
			'grid' => true,
			'alert' => true,
			'form' => true,
		]);

		return $style_includes;
	}

	/**
	 * Get URLs and handles for our CSS
	 *
	 * @since 1.4.4
	 *
	 * @return array
	 */
	public static function get_core_styles()
	{
		$style_urls = [
			'modals' => self::make_url('remodal', false),
			'modals-theme' => self::make_url('remodal-default-theme', false),
			'grid' => self::make_url('caldera-grid', false),
			'form' => self::make_url('caldera-form', false),
			'alert' => self::make_url('caldera-alert', false),
			'field' => self::make_url('fields', false),
			'front' => self::make_url('caldera-forms-front', false),
			'font' => self::make_url('cfont', false),
			'table' => self::make_url('caldera-table', false),
			'entry-viewer-2' => self::make_url('entry-viewer-2', false),
			'render' => self::make_url('render', false),
            'form-builder' => self::make_url('form-builder',false )
		];

		$style_urls[ 'fields' ] = $style_urls[ 'field' ];

		$all = true;
		foreach (self::get_style_includes() as $script => $use) {
			if (false == $use) {
				$all = false;
				unset($style_urls[ $script ]);
			}

		}
		if ($all) {
			foreach (self::get_style_includes() as $script => $use) {
				unset($style_urls[ $script ]);
			}

		}


		return $style_urls;
	}

	/**
	 * Get URLs and handles for our JavaScripts
	 *
	 * @since 1.4.4
	 *
	 * @return array
	 */
	public static function get_core_scripts()
	{
		$script_urls = [
			'dynamic' => self::make_url('formobject'),
			'modals' => self::make_url('remodal'),
			'baldrick' => self::make_url('jquery-baldrick'),
			'ajax' => self::make_url('ajax-core'),
			'field-config' => self::make_url('field-config'),
			'field' => self::make_url('fields'),
			'fields' => self::make_url('fields'),
			'conditionals' => self::make_url('conditionals'),
			'validator-i18n' => null,
			'validator' => self::make_url('parsley'),
			'validator-aria' => self::make_url('parsley-aria'),
			'init' => self::make_url('frontend-script-init'),
			'handlebars' => CFCORE_URL . 'assets/js/handlebars.js',
			'entry-viewer-2' => self::make_url('entry-viewer-2'),
			'vue' => self::make_url('vue/vue'),
			'vue-status' => self::make_url('vue/status-component'),
			'vue-filter' => self::make_url('vue/vue-filter'),
			'form-front' => self::make_url('caldera-forms-front'),
			'api-client' => self::make_url('api/client'),
			'api-stores' => self::make_url('api/stores'),
			'state-events' => self::make_url('state/events'),
			'state-state' => self::make_url('state/state'),
			'blocks' => self::make_url('blocks'),
			'editor' => self::make_url('editor'),
			'pro' => self::make_url('pro'),
			'privacy' => self::make_url('privacy'),
			'render' => self::make_url('render'),
			'legacy-bundle' => self::make_url('legacy-bundle'),
            'form-builder' => self::make_url('form-builder')
		];

		return $script_urls;
	}

	/**
	 * Enqueue CSS/JS for all field types
	 *
	 * @since 1.5.0
	 */
	public static function enqueue_all_fields()
	{
		self::maybe_register();


		self::enqueue_form_assets();


		foreach (self::get_field_styles() as $style) {
			self::enqueue_style($style);

		}

		$depts = self::field_script_dependencies();
		foreach (self::get_field_scripts() as $script) {
			self::enqueue_script(self::make_slug($script), $depts);
		}


	}

	/**
	 * Registers all front-end scripts and styles
	 *
	 * @since 1.5.0
	 */
	public static function register()
	{
		self::$registered = self::$enqueued = [
			'scripts' => [],
			'styles' => [],
		];
		$style_urls = self::get_core_styles();
		$script_urls = self::get_core_scripts();

		if (self::should_minify(true)) {
			unset($script_urls[ 'vue' ]);
			unset($script_urls[ 'vue-filters' ]);
			unset($script_urls[ 'vue-status' ]);
			if (!is_admin()) {
				unset($script_urls[ 'ajax' ]);
				unset($script_urls[ 'conditionals' ]);
			}
			$slug = self::make_slug('vue');
			self::$registered[ 'scripts' ][] = $slug;
			wp_register_script($slug, self::make_url('vue'), ['jquery'], CFCORE_VER, true);
		}

		$script_style_urls = [];

		// check to see language and include the language add on
		Caldera_Forms_Render_Assets::maybe_validator_i18n(true);


		/**
		 * Filter script URLS for Caldera Forms on the frontend, before they are enqueued.
		 *
		 * @since 1.3.1
		 *
		 * @param array $script_urls array containing all urls to register
		 */
		$script_style_urls[ 'script' ] = apply_filters('caldera_forms_script_urls', $script_urls);

		/**
		 * Filter style URLS for Caldera Forms on the frontend, before they are enqueued.
		 *
		 * @since 1.3.1
		 *
		 * @param array $script_urls array containing all urls to register
		 */
		$script_style_urls[ 'style' ] = apply_filters('caldera_forms_style_urls', $style_urls);

		// register styles
		foreach ($script_style_urls[ 'style' ] as $style_key => $style_url) {
			if (empty($style_url)) {
				continue;
			}
			$slug = self::make_style_slug($style_key);
			wp_register_style($slug, $style_url, [], CFCORE_VER);
			self::$registered[ 'styles' ][] = $slug;
		}

		// register scripts
		foreach ($script_style_urls[ 'script' ] as $script_key => $script_url) {
			if (empty($script_url)) {
				continue;
			}
			if ('validator' == $script_key) {
				wp_register_script(self::make_slug($script_key), $script_url, ['jquery'], CFCORE_VER, false);
				continue;
			} elseif ('validator-aria' == $script_key) {
				wp_register_script(self::make_slug($script_key), $script_url, ['jquery', self::make_slug('validator')],
					CFCORE_VER, false);
			}
			self::register_script($script_key, $script_url);
		}

		foreach (self::get_field_styles() as $style) {
			$slug = self::make_style_slug($style);
			self::$registered[ 'styles' ][] = $slug;
			wp_register_style($slug, $style, [], CFCORE_VER);

		}

		$depts = self::field_script_dependencies();
		foreach (self::get_field_scripts() as $script) {
			self::register_script(self::make_slug($script), $script, $depts);
		}

		/**
		 * Runs after scripts and styles are registered
		 *
		 * @since 1.5.0
		 *
		 * @param array $script_style_urls URLs of registered scripts and styles
		 */
		do_action('caldera_forms_assets_registered', $script_style_urls);


	}

	/**
	 * Enqueue a style for Caldera Forms front-end
	 *
	 * @since 1.5.0
	 *
	 * @param string $style Slug or URL
	 */
	public static function enqueue_style($style)
	{
		if (('table' == $style || $style == self::make_style_slug('table')) && self::should_minify(false)) {
			$style = self::make_style_slug('entry-viewer-2');
		}
		$slug = self::make_style_slug($style);

		if (!wp_style_is($style, 'enqueued')) {
			if (false !== strpos($style, '//')) {
				self::$enqueued[ 'styles' ][] = $slug;
				wp_enqueue_style($slug, $style, [], CFCORE_VER);

			} else {
				if (wp_style_is($style, 'registered')) {
					wp_enqueue_style($style);
				} elseif (wp_style_is($slug, 'registered')) {
					self::$enqueued[ 'styles' ][] = $slug;
					wp_enqueue_style($slug);
				}

			}
		}

	}

	/**
	 * Enqueue a script for Caldera Forms front-end
	 *
	 * @since 1.5.0
	 *
	 * @param string $script Script slug or URL
	 * @param array $depts Optional. Array of dependencies. Default is jQuery
	 */
	public static function enqueue_script($script, $depts = ['jquery'])
	{

		if( 'render' === $script ||$script === self::make_slug('render')  ){
			if (is_admin() ) {
				$load_render = false;
			}elseif (
				self::is_elementor_editor()
			){
				$load_render = false;
			}
			elseif( self::is_beaver_builder_editor()  ){
				$load_render = false;
			}else{
				$load_render = true;
			}
			if( ! $load_render  ){
				return;
			}
		}

		if( 'blocks' === $script ||$script === self::make_slug('blocks')  ){
			if (self::is_elementor_editor()){
				return;
			}

		}

		if ('editor-grid' === $script) {
			return Caldera_Forms_Admin_Assets::enqueue_script($script);
		}


		if (in_array($script, ['validator', self::make_slug('validator')])) {
			$scripts = self::get_core_scripts();
			wp_enqueue_script(self::make_slug('validator'), $scripts[ 'validator' ], [], CFCORE_VER, false);
			if (!self::should_minify()) {
				wp_enqueue_script(self::make_slug('validator-aria'));
			}
		}
		$slug = self::make_slug($script);
		if (!wp_script_is($slug, 'enqueued') && wp_script_is($slug, 'registered')) {

			self::$enqueued[ 'scripts' ][] = $slug;
			if (!empty($depts) && is_array($depts) && !filter_var($script, FILTER_VALIDATE_URL)) {
				wp_enqueue_script($slug);
			} else {
				wp_enqueue_script($slug, $script, $depts, CFCORE_VER, false);
			}


		} elseif (wp_script_is($script, 'registered')) {
			self::$enqueued[ 'scripts' ][] = $slug;
			wp_enqueue_script($slug);
		}

		if (Caldera_Forms::settings()->get_cdn()->combine()) {
			Caldera_Forms_CDN_Init::get_cdn()->add_to_combiner($slug);
		}

	}


	/**
	 * Create a URL for a script/style used by Caldera Forms
	 *
	 * @since 1.5.0
	 *
	 * @param string $name Name of script/style (no .js/.css or min please)
	 * @param bool $script Optional. True if is script, the default, false if is style
	 *
	 * @return string
	 */
	public static function make_url($name, $script = true)
	{
		$root_url = CFCORE_URL;

		if ('editor' == $name && !self::is_client_entry_point($name)) {
			$name = 'edit';
		}

		if (!$script && 'fields' == $name) {
			$min = true;
		} else {
			$min = self::should_minify($script);
		}

		//@SEE https://github.com/CalderaWP/Caldera-Forms/issues/2487#issuecomment-388445315
		if ('edit' === $name) {
			$min = false;
		}

		if (self::is_client_entry_point($name)) {
		    if( 'admin-client' === $name ){
                $name = 'admin';
            }

		    $manifest = self::get_webpack_manifest();

			if ($script) {
                if (
                    in_array($name, [
                        'blocks',
                        'pro',
                        'privacy',
                        'legacy-bundle',
                       	'form-builder'
                    ])
                    || empty($manifest)
                    || ! array_key_exists("{$name}.js",$manifest)
                ) {
                    return "{$root_url}clients/{$name}/build/index.min.js";
                } else {
                    return $manifest["{$name}.js"];
                }
            } else {
                if (
                    in_array($name, [
                        'blocks',
                        'pro',
                        'privacy',
                        'legacy-bundle',
                        'form-builder'
                    ])
                    || empty($manifest)
                    || ! array_key_exists("{$name}.css",$manifest)
                ) {
                    return "{$root_url}clients/{$name}/build/style.min.css";

                }else{
                    return $manifest["{$name}.css"];
                }
			}
		}

		if ($min) {
			if ($script) {
				return $root_url . 'assets/build/js/' . $name . '.min.js';
			} else {
				return $root_url . 'assets/build/css/' . $name . '.min.css';
			}
		} else {
			if ($script) {
				return $root_url . 'assets/js/' . $name . '.js';
			} else {
				return $root_url . 'assets/css/' . $name . '.css';
			}
		}

	}


	/**
	 * Is this the slug of a webpack entry point
	 *
	 * @since 1.6.2
	 *
	 * @param string $slug
	 *
	 * @return bool
	 */
	public static function is_client_entry_point($slug)
	{
		return in_array($slug, [
		    'admin-client',
		    'blocks',
            'pro',
            'privacy',
            'render',
            'legacy-bundle',
            'form-builder'
        ]);
	}

	/**
	 * Should we mbe minifying script/styles?
	 *
	 * @since 1.5.0
	 *
	 * @param bool $script Optional. True if is script, the default, false if is style
	 *
	 * @return bool
	 */
	public static function should_minify($script = true)
	{
		/**
		 * Filter for disabling minimization of script(s)/style(s)
		 *
		 * @since 1.5.0
		 *
		 * @param bool $minify Whether to minfify or not.
		 * @param bool $script If is script or not.
		 */
		return apply_filters('caldera_forms_render_assets_minify', true, $script);
	}

	/**
	 * Find dependencies tags or enqueue them
	 *
	 * @since 1.8.4
	 */
	public static function cf_dependencies($tag){

		global $wp_version;
		$tags = [];
		if( !version_compare($wp_version, '5.0.0', '>=') ){
			if(!wp_script_is( self::make_slug( 'legacy-bundle' ), 'enqueued')){
				self::enqueue_script('legacy-bundle');
			}
			$tags = [self::make_slug( 'legacy-bundle' )];
		}else {

		    //Get the json file listing dependencies for this client.
            //Generate with wordpress-scripts by running `yarn build:clients` or `yarn build`.
            $root_path = CFCORE_PATH;
            if( 'admin-client' === $tag ){
                $name = 'admin';
            }else{
                $name = $tag;
            }
            $deps_path = "{$root_path}clients/{$name}/build/index.min.asset.json";
            $assets = file_exists($deps_path) ? (array)json_decode(file_get_contents($deps_path)) : [];
            //If file exists it SHOULD have key "dependencies" with a list of tags.
            $tags =  is_array($assets) && isset($assets['dependencies']) ? $assets['dependencies'] : [];
        }

		foreach ( $tags as $_tag ){
            if( ! wp_script_is($_tag, 'registered')){
                global $wp_scripts;
                wp_default_packages_vendor( $wp_scripts );
                wp_default_packages_scripts( $wp_scripts );
                break;
            }
        }
        //this should not be needed, but it seams to be only way to get react on the page
        foreach ($tags as $t) {
            wp_enqueue_script($t);
        }

		return $tags;
	}

	/**
	 * Load form JS
	 *
	 * @since 1.5.0
	 */
	public static function enqueue_form_assets()
	{
		self::enqueue_style('field-styles');
		self::maybe_validator_i18n(false);
		self::enqueue_script('validator');
		self::enqueue_script('init');
		self::enqueue_script('render', self::cf_dependencies('render') );
		self::enqueue_style('render');

		$should_minify = self::should_minify();
		if ($should_minify) {
			self::enqueue_script('validator');
			self::enqueue_script('form-front', []);
			foreach ([
						 'field-config',
						 'field',
						 'fields',
						 'init',
						 'state-state',
						 'state-events',
					 ] as $script) {
				wp_dequeue_script(self::make_slug($script));
			}
		} else {
			self::enqueue_script('fields');
			self::enqueue_script('field-config', [self::make_slug('validator'), self::make_slug('field')]);
		}

		$field_script_to_localize = self::field_script_to_localize_slug();
		if (!is_array(self::$api_data_localized) || !isset(self::$api_data_localized[ $field_script_to_localize ])) {
			wp_localize_script($field_script_to_localize, 'CF_API_DATA', [
				'rest' => [
					'root' => esc_url_raw(Caldera_Forms_API_Util::url()),
					'rootV3' => esc_url_raw(Caldera_Forms_API_Util::url('', false, 'v3')),
					'fileUpload' => esc_url_raw(Caldera_Forms_API_Util::url('file', false, 'v3')),
					'tokens' => [
						'nonce' => esc_url_raw(Caldera_Forms_API_Util::url('tokens/form')),
					],
					'nonce' => wp_create_nonce('wp_rest'),
				],
				'strings' => [
					'cf2FileField' => [
						'removeFile' => esc_attr__('Remove file', 'caldera-forms'),
						'defaultButtonText' => esc_attr__('Drop files or click to select files to Upload',
							'caldera-forms'),
						'fileUploadError1' => esc_attr__('Error: ', 'caldera-forms'),
						'fileUploadError2' => esc_attr__(' could not be processed', 'caldera-forms'),
						'invalidFiles' => esc_attr__('These Files have been rejected : ', 'caldera-forms'),
						'checkMessage' => esc_attr__('Please check files type and size', 'caldera-forms'),
						'invalidFileResponse' => esc_attr__('Unknown File Process Error', 'caldera-forms'),
						'fieldIsRequired' => esc_attr__('Field is required', 'caldera-forms'),
						'filesUnit' => esc_attr__('bytes', 'caldera-forms'),
						'maxSizeAlert' => esc_attr__('This file is too large. Maximum size is ', 'caldera-forms'),
						'wrongTypeAlert' => esc_attr__('This file type is not allowed. Allowed types are ',
							'caldera-forms'),
					],
				],
				'nonce' => [
					'field' => Caldera_Forms_Render_Nonce::nonce_field_name(),
				],
			]);

			self::$api_data_localized[ $field_script_to_localize ] = true;
		}

	}

	/**
	 * Register scripts if not already registered
	 *
	 * @since 1.5.0
	 *
	 * Calls self::do_register() if not already ran
	 */
	public static function maybe_register()
	{
		if (!did_action('caldera_forms_assets_registered')) {
			self::register();
		}
	}

	/**
	 * Register a script
	 *
	 * @since 1.5.0
	 *
	 *
	 * @param string $script_key Slug or URL
	 * @param string $script_url URL
	 * @param array $depts Optional. Dependencies argument. Assumed to be jquery.
	 */
	protected static function register_script($script_key, $script_url, $depts = ['jquery'])
	{
		if (0 === strpos($script_key, 'cf-')) {
			$slug = $script_key;
		} else {
			$slug = self::make_slug($script_key);
		}


		if ('field' == $script_key) {
			$depts[] = self::make_slug('field-config');
		} elseif ('field-config' == $script_key) {
			$depts[] = self::make_slug('validator');
			$depts[] = self::make_slug('state-state');
			$depts[] = self::make_slug('state-events');
		} elseif ('entry-viewer-2' == $script_key) {
			$depts = ['jquery', self::make_slug('vue'), 'underscore'];
		} elseif ('vue-filter' == $script_key || 'vue-status' == $script_key) {
			$depts = [self::make_slug('vue')];
		} elseif (in_array($script_key, [
			'api-client',
			'api-stores',
		])) {
			add_filter('caldera_forms_render_assets_minify', '__return_false', 51);
			$script_url = self::make_url(str_replace('-', '/', $script_key));
			self::$registered[ 'scripts' ][] = $slug;
			wp_register_script($slug, $script_url, $depts, CFCORE_VER, true);
			remove_filter('caldera_forms_render_assets_minify', '__return_false', 51);
			return;
		} elseif (in_array($script_key, [
			'state-events',
			'state-state',
		])) {
			add_filter('caldera_forms_render_assets_minify', '__return_false', 51);
			$script_url = self::make_url(str_replace('-', '/', $script_key));
			self::$registered[ 'scripts' ][] = $slug;
			wp_register_script($slug, $script_url, $depts, CFCORE_VER, true);
			remove_filter('caldera_forms_render_assets_minify', '__return_false', 51);
			return;
		} elseif ('editor' == $script_key) {
			$depts = ['jquery', 'wp-color-picker'];
		} else {
			//no needd...
		}

		self::$registered[ 'scripts' ][] = $slug;
		wp_register_script($slug, $script_url, $depts, CFCORE_VER, true);

	}

	/**
	 * Get the urls of all field Javascript
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public static function get_field_scripts()
	{
		$fields = Caldera_Forms_Fields::get_all();
		$scripts = [];
		foreach ($fields as $field) {
			if (isset($field[ 'scripts' ]) && is_array($field[ 'scripts' ])) {
				$scripts = array_merge($field[ 'scripts' ], $scripts);
			}
		}

		return $scripts;
	}

	/**
	 * Get the urls of all field CSS
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public static function get_field_styles()
	{
		$fields = Caldera_Forms_Fields::get_all();
		$styles = [];
		foreach ($fields as $field) {
			if (isset($field[ 'styles' ]) && is_array($field[ 'styles' ])) {
				$styles = array_merge($field[ 'styles' ], $styles);
			}
		}
		return $styles;
	}

	/**
	 * Find slug of script to use with wp_localize_script()
	 *
	 * @since unknown
	 *
	 * @return string
	 */
	public static function field_script_to_localize_slug()
	{
		$should_minify = self::should_minify();
		if ($should_minify) {
			$localize_slug = 'form-front';
		} else {
			$localize_slug = 'init';
		}

		return self::make_slug($localize_slug);
	}

	/**
	 * Enqueue CSS/JS for modals
	 *
	 * @since 1.5.0.4
	 */
	public static function enqueue_modals()
	{
		self::maybe_register();
		self::enqueue_style('modals');
		self::enqueue_style('modals-theme');
		self::enqueue_script('modals');

	}

	/**
	 * If needed register or enqueue parsley translations
	 *
	 *
	 * @since 1.5.0.5
	 *
	 * @param bool $register Optional. If false, file is enqueued. Default is true, which registers.
	 */
	public static function maybe_validator_i18n($register = true)
	{

		$locale = get_locale();
		/**
		 * Instead of loading Parsely validator strings via HTTP, print in footer
		 *
		 * @since 1.8.3
		 *
		 * @param bool $use_footer Print strings in footer. Defaults to false
		 * @param string $locale Current local
		 */
		$print_strings = apply_filters('caldera_forms_print_translation_strings_in_footer', false, $locale);
		if ($locale == 'en_US' && ! $print_strings) {
			return;
		}

		if ($register) {
			self::register_validator_i18n($locale);
		} else {
			if (self::$locale) {
				$code = self::set_locale_code($locale);
			} else {
				$code = $locale;
			}
			if ($print_strings) {
				wp_localize_script(self::make_slug('validator'), 'CF_VALIDATOR_STRINGS', [
					'defaultMessage' => __( "This value seems to be invalid.", 'caldera-forms' ),
					'type' => [
						'email' => __("This value should be a valid email.", 'caldera-forms' ),
						'url' => __("This value should be a valid url.", 'caldera-forms' ),
						'number' => __("This value should be a valid number.", 'caldera-forms' ),
						'integer' => __("This value should be a valid integer.", 'caldera-forms' ),
						'digits' => __("This value should be digits.", 'caldera-forms' ),
						'alphanum' => __("This value should be alphanumeric.", 'caldera-forms' ),
					],
					'notblank' => __("This value should not be blank.", 'caldera-forms' ),
					'required' => __("This value is required", 'caldera-forms' ),
					'pattern' => __("This value seems to be invalid.", 'caldera-forms' ),
					'min' =>__( "This value should be greater than or equal to %s.", 'caldera-forms' ),
					'max' => __("This value should be lower than or equal to %s.", 'caldera-forms' ),
					'range' =>__( "This value should be between %s and %s.", 'caldera-forms' ),
					'minlength' => __("This value is too short. It should have %s characters or more.", 'caldera-forms' ),
					'maxlength' => __("This value is too long. It should have %s characters or fewer.", 'caldera-forms' ),
					'length' => __("This value length is invalid. It should be between %s and %s characters long.", 'caldera-forms' ),
					'mincheck' => __("You must select at least %s choices.", 'caldera-forms' ),
					'maxcheck' => __("You must select %s choices or fewer.", 'caldera-forms' ),
					'check' => __("You must select between %s and %s choices.", 'caldera-forms' ),
					'equalto' => __("This value should be the same.", 'caldera-forms' ),
				]);
				$script = "<script>window.Parsley.addMessages( '$code', CF_VALIDATOR_STRINGS ); window.Parsley.setLocale( '$locale' );</script>";
				wp_dequeue_script( self::make_slug( 'validator-i18n' ) );
			}else{
				$script = "<script> setTimeout(function(){window.Parsley.setLocale('$code'); }, 2000 );</script>";

			}
			Caldera_Forms_Render_Util::add_inline_data($script, ['ID' => rand()]);
			self::enqueue_validator_i18n();
		}

	}

	/**
	 * Register parsley translations
	 *
	 * @since 1.5.0.5
	 *
	 * @param null|string $locale Optional. Locale code, if null, the default, get_local is used;
	 *
	 * @return null|string Returns locale code if found
	 */
	public static function register_validator_i18n($locale = null)
	{
		if (!$locale) {
			$locale = get_locale();
		}

		$validator_url = self::get_validator_locale_url($locale);

		wp_register_script(
			self::make_slug('validator-i18n'),
			$validator_url,
			[self::make_slug('validator')],
			CFCORE_VER, true
		);

		self::$locale = $locale;
		return $locale;

	}

	/**
	 * Enqueue previously registered parsely translations
	 *
	 * @since 1.5.0.5
	 */
	public static function enqueue_validator_i18n()
	{

		self::enqueue_script(self::make_slug('validator-i18n'));
	}

	/**
	 * Get field script dependencies for wp_enqueue_script()
	 *
	 * @since 1.5.0.7
	 *
	 * @return array
	 */
	protected static function field_script_dependencies()
	{
		$depts = ['jquery', self::make_slug('validator')];
		if (self::should_minify()) {
			$depts[] = self::field_script_to_localize_slug();

		} else {
			$depts[] = self::make_slug('field');
			$depts[] = self::make_slug('field-config');

		}

		return $depts;
	}

	/**
	 * Get the URL for the parsely translations file
	 *
	 * @since 1.8.3
	 *
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function get_validator_locale_url($locale)
	{
		//Use correct formatted locale for file to be matched
		$locale = self::set_locale_code($locale);

		$validator_url = CFCORE_URL . 'assets/js/i18n/' . $locale . '.js';
		return $validator_url;

	}

	/**
	 * Get the Locale Code used by translation files
	 *
	 * @since 1.8.4
	 *
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function set_locale_code($locale)
	{
		if (file_exists(CFCORE_PATH . 'assets/js/i18n/' . $locale . '.js')) {
			// no need to check other possibilities- break if/else early
		} elseif (file_exists(CFCORE_PATH . 'assets/js/i18n/' . strtolower($locale) . '.js')) {
			$locale = strtolower($locale);
		} elseif (file_exists(CFCORE_PATH . 'assets/js/i18n/' . strtolower(str_replace('_', '-', $locale)) . '.js')) {
			$locale = strtolower(str_replace('_', '-', $locale));
		} elseif (file_exists(CFCORE_PATH . 'assets/js/i18n/' . strtolower(substr($locale, 0, 2)) . '.js')) {
			$locale = strtolower(substr($locale, 0, 2));
		} elseif (file_exists(CFCORE_PATH . 'assets/js/i18n/' . strtolower(substr($locale, 3)) . '.js')) {
			$locale = strtolower(substr($locale, 3));
		} else {
			//No file is matching the locale in validation folder, 
			//Fallback to english instead of not enqueueing for Parsley to be set a locale and not return an error
			$locale = "en";
		}

		return $locale;
	}

	/**
	 * Check if Elementor is being used to edit current post
	 *
	 * @since 1.8.4
	 *
	 * @return bool
	 */
	protected static function is_elementor_editor()
	{
		return isset($_GET) && (
			isset($_GET[ 'action' ]) && 'elementor' === $_GET[ 'action' ]
			|| isset($_GET[ 'elementor-preview' ])
		);
	}

	/**
	 * Check if Beaver Builder is being used to edit current post
	 *
	 * @since 1.8.4
	 *
	 * @return bool
	 */
	protected static function is_beaver_builder_editor(){
		return isset($_GET, $_GET[ 'fl_builder']);
	}


    /**
     * Get the generated asset-mainfiest.json
     *
     * @since 1.8.6
     *
     * @return array
     */
    protected static function get_webpack_manifest()
    {
        if( ! is_array( self::$webpack_asset_manifest ) ){
            $path =  CFCORE_PATH .'/dist/asset-manifest.json';
            if (!file_exists($path)) {
                return [];
            }
            $contents = file_get_contents($path);
            if (empty($contents)) {
                return [];
            }
            $assets = json_decode($contents, true);
            if( ! empty($assets) ){

                self::$webpack_asset_manifest = [];
                foreach ($assets as $asset => $url) {
                    if (false === strpos($asset, '.map')) {
                        self::$webpack_asset_manifest[$asset] = $url;
                    }
                }

            }else{
                self::$webpack_asset_manifest = [];
            }
        }
        return self::$webpack_asset_manifest;

    }

	/**
	 * If neccasary, remove 'ver' query arg from CSS/ JS URL
	 * 
	 * This is neccasary when webpack is serving the asset for HMR to work.
	 * 
	 * @since 1.8.6
	 * 
	 * @uses "script_loader_src" filter
	 * @uses "style_loader_src" filter
	 */
	public static function maybe_remove_version_query_arg( $src,$handle){
		$manifest = self::get_webpack_manifest();
		if( empty( $manifest)){
			return $src;
		}
		if( in_array($handle,[
			'cf-admin-client',
			'cf-render',
			'cf-privacy',
			'cf-block'
		])){
			$src = remove_query_arg('ver',$src );
		}
		return $src;

	}

	public static function maybe_redirect_to_dist(){
		$manifest = self::get_webpack_manifest();
		if( empty($manifest)){
			return;
		}
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI']  : '';
		if( $uri && 0 === strpos( $uri,'/dist/caldera-hot-load' ) ){
	
			$path =  CFCORE_PATH  . $_SERVER['REQUEST_URI'];
			if( file_exists($path)){
				cf_redirect(untrailingslashit(CFCORE_URL) . $uri);exit;
			}
		}
	

	}
}
