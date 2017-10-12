<?php

/**
 * Entry viewer shortcode
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_API_Token {

	/**
	 * Create an API token
	 *
	 * Used as a possible way of authenticating for GET only. Don't use for POST.
	 *
	 * @since 1.5.0
	 *
	 * @param string $lowest_role The lowest user role -- IE editor -- that this token is valid for. Use "public" to make public.
	 * @param string $form_id Form ID to generate token for.
	 *
	 * @return string
	 */
	public static function make_token( $lowest_role, $form_id ){

		/**
		 * Filter secret portion of API token
		 *
		 * @since 1.5.0
		 *
		 * @param string $secret Secret thing to use
		 * @param string $form_id ID of form generating/checking token on
		 */
		$secret = apply_filters( 'caldera_forms_api_token_secret', get_option( 'caldera_forms_api_token_secret', NONCE_SALT . md5_file( __FILE__ ) ), $form_id  );
		return sha1( 'cf_viewer_' . $lowest_role . $secret  . $form_id );

	}

	/**
	 * Check a token
	 *
	 * @since 1.5.0
	 *
	 * @param string $token Token to check
	 * @param string $form_id Form ID to check based on.
	 * @param WP_User|null $user Optional. User to check for sufficient role of. Defaults to current user. If null and not logged in, only "public" is checked for.
	 *
	 * @return bool
	 */
	public static function check_token( $token, $form_id, WP_User $user = null ){
		if (  null == $user  ) {
			$user = get_user_by( 'ID', get_current_user_id() );
		}

		if( null == $user ){
			return self::verify_token( $token, 'public', $form_id );
		}

		foreach( array_merge( array_keys( caldera_forms_get_roles() ), 'public' ) as $role ){
			if( true == self::verify_token( $token, $role, $form_id ) ){
				return true;
			}
		}

		return false;

	}

	/**
	 * Check a token against a role
	 *
	 * @since 1.5.0
	 *
	 * @param string $check_token Token to check.
	 * @param string $role User role to check against.
	 * @param string $form_id ID of form this token is for.
	 *
	 * @return bool
	 */
	protected static function verify_token( $check_token, $role, $form_id ){
		return hash_equals( self::make_token( $role, $form_id ), $check_token );

	}

}