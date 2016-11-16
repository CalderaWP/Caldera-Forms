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
	 * @return string
	 */
	public static function url( $endpoint = '' ){
		return rest_url( self::api_namespace() . '/' . $endpoint );
	}

}