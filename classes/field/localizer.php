<?php

/**
 * Handles sending form field config to DOM
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Field_Localizer {

	/**
	 * The configs to be printed
	 *
	 * @since 1.5.0
	 *
	 * @var
	 */
	protected static $localized;

	/**
	 * Add form
	 *
	 * Functions as factory for Caldera_Forms_Render_FieldsJS
	 *
	 * @since 1.5.0
	 *
	 * @param array $form
	 * @param $current_form_count
	 */
	public static function add_form( array  $form, $current_form_count ){
		$fieldjs = new Caldera_Forms_Field_JS( $form, $current_form_count  );

		self::$localized[ $current_form_count ] = $fieldjs->to_array();
		add_action( 'wp_footer', array( __CLASS__, 'localize_cb' ), 100 );

	}

	/**
	 * Output the configs as CDATA
	 *
	 * @since 1.5.0
	 *
	 * @uses "wp_footer"
	 */
	public static function localize_cb(){
		if ( ! empty( self::$localized ) ) {

			$slug = Caldera_Forms_Render_Assets::field_script_to_localize_slug();
			$data = array();
			foreach ( self::$localized as $form_instance => $form_data ){
				$form_data = array_merge( $form_data, array(
					'error_strings' => self::error_strings()
				));
				$data[ $form_instance ] = $form_data;
			}

			$wp_scripts = wp_scripts();
			wp_localize_script( $slug, 'CFFIELD_CONFIG', $data );

			$wp_scripts->print_extra_script( $slug, true );

		}
	}

	/**
	 * Error messages to localize
	 *
	 * @since 1.5.3
	 *
	 * @return array
	 */
	protected static function error_strings(){
		$strings = array(
			'mixed_protocol' => __( 'Submission URL and current URL protocols do not match. Form may not function properly.', 'caldera-forms' ),
			'jquery_old'     => __( 'An out of date version of jQuery is loaded on the page. Form may not function properly.', 'caldera-forms' )
		);

		if( ! current_user_can( Caldera_Forms::get_manage_cap( 'admin' )) ){
			unset( $strings[ 'jquery_old' ] );
		}

		return $strings;

	}
}