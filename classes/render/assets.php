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
class Caldera_Forms_Render_Assets {

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
	 * Enqueue styles for field type
	 *
	 * @since 1.4.3
	 *
	 * @param array $field_types Field types in form
	 * @param array $field Current field
	 */
	public static function enqueue_field_scripts( $field_types, $field ) {
		self::maybe_register();

		$type = Caldera_Forms_Field_Util::get_type( $field );
		if( in_array( $type , array( 'credit_card_number' ) ) ){
			self::enqueue_style( 'font' );
		}

		if( !empty( $field_types[$field['type']]['styles'])){
			foreach($field_types[$field['type']]['styles'] as $style){
				self::enqueue_style( $style );
			}
		}


		if ( ! empty( $field_types[ $field[ 'type' ] ][ 'scripts' ] ) ) {
			$depts = self::field_script_dependencies();
			foreach ( $field_types[ $field[ 'type' ] ][ 'scripts' ] as $script ) {
				self::enqueue_script( $script, $depts );
			}

		}

	}


	/**
	 * Load the optional styles based on settings
	 *
	 * @since 1.4.3
	 */
	public static function optional_style_includes() {
		self::maybe_register();

		$style_includes = self::get_style_includes();

		$all = true;
		foreach( $style_includes as $style_include => $use ){
			if( false == $use ){
				$all = false;
			}
			self::enqueue_style( self::make_style_slug( $style_include ) );

		}

		if( $all ){
			self::enqueue_style( 'front' );
			foreach( array_keys(  $style_includes ) as $style_include  ){
				wp_dequeue_style( self::make_slug( $style_include ) );
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
	public static function make_slug( $script ) {
		if( 0 === strpos( $script, 'cf-' ) ){
			$slug = $script;
		}elseif( wp_script_is( 'cf-' . $script, 'registered' )  ){
			$slug = 'cf-' . $script;
		}elseif( wp_script_is( 'cf-' . $script . '-scripts', 'registered' )  ){
			$slug =  'cf-' . $script . '-scripts';
		} elseif( wp_style_is( 'cf-' . $script, 'registered' )  ){
			$slug = 'cf-' . $script;
		}elseif( wp_style_is( 'cf-' . $script . '-styles', 'registered' )  ){
			$slug =  'cf-' . $script . '-styles';
		}else{
			$slug = 'cf-' . sanitize_key( basename( $script ) );
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
	protected static function make_style_slug( $style ){
		if( 0 === strpos( $style, 'cf-' ) ){
			$slug = $style;
		} elseif ( wp_style_is( 'cf-' . $style . '-styles', 'registered' ) ) {
			$slug = 'cf-' . $style . '-styles';
		}elseif ( wp_style_is( 'cf-' . $style, 'registered' ) ) {
			$slug = 'cf-' . $style;
		}else {
			$slug = 'cf-' . sanitize_key( basename( $style ) );
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
	protected static function is_loaded( $slug, $type = 'js' ){
		if ( empty( self::$loaded ) ){
			self::$loaded = array_fill_keys( array( 'js', 'css' ), array() );
		}

		return isset( self::$loaded[ $type ][ $slug ] );

	}

	/**
	 * Get which of the optional styles are to be used
	 *
	 * @since 1.4.4.
	 *
	 * @return array
	 */
	protected static function get_style_includes() {
		$style_includes = get_option( '_caldera_forms_styleincludes' );

		/**
		 * Disable/enable including of front-end styles
		 *
		 * @since unknown
		 *
		 * @param array $style_includes To include or not. Default is value of option "_caldera_forms_styleincludes"
		 */
		$style_includes = apply_filters( 'caldera_forms_get_style_includes', $style_includes );
		$style_includes = wp_parse_args( $style_includes, array(
			'grid'  => true,
			'alert' => true,
			'form'  => true
		) );

		return $style_includes;
	}

	/**
	 * Get URLs and handles for our CSS
	 *
	 * @since 1.4.4
	 *
	 * @return array
	 */
	public static function get_core_styles(){
		$style_urls = array(
			'modals'            => self::make_url( 'remodal', false ),
			'modals-theme'      => self::make_url( 'remodal-default-theme', false ),
			'grid'              => self::make_url( 'caldera-grid', false ),
			'form'              => self::make_url( 'caldera-form', false ),
			'alert'             => self::make_url( 'caldera-alert', false ),
			'field'             => self::make_url( 'fields', false ),
			'front'             => self::make_url( 'caldera-forms-front', false ),
			'font'              => self::make_url( 'cfont', false ),
			'table'             => self::make_url( 'caldera-table', false ),
			'entry-viewer-2'    => self::make_url( 'entry-viewer-2', false ),

		);

		$all = true;
		foreach ( self::get_style_includes()  as $script => $use ){
			if( false == $use ){
				$all = false;
				unset( $style_urls[  $script ] );
			}

		}
		if( $all ){
			foreach ( self::get_style_includes()  as $script => $use ){
				unset( $style_urls[ $script ] );
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
	public static function get_core_scripts(){
		$script_urls = array(
			'dynamic'        => self::make_url( 'formobject' ),
			'modals'         => self::make_url( 'remodal' ),
			'baldrick'       => self::make_url( 'jquery-baldrick' ),
			'ajax'           => self::make_url( 'ajax-core' ),
			'field-config'   => self::make_url( 'field-config' ),
			'field'          => self::make_url( 'fields' ),
			'conditionals'   => self::make_url( 'conditionals' ),
			'validator-i18n' => null,
			'validator'      => self::make_url( 'parsley' ),
			'validator-aria' => self::make_url( 'parsley-aria' ),
			'init'           => self::make_url( 'frontend-script-init' ),
			'handlebars'     => CFCORE_URL . 'assets/js/handlebars.js',
			'entry-viewer-2' => self::make_url( 'entry-viewer-2'),
			'vue'               => self::make_url( 'vue/vue' ),
			'vue-status'        => self::make_url( 'vue/status-component' ),
			'vue-filter' => self::make_url( 'vue/vue-filter' ),
			'form-front' => self::make_url( 'caldera-forms-front' ),
            'api-client' => self::make_url( 'api/client' ),
            'api-stores' => self::make_url( 'api/stores' ),
			'state-events' => self::make_url( 'state/events' ),
			'state-state' => self::make_url( 'state/state' ),
			'inputmask' => self::make_url( 'inputmask')
		);

		return $script_urls;
	}

	/**
	 * Enqueue CSS/JS for all field types
	 *
	 * @since 1.5.0
	 */
	public static function enqueue_all_fields(){
		self::maybe_register();


		self::enqueue_form_assets();


		foreach ( self::get_field_styles() as $style ) {
			self::enqueue_style( $style );

		}

		$depts = self::field_script_dependencies();
		foreach ( self::get_field_scripts() as $script ) {
			self::enqueue_script( self::make_slug( $script ), $depts );
		}


	}

	/**
	 * Registers all front-end scripts and styles
	 *
	 * @since 1.5.0
	 */
	public static function register(){
		self::$registered = self::$enqueued = array(
			'scripts' => array(),
			'styles' => array()
		);
		$style_urls = self::get_core_styles();
		$script_urls = self::get_core_scripts();

		if( self::should_minify( true ) ){
			unset( $script_urls[ 'inputmask' ] );
			unset( $script_urls[ 'vue' ] );
			unset( $script_urls[ 'vue-filters' ] );
			unset( $script_urls[ 'vue-status' ] );
			$slug = self::make_slug( 'vue' );
			self::$registered[ 'scripts' ][] = $slug;
			wp_register_script( $slug, self::make_url( 'vue' ), array( 'jquery' ), CFCORE_VER, true );
		}

		$script_style_urls = array();

		// check to see language and include the language add on
		Caldera_Forms_Render_Assets::maybe_validator_i18n( true );


		/**
		 * Filter script URLS for Caldera Forms on the frontend, before they are enqueued.
		 *
		 * @since 1.3.1
		 *
		 * @param array $script_urls array containing all urls to register
		 */
		$script_style_urls['script'] = apply_filters( 'caldera_forms_script_urls', $script_urls );

		/**
		 * Filter style URLS for Caldera Forms on the frontend, before they are enqueued.
		 *
		 * @since 1.3.1
		 *
		 * @param array $script_urls array containing all urls to register
		 */
		$script_style_urls['style'] = apply_filters( 'caldera_forms_style_urls', $style_urls );

		// register styles
		foreach( $script_style_urls['style'] as $style_key => $style_url ){
			if( empty( $style_url ) ){
				continue;
			}
			$slug = self::make_style_slug( $style_key );
			wp_register_style( $slug, $style_url, array(), CFCORE_VER );
			self::$registered[ 'styles' ][] = $slug;
		}

		// register scripts
		foreach( $script_style_urls['script'] as $script_key => $script_url ){
			if( empty( $script_url ) ){
				continue;
			}
			if( 'validator' == $script_key ){
				wp_register_script( self::make_slug( $script_key ), $script_url, array(), CFCORE_VER, false );
				continue;
			}elseif ( 'validator-aria' == $script_key ){
				wp_register_script( self::make_slug( $script_key ), $script_url, array( 'jquery', self::make_slug( 'validator' ) ), CFCORE_VER, false );
			}
            self::register_script( $script_key, $script_url );
		}

		foreach ( self::get_field_styles() as $style ){
			$slug = self::make_style_slug( $style );
			self::$registered[ 'styles'][] = $slug;
			wp_register_style( $slug, $style, array(), CFCORE_VER );

		}

		$depts = self::field_script_dependencies();
		foreach ( self::get_field_scripts() as $script ) {
			self::register_script( self::make_slug( $script ), $script, $depts );
		}

		/**
		 * Runs after scripts and styles are registered
		 *
		 * @since 1.5.0
		 *
		 * @param array $script_style_urls URLs of registered scripts and styles
		 */
		do_action( 'caldera_forms_assets_registered', $script_style_urls );


	}

	/**
	 * Enqueue a style for Caldera Forms front-end
	 *
	 * @since 1.5.0
	 *
	 * @param string $style Slug or URL
	 */
	public static function enqueue_style( $style ){
		if( ( 'table' == $style || $style == self::make_style_slug( 'table' ) ) && self::should_minify( false ) ){
			$style = self::make_style_slug( 'entry-viewer-2' );
		}
		$slug = self::make_style_slug( $style );

		if ( ! wp_style_is( $style,  'enqueued'  ) ) {
			if ( false !== strpos( $style, '//' ) ) {
				self::$enqueued[ 'styles' ][] = $slug;
				wp_enqueue_style( $slug, $style, array(), CFCORE_VER );

			} else {
				if ( wp_style_is( $style, 'registered') ) {
					wp_enqueue_style( $style );
				}elseif( wp_style_is( $slug, 'registered' ) ){
					self::$enqueued[ 'styles' ][] = $slug;
					wp_enqueue_style( $slug );
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
	public static function enqueue_script( $script, $depts = array( 'jquery' ) ){
		if( in_array( $script, array( 'validator', self::make_slug( 'validator' ) ) ) ){
			$scripts = self::get_core_scripts();
			wp_enqueue_script( self::make_slug( 'validator' ), $scripts[ 'validator' ], array(), CFCORE_VER, false );
			if( ! self::should_minify() ){
				wp_enqueue_script( self::make_slug( 'validator-aria' ) );
			}
		}
		$slug = self::make_slug( $script );
		if ( ! wp_script_is( $slug, 'enqueued' ) && wp_script_is( $slug, 'registered' ) ) {

			self::$enqueued[ 'scripts' ][] = $slug;
			if (  ! empty( $depts)  && is_array( $depts ) && ! filter_var( $script, FILTER_VALIDATE_URL )  ) {
				wp_enqueue_script( $slug );
			} else {
				wp_enqueue_script( $slug, $script, $depts, CFCORE_VER, false );
			}


		}elseif ( wp_script_is( $script, 'registered' ) ) {
			self::$enqueued[ 'scripts' ][] = $slug;
			wp_enqueue_script( $slug );
		}

		if( Caldera_Forms::settings()->get_cdn()->combine() ){
			Caldera_Forms_CDN_Init::get_cdn()->add_to_combiner( $slug );
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
	public static function make_url( $name, $script = true ){
		$root_url = CFCORE_URL;

		if( ! $script && 'fields' == $name ){
			$min = true;
		}else{
			$min = self::should_minify( $script );
		}


		if ( $min ) {
			if ( $script ) {
				return $root_url . 'assets/build/js/' . $name . '.min.js';
			} else {
				return $root_url . 'assets/build/css/' . $name . '.min.css';
			}
		}else{
			if ( $script ) {
				return $root_url . 'assets/js/' . $name . '.js';
			} else {
				return $root_url . 'assets/css/' . $name . '.css';
			}
		}

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
	public static function should_minify( $script = true ){
		/**
		 * Filter for disabling minimization of script(s)/style(s)
		 *
		 * @since 1.5.0
		 *
		 * @param bool $minify Whether to minfify or not.
		 * @param bool $script If is script or not.
		 */
		return apply_filters( 'caldera_forms_render_assets_minify', true, $script );
	}

	/**
	 * Load form JS
	 *
	 * @since 1.5.0
	 */
	public static function enqueue_form_assets(){
		self::enqueue_style( 'field-styles' );
		self::maybe_validator_i18n( false );
		self::enqueue_script( 'validator' );
		self::enqueue_script( 'init' );

		$should_minify = self::should_minify();
		if( $should_minify  ){
			self::enqueue_script( 'validator' );
			self::enqueue_script( 'form-front', array(  ) );
			foreach ( array(
				'field-config',
				'field',
				'init',
				'state-state',
				'state-events',
			) as  $script ) {
				wp_dequeue_script( self::make_slug( $script ) );
			}
		}else{
			self::enqueue_script( 'field' );
			self::enqueue_script( 'field-config', array( self::make_slug( 'validator' ), self::make_slug( 'field' ) ) );
		}

		wp_localize_script(  self::field_script_to_localize_slug(), 'CF_API_DATA', array(
			'rest' => array(
				'root' => esc_url_raw( Caldera_Forms_API_Util::url() ),
				'tokens' => array(
					'nonce' => esc_url_raw( Caldera_Forms_API_Util::url( 'tokens/form' ) )
				),
				'nonce' => wp_create_nonce( 'wp_rest' )
			),
			'nonce' => array(
				'field' => Caldera_Forms_Render_Nonce::nonce_field_name(),
			),
		) );

	}

	/**
	 * Register scripts if not already registered
	 *
	 * @since 1.5.0
	 *
	 * Calls self::do_register() if not already ran
	 */
	public static function maybe_register() {
		if ( ! did_action( 'caldera_forms_assets_registered' ) ) {
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
    protected static function register_script( $script_key, $script_url, $depts = array( 'jquery' ) ) {
    	if( 0 === strpos( $script_key, 'cf-' ) ){
		    $slug = $script_key;
	    }else{
		    $slug = self::make_slug( $script_key );
	    }


        if ( 'field' == $script_key) {
            $depts[] = self::make_slug( 'field-config' );
	        $depts[] = self::make_slug( 'inputmask' );
        } elseif ( 'field-config' == $script_key) {
            $depts[] = self::make_slug( 'validator' );
            $depts[] = self::make_slug( 'state-state' );
            $depts[] = self::make_slug( 'state-events' );
        } elseif ( 'entry-viewer-2' == $script_key) {
            $depts = array('jquery', self::make_slug( 'vue' ), 'underscore' );
        } elseif ( 'vue-filter' == $script_key || 'vue-status' == $script_key ) {
            $depts = array(self::make_slug('vue'));
        } elseif (in_array( $script_key, array(
            'api-client',
            'api-stores'
        ))) {
            add_filter('caldera_forms_render_assets_minify', '__return_false', 51);
            $script_url = self::make_url(str_replace('-', '/', $script_key));
	        self::$registered[ 'scripts' ][] = $slug;
            wp_register_script( $slug, $script_url, $depts, CFCORE_VER, true);
            remove_filter('caldera_forms_render_assets_minify', '__return_false', 51);
			return;
        } elseif (in_array( $script_key, array(
	        'state-events',
	        'state-state'
        ))) {
	        add_filter('caldera_forms_render_assets_minify', '__return_false', 51);
	        $script_url = self::make_url(str_replace('-', '/', $script_key));
	        self::$registered[ 'scripts' ][] = $slug;
	        wp_register_script( $slug, $script_url, $depts, CFCORE_VER, true);
	        remove_filter('caldera_forms_render_assets_minify', '__return_false', 51);
	        return;
        }else{
	    	//no needd...
	    }

		self::$registered[ 'scripts' ][] = $slug;
        wp_register_script( $slug, $script_url, $depts, CFCORE_VER, true);

    }

	/**
	 * Get the urls of all field Javascript
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
    protected static function get_field_scripts(){
	    $fields  = Caldera_Forms_Fields::get_all();
	    $scripts = array();
	    foreach ( $fields as $field ) {
		    if ( isset( $field[ 'scripts' ] ) && is_array( $field[ 'scripts' ] ) ) {
			    $scripts = array_merge( $field[ 'scripts' ], $scripts );
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
	protected static function get_field_styles(){
		$fields = Caldera_Forms_Fields::get_all();
		$styles = array();
		foreach ( $fields as $field ) {
			if ( isset( $field[ 'styles' ] ) && is_array( $field[ 'styles' ] ) ) {
				$styles = array_merge( $field[ 'styles' ], $styles );
			}
		}
		return $styles;
	}

	public static function field_script_to_localize_slug(){
		$should_minify = self::should_minify();
		if( $should_minify  ) {
			$localize_slug = 'form-front';
		}else{
			$localize_slug = 'init';
		}

		return self::make_slug( $localize_slug );
	}

	/**
	 * Enqueue CSS/JS for modals
	 *
	 * @since 1.5.0.4
	 */
	public static function enqueue_modals(){
		self::maybe_register();
		self::enqueue_style( 'modals' );
		self::enqueue_style( 'modals-theme' );
		self::enqueue_script( 'modals' );

	}

	/**
	 * If needed register or enqueue parsley translations
	 *
	 *
	 * @since 1.5.0.5
	 *
	 * @param bool $register Optional. If false, file is enqueued. Default is true, which registers.
	 */
	public static function maybe_validator_i18n( $register = true ){
		$locale = get_locale();
		if( $locale == 'en_US' ){
			return;
		}

		if( $register ){
			self::register_validator_i18n( $locale );
		}else{
			if ( self::$locale ) {
				$code = self::$locale;
			}else{
				$code = $locale;
			}
			$script = "<script> setTimeout(function(){window.Parsley.setLocale('$code'); }, 2000 );</script>";
			Caldera_Forms_Render_Util::add_inline_data( $script, array( 'ID' => rand() )  );
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
	public static function register_validator_i18n( $locale = null ){
		if( ! $locale ){
			$locale = get_locale();
		}

		if( file_exists( CFCORE_PATH . 'assets/js/i18n/' . $locale . '.js' ) ){
			// no need to check other possibilities- break if/else early

		}elseif ( file_exists( CFCORE_PATH . 'assets/js/i18n/' . strtolower( $locale ) . '.js' ) ){
			$locale = strtolower( $locale );
		}elseif ( file_exists( CFCORE_PATH . 'assets/js/i18n/' . strtolower( str_replace('_', '-', $locale ) ) . '.js' ) ){
			$locale = strtolower( str_replace('_', '-', $locale ) );
		}elseif ( file_exists( CFCORE_PATH . 'assets/js/i18n/' . strtolower( substr( $locale,0, 2 ) ) . '.js' ) ){
			$locale = strtolower( substr( $locale,0, 2 ) );
		}elseif ( file_exists( CFCORE_PATH . 'assets/js/i18n/' . strtolower( substr( $locale,3 ) ) . '.js' ) ){
			$locale = strtolower( substr( $locale,3 ) );
		}

		wp_register_script( self::make_slug( 'validator-i18n' ), CFCORE_URL . 'assets/js/i18n/' . $locale . '.js', array( self::make_slug( 'validator' ) ), CFCORE_VER, true );

		self::$locale = $locale;
		return $locale;

	}

	/**
	 * Enqueue previously registered parsely translations
	 *
	 * @since 1.5.0.5
	 */
	public static function enqueue_validator_i18n( ){

		self::enqueue_script( self::make_slug( 'validator-i18n' ) );
	}

	/**
	 * Get field script dependencies for wp_enqueue_script()
	 *
	 * @since 1.5.0.7
	 *
	 * @return array
	 */
	protected static function field_script_dependencies(){
		$depts = array( 'jquery', self::make_slug( 'validator' ) );
		if ( self::should_minify() ) {
			$depts[] = self::field_script_to_localize_slug();

		} else {
			$depts[] = self::make_slug( 'field' );
			$depts[] = self::make_slug( 'field-config' );

		}

		return $depts;
	}


}