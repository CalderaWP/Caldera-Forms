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
	 * Enqueue styles for field type
	 *
	 * @since 1.4.3
	 *
	 * @param array $field_types Field types in form
	 * @param array $field Current field
	 */
	public static function enqueue_field_scripts( $field_types, $field ) {
		if( ! did_action( 'caldera_forms_assets_registered' ) ){
			self::register();
		}

		$type = Caldera_Forms_Field_Util::get_type( $field );
		if( in_array( $type , array( 'credit_card_number' ) ) ){
			self::enqueue_style( 'font' );
		}

		self::enqueue_script( self::make_slug( 'field-config' ) );

		if( !empty( $field_types[$field['type']]['styles'])){
			foreach($field_types[$field['type']]['styles'] as $style){
				self::enqueue_style( $style );
			}
		}


		if ( ! empty( $field_types[ $field[ 'type' ] ][ 'scripts' ] ) ) {
			$depts = array( 'jquery', self::make_slug( 'field'), self::make_slug( 'field-config' ), self::make_slug( 'validator' ) );
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
		if( ! did_action( 'caldera_forms_assets_registered' ) ){
			self::register();
		}

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
		if( wp_script_is( 'cf-' . $script, 'registered' )  ){
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
		if ( wp_style_is( 'cf-' . $style . '-styles', 'registered' ) ) {
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
			'modals'       => self::make_url( 'remodal', false ),
			'modals-theme' => self::make_url( 'remodal-default-theme', false ),
			'grid'         => self::make_url( 'caldera-grid', false ),
			'form'         => self::make_url( 'caldera-form', false ),
			'alert'        => self::make_url( 'caldera-alert', false ),
			'field'        => self::make_url( 'fields', false ),
			'front'        => self::make_url( 'caldera-forms-front', false ),
			'font'         => self::make_url( 'cfont', false )
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
			'init'           => self::make_url( 'frontend-script-init' ),
			'handlebars'     => CFCORE_URL . 'assets/js/handlebars.js'
		);

		return $script_urls;
	}


	public static function enqueue_all_fields(){
		if( ! did_action( 'caldera_forms_assets_registered' ) ){
			self::register();
		}

		$field_types = Caldera_Forms_Fields::get_all();

		self::enqueue_form_assets();

		foreach ( $field_types as $field ) {
			if ( ! empty( $field[ 'styles' ] ) ) {
				foreach ( $field[ 'styles' ] as $style ) {
					self::enqueue_style( $style );

				}
			}

			if ( ! empty( $field[ 'scripts' ] ) ) {
				$depts[] = 'jquery';
				foreach ( $field[ 'scripts' ] as $script ) {
					self::enqueue_script( $script, $depts );
				}
			}
		}
	}

	/**
	 * Registers all front-end scripts and styles
	 *
	 * @since 1.5.0
	 */
	public static function register(){
		$style_urls = self::get_core_styles();
		$script_urls = self::get_core_scripts();

		$script_style_urls = array();

		// check to see language and include the language add on
		$locale = get_locale();
		if( $locale !== 'en_US' ){
			// not default lets go find if there is a translation available
			if( file_exists( CFCORE_PATH . 'assets/js/i18n/' . $locale . '.js' ) ){
				// nice- its there
				$locale_file = $locale;
			}elseif ( file_exists( CFCORE_PATH . 'assets/js/i18n/' . strtolower( $locale ) . '.js' ) ){
				$locale_file = strtolower( $locale );
			}elseif ( file_exists( CFCORE_PATH . 'assets/js/i18n/' . strtolower( str_replace('_', '-', $locale ) ) . '.js' ) ){
				$locale_file = strtolower( str_replace('_', '-', $locale ) );
			}elseif ( file_exists( CFCORE_PATH . 'assets/js/i18n/' . strtolower( substr( $locale,0, 2 ) ) . '.js' ) ){
				$locale_file = strtolower( substr( $locale,0, 2 ) );
			}elseif ( file_exists( CFCORE_PATH . 'assets/js/i18n/' . strtolower( substr( $locale,3 ) ) . '.js' ) ){
				$locale_file = strtolower( substr( $locale,3 ) );
			}
			if( !empty( $locale_file ) ){
				$script_urls['validator-i18n'] = CFCORE_URL . 'assets/js/i18n/' . $locale_file . '.js';
				$script_style_urls['config']['validator_lang'] = $locale_file;
			}

		}

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
			wp_register_style( self::make_style_slug( $style_key ), $style_url, array(), CFCORE_VER );
		}
		// register scripts
		foreach( $script_style_urls['script'] as $script_key => $script_url ){
			if( empty( $script_url ) ){
				continue;
			}
			$depts = array( 'jquery' );
			if( 'field' == $script_key ) {
				$depts[] = self::make_slug( 'validator' );
				$depts[] = self::make_slug( 'field-config' );

			}elseif ( 'field-config' == $script_key ){
				$depts[] = self::make_slug( 'validator' );
			}

			wp_register_script( 'cf-' . $script_key, $script_url, $depts, CFCORE_VER, true );
		}

		// localize for dynamic form generation
		wp_localize_script( 'cf-dynamic', 'cfModals', $script_style_urls );

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
		if ( ! wp_style_is( $style,  'enqueued'  ) ) {
			if ( false !== strpos( $style, '//' ) ) {
				$slug = self::make_style_slug( $style );
				if ( ! self::is_loaded( $slug, 'css' ) ) {
					wp_enqueue_style( $slug, $style, array(), CFCORE_VER );
					self::$loaded[ 'css' ][ $slug ] = true;
				}
			} else {
				if ( wp_style_is( $style, 'registered') ) {
					wp_enqueue_style( $style );
				}elseif( wp_style_is( self::make_style_slug( $style ), 'registered' ) ){
					wp_enqueue_style( self::make_style_slug( $style ) );
				} elseif( wp_style_is( self::make_slug( $style ), 'registered' ) ){
					wp_enqueue_style( self::make_slug( $style ) );
				} elseif ( 'cf-' !== substr( $style, 0, 2 ) ) {
					if ( wp_style_is(  'cf-' . $style, 'registered' ) ) {
						wp_enqueue_script( 'cf-' . $style );
					}
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
		if ( ! wp_script_is( $script, 'enqueued') ) {
			if ( false !== strpos( $script, '//' ) ) {
				$slug = self::make_slug( $script );
				if ( ! self::is_loaded( $slug ) ) {
					wp_enqueue_script( $slug, $script, $depts, CFCORE_VER, true );
					self::$loaded[ 'js' ][ $slug ] = true;
				}
			} else {

				if ( wp_script_is( $script, 'registered' ) ) {
					wp_enqueue_script( $script );
				} elseif ( 'cf-' !== substr( $script, 0, 2 ) ) {
					if ( wp_script_is(  'cf-' . $script, 'registered' ) ) {
						wp_enqueue_script( 'cf-' . $script );
					}
				}
			}
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

		$min = self::should_minify( $script );
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
		self::enqueue_script( 'validator-i18n' );
		self::enqueue_script( 'validator' );
		self::enqueue_script( 'field-config', array( self::make_slug( 'validator' ), self::make_slug( 'field' ) ) );
		self::enqueue_script( 'field' );

		self::enqueue_script( 'init' );

		wp_localize_script( self::make_slug( 'init' ), 'CF_API_DATA', array(
			'rest' => array(
				'root' => esc_url_raw( Caldera_Forms_API_Util::url() ),
				'tokens' => array(
					'nonce' => esc_url_raw( Caldera_Forms_API_Util::url( 'tokens/form' ) )
				)
			),
			'nonce' => array(
				'field' => Caldera_Forms_Render_Nonce::nonce_field_name(),
			),
		) );

	}

}