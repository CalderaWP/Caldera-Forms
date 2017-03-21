<?php


/**
 * Entry token abstraction -- used for allowing cf_ee edits/ entry editor edits
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_Entry_Token {
	/**
	 * Check a token
	 *
	 * @since 1.5.0.6
	 *
	 * @param string $test_token Encoded token to test
	 * @param string $entry_id ID of entry to test
	 * @param string $form_id ID of form to test
	 *
	 * @return bool|WP_Error
	 */
	public static function verify_token( $test_token, $entry_id, $form_id ){
		$compare_token = self::make_test_token( $entry_id, $form_id );
		if( ! hash_equals(  $compare_token, $test_token ) ){
			return new WP_Error( 'error', __( "Permission denied.", 'caldera-forms' ) );

		}

		return true;

	}

	/**
	 * Create entry edit token for stored entry
	 *
	 * @since 1.5.0.6
	 *
	 * @param string $entry_id ID of entry
	 * @param array $form Form config
	 *
	 * @return string
	 */
	public static function  create_entry_token( $entry_id, $form ){
		$details = Caldera_Forms::get_entry_detail( $entry_id, $form );
		return self::make_token( $entry_id, $form[ 'ID' ], $details[ 'datestamp' ],  $details[ 'user_id' ]  );
	}

	/**
	 * Check a token vs a what it should be.
	 *
	 * @since 1.5.0.6
	 *
	 * @param string $test_token Encoded token to test
	 * @param string $compare_token Token to compare against
	 *
	 * @return bool|WP_Error
	 */
	protected static function check_token( $test_token, $compare_token ){
		if( ! hash_equals(  $compare_token, $test_token ) ){
			return new WP_Error( 'error', __( "Permission denied.", 'caldera-forms' ) );

		}
		return true;
	}

	/**
	 * Make token to test against
	 *
	 * Uses current user, not saved user
	 *
	 * @since 1.5.0.6
	 *
	 * @param int $entry_id ID of entry
	 * @param array $form Form config
	 *
	 * @return string
	 */
	protected static function make_test_token( $entry_id, $form ){
		$details = Caldera_Forms::get_entry_detail( $entry_id, $form );
		return self::make_token( $entry_id, $form[ 'ID' ], $details[ 'datestamp' ],  get_current_user_id()  );
	}


	/**
	 * Make entry token
	 *
	 * @since 1.5.0.6
	 *
	 * @param int $entry_id ID of entry ID of stored entry
	 * @param string $form_id Form ID
	 * @param string $datestamp Datestamp of entry
	 * @param int $user_id ID of user who created entry
	 *
	 * @return string
	 */
	protected static function make_token( $entry_id, $form_id, $datestamp, $user_id ){
		$token_array = array(
			'id'        => (int) $entry_id,
			'datestamp' => $datestamp,
			'user_id'   => (int) $user_id,
			'form_id'   => $form_id
		);

		return sha1( json_encode( $token_array ) );

	}
}