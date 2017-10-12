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

		self::admin_common();
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
		Caldera_Forms_Render_Assets::maybe_register();
		Caldera_Forms_Render_Assets::enqueue_script( 'validator' );
		$locale = get_locale();
		if( $locale !== 'en_US' ){
			Caldera_Forms_Render_Assets::enqueue_validator_i18n();
		}

		Caldera_Forms_Render_Assets::enqueue_style( 'grid' );
		self::enqueue_style( 'admin' );
		$data = array(
			'adminAjax' => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			'rest' => array(
				'root' => esc_url_raw( Caldera_Forms_API_Util::url() ),
				'nonce' => Caldera_Forms_API_Util::get_core_nonce()
			)
		);

		if( Caldera_Forms_Admin::is_edit( ) ){
			$form_id = trim( $_GET[ Caldera_Forms_Admin::EDIT_KEY ] );
			$data[ 'rest' ][ 'form' ] = esc_url_raw( Caldera_Forms_API_Util::url( 'forms/' . $form_id, true ) );
			$data[ 'rest' ][ 'revisions' ] = esc_url_raw( Caldera_Forms_API_Util::url( 'forms/' . $form_id . '/revisions', true ) );
		}

		wp_localize_script( self::slug( 'admin' ), 'CF_ADMIN', $data  );

		self::enqueue_style( 'modal' );
		self::enqueue_script( 'admin' );
		Caldera_Forms_Render_Assets::enqueue_style( 'field' );

		self::enqueue_script( 'baldrick' );

	}

	/**
	 * Register all scripts for Caldera Forms admin
	 *
	 * @since 1.5.0
	 */
	public static function register_scripts(){
		$version = Caldera_Forms::VERSION;
		Caldera_Forms_Render_Assets::maybe_validator_i18n( true );

		wp_register_script( self::slug( 'shortcode-insert' ), Caldera_Forms_Render_Assets::make_url( 'shortcode-insert' ), array( 'jquery', 'wp-color-picker' ), $version );

		wp_register_script( self::slug( 'baldrick' ), Caldera_Forms_Render_Assets::make_url( 'wp-baldrick-full' ), array( 'jquery' ), $version );
		wp_register_script( self::slug( 'admin' ), Caldera_Forms_Render_Assets::make_url( 'admin' ), array(
			self::slug( 'baldrick' ),
			Caldera_Forms_Render_Assets::make_slug( 'conditionals' ),
			'wp-pointer',
			'password-strength-meter'
		), $version );

		if( Caldera_Forms_Render_Assets::should_minify() ){
			wp_register_script( Caldera_Forms_Render_Assets::make_slug( 'inputmask' ), Caldera_Forms_Render_Assets::make_url( 'inputmask' ), array( 'jquery' ), $version );
		}

		wp_register_script( self::slug( 'edit-fields' ), Caldera_Forms_Render_Assets::make_url( 'fields' ), array(
			'jquery',
			'wp-color-picker',
			Caldera_Forms_Render_Assets::make_slug( 'inputmask' )

		), $version );

		//this is bad, but fixes -> https://github.com/CalderaWP/Caldera-Forms/issues/1141
		wp_register_script( self::slug( 'edit-editor' ), CFCORE_URL . 'assets/js/edit.js', array( 'jquery', 'wp-color-picker' ), $version );

		wp_register_script( self::slug(  'support-page' ), Caldera_Forms_Render_Assets::make_url( 'support-page' ), array( 'jquery' ), $version );

		wp_localize_script( self::slug( 'edit-fields' ), 'CF_ADMIN_TOOLTIPS', self::get_tooltips() );
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
		wp_register_style( self::slug( 'modals', false ), Caldera_Forms_Render_Assets::make_url( 'modals', false ), array( 'wp-color-picker' ), $version );
		wp_register_style( self::slug( 'admin', false ), Caldera_Forms_Render_Assets::make_url( 'admin', false ), array(
			self::slug( 'modals', false ),
			'wp-color-picker',
			'wp-pointer'
		), $version );

		wp_register_style( self::slug( 'processors', false ), Caldera_Forms_Render_Assets::make_url( 'processors-edit', false ), array(), $version );
		wp_register_style( self::slug( 'editor-grid', false ), Caldera_Forms_Render_Assets::make_url( 'editor-grid', false ), array(
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
	protected static function maybe_register_all_admin(){
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

	/**
	 * Get strings for admin tooltips
	 *
	 * @since 1.5.0.7
	 *
	 * @return array
	 */
	public static function get_tooltips(){
		$tooltips = array(
			'add_field_row' => esc_html__( 'Add field to row', 'caldera-forms' ),
			'split_row' => esc_html__( 'Split row.', 'caldera-forms' ),
			'delete_row' => esc_html__( 'Delete row.', 'caldera-forms' ),
		);

		/**
		 * Filter admin tooltips
		 *
		 * @since 1.5.0.7
		 */
		return apply_filters( 'caldera_forms_admin_tooltip_strings', $tooltips );
	}

}