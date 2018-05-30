<?php


/**
 * Utility functions for Caldera Forms REST API
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_API_Util {

	/**
	 * The namespace for Caldera Forms REST API
	 *
	 * @since 1.4.4
	 *
	 * @return string
	 */
	public static function api_namespace(){
		return 'cf-api/v2';
	}

	/**
	 * The URL for Caldera Forms REST API
	 *
	 * @since 1.4.4
	 *
	 * @param string $endpoint Optional. Endpoint.
	 * @param bool $add_nonce Optional. If true, _wp_nonce is set with WP REST API nonce. Default is false
	 * @return string
	 */
	public static function url( $endpoint = '', $add_nonce = false ){
		if( ! function_exists( 'rest_url' ) ){
			return '';
		}

		$url =  rest_url( self::api_namespace() . '/' . $endpoint );
		if( $add_nonce ){
			$url = add_query_arg( '_wpnonce', self::get_core_nonce(), $url );
		}

		return $url;
	}

	public static function check_api_token( WP_REST_Request $request ){
		$allowed = false;
			if( false != ( $token = $request->get_header( 'x_cf_entry_token') ) && is_string( $request[ 'form_id' ] ) ){
				$allowed = Caldera_Forms_API_Token::check_token( $token, $request[ 'form_id' ] );
			}

		return $allowed;
	}

	/**
	 * Get the nonce for the WP REST API
	 *
	 * @since 1.5.3
	 *
	 * @return string
	 */
	public static function get_core_nonce(){
		return wp_create_nonce( 'wp_rest' );
	}

    /**
     * Given an array of field IDs and form config, reduce field IDs array to fields that form has
     *
     * @since 1.7.0
     *
     * @param array $field_ids
     * @param array $form
     * @return array
     */
	public static function validate_array_of_field_ids(array $field_ids, array $form )
    {
        $valid_fields = [];
        $form_fields = array_keys(Caldera_Forms_Forms::get_fields( $form, false ));
        if (! empty( $field_ids )) {
            foreach ($field_ids as $field_id) {
                $field_id = trim( $field_id );
                if( in_array( $field_id, $form_fields ) ){
                    $valid_fields[] = $field_id;
                }

            }
        }

        return $valid_fields;
    }

}