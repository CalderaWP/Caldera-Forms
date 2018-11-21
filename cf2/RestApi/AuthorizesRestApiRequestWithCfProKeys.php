<?php


namespace calderawp\calderaforms\cf2\RestApi;


trait AuthorizesRestApiRequestWithCfProKeys
{

	/**
	 * Check public key and token from request param or headers
	 *
	 * @since 1.8.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return null|string
	 */
	public function checkKeys(\WP_REST_Request $request ){
		$public = $this->getPublic($request);
		$token = $this->getToken($request);

		if( ! is_string( $public ) || ! is_string( $token )  ){
			return false;
		}
		return caldera_forms_pro_compare_to_saved_keys( $public, $token );
	}

	/**
	 * Get public key from request param or headers
	 *
	 * @since 1.8.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return null|string
	 */
	public function getPublic(\WP_REST_Request $request ){
		if(  ! empty( $request[ 'public']) && is_string( $request[ 'public' ] ) ){
			return $request[ 'public' ];
		}
		if( ! is_null( $request->get_header( 'X-CS-PUBLIC' ) )  ){
			return $request->get_header( 'X-CS-PUBLIC' );
		}
	}

	/**
	 * Get token from request param or headers
	 *
	 * @since 1.8.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return null|string
	 */
	public function getToken(\WP_REST_Request $request ){
		if(  ! empty( $request[ 'token']) && is_string( $request[ 'token' ] ) ){
			return $request[ 'token' ];
		}
		if( ! is_null( $request->get_header( 'X-CS-TOKEN' ) )  ){
			return $request->get_header( 'X-CS-TOKEN' );
		}
	}
}