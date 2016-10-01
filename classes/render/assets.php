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
		$style_includes = get_option( '_caldera_forms_styleincludes' );

		/**
		 * Disable/enable including of front-end styles
		 *
		 * @since unknown
		 *
		 * @param array $style_includes To include or not. Default is value of option "_caldera_forms_styleincludes"
		 */
		$style_includes = apply_filters( 'caldera_forms_get_style_includes', $style_includes );

		if ( ! empty( $style_includes[ 'grid' ] ) ) {
			wp_enqueue_style( 'cf-grid-styles' );
		}
		if ( ! empty( $style_includes[ 'form' ] ) ) {
			wp_enqueue_style( 'cf-form-styles' );
		}
		if ( ! empty( $style_includes[ 'alert' ] ) ) {
			wp_enqueue_style( 'cf-alert-styles' );
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

}