<?php

/**
 * Nonce abstraction for protecting forms against cross-site request forgery
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Render_Nonce {

	/**
	 * Nonce action prefix
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	protected static $action = 'caldera_forms_front_';

	/**
	 * Create verification nonce
	 *
	 * @since 1.5.0
	 *
	 * @param string $form_id Form ID
	 *
	 * @return string
	 */
	public static function create_verify_nonce( $form_id ){
		return wp_create_nonce( self::nonce_action( $form_id ) );
	}

	/**
	 * Get name of nonce field
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public static function nonce_field_name( $form_id = false ){
		$name = '_cf_verify';
		if( $form_id ){
			$name .= '_' . $form_id;
		}

		return $name;
	}

	/**
	 * Verify the verification nonce
	 *
	 * @since 1.5.0
	 *
	 * @param string $nonce Nonce to check
	 * @param string $form_id Form ID
	 *
	 * @return false|int
	 */
	public static function verify_nonce( $nonce, $form_id ){
		$valid = wp_verify_nonce( $nonce, self::nonce_action( $form_id ) );
		if( ! $valid ){
			/**
			 * Fires when form submission is stopped by invalid security token
			 *
			 * @since 1.5.0
			 *
			 * @param string $form_id ID of form that the
			 */
			do_action( 'caldera_forms_verification_token_failed', $form_id );
		}
		return $valid;
	}

	/**
	 * Create nonce field for use in form
	 *
	 * @since 1.5.0
	 *
	 * @param $form_id
	 *
	 * @return string
	 */
	public static function nonce_field( $form_id ){
		$nonce_field = '<input type="hidden" id="' . esc_attr( self::nonce_field_name( $form_id ) ) . '" name="' . esc_attr( self::nonce_field_name() ) . '" value="' . esc_attr( self::create_verify_nonce( $form_id ) ) . '"  data-nonce-time="' . esc_attr( time() ) . '" />';
		$nonce_field .= wp_referer_field( false );
		return $nonce_field;
	}

	/**
	 * Create nonce action with form ID attatched
	 *
	 * @since 1.5.0
	 *
	 * @param string $form_id Form ID
	 *
	 * @return string
	 */
	protected static function nonce_action( $form_id ){
		return self::$action . $form_id;
	}

}