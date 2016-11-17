<?php

/**
 * Handles asset loading for admin
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Admin_Assets {

	/**
	 * Enqueue scripts and styles used in the post editor
	 *
	 * @since 1.5.0
	 */
	public static function post_editor(){
		self::maybe_register_all_admin();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		self::enqueue_style( 'modal' );
		self::enqueue_script( 'shortcode-insert' );
	}

	/**
	 * Enqueue scripts and styles used in the form editor
	 *
	 * @since 1.5.0
	 */
	public static function form_editor(){
		self::maybe_register_all_admin();
		wp_enqueue_style( 'wp-color-picker' );
		self::enqueue_script( 'edit-fields' );
		self::enqueue_script( 'edit-editor' );
		self::enqueue_style( 'editor-grid' );
		wp_enqueue_script( 'jquery-ui-users' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
	}

	/**
	 * Enqueue scripts and styles used in main admin and in form editor
	 *
	 * @since 1.5.0
	 */
	public static function admin_common(){
		self::maybe_register_all_admin();
		Caldera_Forms_Render_Assets::enqueue_style( 'grid' );
		self::enqueue_style( 'admin' );
		self::enqueue_style( 'modal' );
		self::enqueue_style( 'field' );

		self::enqueue_script( 'baldrick' );
		self::enqueue_script( 'admin' );
	}

	/**
	 * Register all scripts for Caldera Forms admin
	 *
	 * @since 1.5.0
	 */
	public static function register_scripts(){
		$version = Caldera_Forms::VERSION;

		wp_register_script( self::slug( 'shortcode', '-insert' ), CFCORE_URL . 'assets/js/shortcode-insert.min.js', array( 'jquery', 'wp-color-picker' ), $version );


		wp_register_script( self::slug( 'baldrick' ), CFCORE_URL . 'assets/js/wp-baldrick-full.js', array( 'jquery' ), $version );
		wp_register_script( self::slug( 'admin' ), CFCORE_URL . 'assets/js/admin.min.js', array(
			self::slug( 'baldrick' ),
			'wp-pointer',
			'password-strength-meter'
		), $version );

		wp_register_script( self::slug( 'edit-fields' ), CFCORE_URL . 'assets/js/fields.min.js', array( 'jquery', 'wp-color-picker' ), $version );

		wp_register_script( self::slug( 'edit-editor' ), CFCORE_URL . 'assets/js/edit.min.js', array( 'jquery', 'wp-color-picker' ), $version );

		wp_register_script( self::slug(  'support-page' ), CFCORE_URL . 'assets/js/support-page.js', array( 'jquery' ), $version );

		/**
		 * Runs after scripts are registered for Caldera Forms admin
		 *
		 * @since 1.5.0
		 */
		do_action( 'caldera_forms_admin_assets_scripts_registered' );
	}

	/**
	 * Register all styles for Caldera Forms admin
	 *
	 * @since 1.5.0
	 */
	public static function register_styles(){
		$version = Caldera_Forms::VERSION;
		wp_register_style( self::slug( 'fields', false ), CFCORE_URL . 'assets/css/fields.min.css', array( 'wp-color-picker' ), $version );
		wp_register_style( self::slug( 'modals', false ), CFCORE_URL . 'assets/css/modals.css', array( 'wp-color-picker' ), $version );
		wp_register_style( self::slug( 'admin', false ), CFCORE_URL . 'assets/css/admin.css', array(
			self::slug( 'modals', false ),
			self::slug( 'fields', false ),
			'wp-pointer'
		), $version );

		wp_register_style( self::slug( 'processors', false ), CFCORE_URL . 'assets/css/processors-edit.css', array(), $version );
		wp_register_style( self::slug( 'editor-grid', false ), CFCORE_URL . 'assets/css/editor-grid.css', array(
			self::slug( 'processors', false )
		), $version );

		/**
		 * Runs after styles are registered for Caldera Forms admin
		 *
		 * @since 1.5.0
		 */
		do_action( 'caldera_forms_admin_assets_styles_registered' );

	}

	/**
	 * Enqueue a style for Caldera Forms admin
	 *
	 * @since 1.5.0
	 *
	 * @param string $slug Style slug
	 */
	public static function enqueue_style( $slug ){
		if( 1 !== strpos( $slug, Caldera_Forms::PLUGIN_SLUG ) ){
			$slug = self::slug( $slug, false );
		}

		wp_enqueue_style( $slug );
	}

	/**
	 * Enqueue a script for Caldera Forms admin
	 *
	 * @since 1.5.0
	 *
	 * @param string $slug Script slug
	 */
	public static function enqueue_script( $slug ){
		if( 1 !== strpos( $slug, Caldera_Forms::PLUGIN_SLUG ) ){
			$slug = self::slug( $slug, true );
		}

		wp_enqueue_script( $slug );
	}

	/**
	 * Create a script/style slug for Caldera Forms admin
	 *
	 * @since 1.5.0
	 *
	 * @param string $slug Short slug
	 * @param bool|string $script Optional. True, the default append -scripts, false appends -style. A string appends that string.
	 *
	 * @return string
	 */
	public static function slug( $slug, $script = true ){
		if( 'baldrick' == $slug ){
			$slug = Caldera_Forms::PLUGIN_SLUG . '-' . $slug;
			return $slug;
		}
		$slug = Caldera_Forms::PLUGIN_SLUG . '-' . $slug;
		if( is_string( $script ) ){
			$slug .= $script;
		}elseif( true === $script ){
			$slug .= '-scripts';
		}elseif( false === $script ){
			$slug .= '-styles';
		}

		return $slug;
	}

	/**
	 * Load scripts for form editor panels
	 *
	 * @since 1.5.0
	 */
	public static function panels(){
		$panels = Caldera_Forms_Admin_Panel::get_panels();
		if( ! empty( $panels ) ){
			foreach ( $panels as $panel ){
				if( ! empty( $panel[ 'setup' ][ 'scripts' ] ) ){
					foreach( $panel[ 'setup' ][ 'scripts' ] as $script ){
						if( filter_var( $script, FILTER_VALIDATE_URL ) ){
							self::enqueue_script( $script );
						}else{
							wp_enqueue_script( $script );
						}
					}

					foreach( $panel[ 'setup' ][ 'styles' ] as $style ){
						if( filter_var( $style, FILTER_VALIDATE_URL ) ){
							self::enqueue_style( $style );
						}else{
							wp_enqueue_style( $style );
						}
					}
				}
			}
		}
	}

	/**
	 * Registers all scripts needed if not registered yet
	 *
	 * @since 1.5.0
	 */
	protected function maybe_register_all_admin(){
		$front = false;
		if( ! did_action( 'caldera_forms_admin_assets_styles_registered' ) ){
			Caldera_Forms_Render_Assets::register();
			Caldera_Forms_Render_Assets::enqueue_all_fields();
			$front = true;
			self::register_styles();
		}

		if( ! did_action( 'caldera_forms_admin_assets_scripts_registered' ) ){
			if( false === $front ){
				Caldera_Forms_Render_Assets::register();
				Caldera_Forms_Render_Assets::enqueue_all_fields();
			}
			self::register_scripts();
		}
	}

}