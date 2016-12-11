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
		return wp_verify_nonce( $nonce, self::nonce_action( $form_id ) );
	}

	/**
	 * Create nonce field for use in form
	 *
	 * @since 1.5.0
	 *
	 * @param string $form_id Form ID
	 *
	 * @return string
	 */
	public static function nonce_field( $form_id ){
		return wp_nonce_field( self::nonce_action( $form_id ), '_cf_verify', true, false );
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
	protected function nonce_action( $form_id ){
		return self::$action . $form_id;
	}

}