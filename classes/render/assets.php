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

		if( !empty( $field_types[$field['type']]['styles'])){
			foreach($field_types[$field['type']]['styles'] as $style){
				if( false !== strpos($style, '//')){
					$slug = self::make_slug( $style );
					if ( ! self::is_loaded( $slug, 'css' )  ) {
						wp_enqueue_style( $slug, $style, array(), CFCORE_VER );
						self::$loaded[ 'css' ][ $slug ] = true;
					}
				}else{
					if ( ! wp_style_is( $style ) ) {
						wp_enqueue_style( $style );
					}
				}
			}
		}


		if ( ! empty( $field_types[ $field[ 'type' ] ][ 'scripts' ] ) ) {
			$depts = array( 'jquery' );
			foreach ( $field_types[ $field[ 'type' ] ][ 'scripts' ] as $script ) {
				if ( false !== strpos( $script, '//' ) ) {
					$slug = self::make_slug( $script );
					if (  ! self::is_loaded( $slug ) ) {
						wp_enqueue_script( $slug, $script, $depts, CFCORE_VER );
						self::$loaded[ 'js' ][ $slug ] = true;
					}
				} else {
					if (  ! wp_script_is( $script ) ) {
						wp_enqueue_script( $script );
					}
				}

			}

		}

	}


	/**
	 * Load the optional styles based on settings
	 *
	 * @since 1.4.3
	 */
	public static function optional_style_includes() {
		$style_includes = self::get_style_includes();
		$all = true;
		$styles = array();
		if ( true == $style_includes[ 'grid' ]  ) {
			$styles[] =  'cf-grid-styles';
		}else{
			$all = false;
		}

		if ( true == $style_includes[ 'form' ]  ) {
			$styles[] = 'cf-form-styles';
		} else{

			$all = false;
		}

		if ( true == $style_includes[ 'alert' ]  ) {
			$styles[] = 'cf-alert-styles';

		}else{
			$all = false;
		}

		if( $all ){
			wp_enqueue_style( 'cf-front-styles' );
		}else{
			if (  ! empty( $styles ) ) {
				foreach ( $styles as $style ) {
					wp_enqueue_style( $style );
				}

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
		$slug = 'cf-' . sanitize_key( basename( $script ) );
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
			'modals' => CFCORE_URL . 'assets/css/remodal.min.css',
			'modals-theme' => CFCORE_URL . 'assets/css/remodal-default-theme.min.css',
			'grid' => CFCORE_URL . 'assets/css/caldera-grid.css',
			'form' => CFCORE_URL . 'assets/css/caldera-form.css',
			'alert' => CFCORE_URL . 'assets/css/caldera-alert.css',
			'field' => CFCORE_URL . 'assets/css/fields.min.css',
		);

		$all = true;
		foreach ( self::get_style_includes()  as $script => $use ){
			if( false == $use ){
				$all = false;
				unset($style_urls[  $script ] );
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
			'dynamic'	=>	CFCORE_URL . 'assets/js/formobject.min.js',
			'modals'	=>	CFCORE_URL . 'assets/js/remodal.min.js',
			'baldrick'	=>	CFCORE_URL . 'assets/js/jquery.baldrick.min.js',
			'ajax'		=>	CFCORE_URL . 'assets/js/ajax-core.min.js',
			'field'	=>	CFCORE_URL . 'assets/js/fields.min.js',
			'conditionals' => CFCORE_URL . 'assets/js/conditionals.min.js',
			'validator-i18n' => null,
			'validator' => CFCORE_URL . 'assets/js/parsley.min.js',
			'init'		=>	CFCORE_URL . 'assets/js/frontend-script-init.min.js',
		);

		return $script_urls;
	}

}