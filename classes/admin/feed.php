<?php

/**
 * Mini CalderaWP.com API client for feeds to admin
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Admin_Feed {

	/**
	 * URL for the API
	 *
	 * @since 1.4.2
	 *
	 * @var string
	 */
	protected static  $api_url = 'https://calderaforms.com/wp-json/calderawp_api/v2/';

	/**
	 * Get the data for the request from remote site
	 *
	 * @since 1.4.2
	 *
	 * @param string $endpoint Endpoint for request.
	 *
	 * @return string
	 */
	protected static function request_data( $endpoint ) {
		$url = self::$api_url . $endpoint;

		$request = wp_remote_get( $url );
		$data = '';
		if ( ! is_wp_error( $request ) ) {
			$data = wp_remote_retrieve_body( $request );
			$data = json_decode( $data );
		}

		return $data;

	}

	/**
	 * Get the data for the request from remote site or cache
	 *
	 * @since 1.4.2
	 *
	 * @param string $endpoint Endpoint for request.
	 * @param null|array $args Optional. Additional query vars to add to request.
	 *
	 * @return string
	 */
	protected static function get_data( $endpoint, $args = null ){
		if ( is_array( $args ) ) {
			$endpoint = add_query_arg( $args, $endpoint );
		}

		$key = md5( 'cf_feed' . $endpoint . CFCORE_VER );

		if ( false == ( $data = get_transient( $key ) ) ) {
			$data = self::request_data( $endpoint );
			foreach ( $data as &$datum ){
				$datum = self::prepare_item( $datum );
			}

			set_transient( $key, $data, HOUR_IN_SECONDS );
		}

		return $data;
	}

	/**
	 * Get featured plugins
	 *
	 * @since 1.4.2
	 *
	 * @return array|string
	 */
	public static function get_featured(){
		return self::get_data( 'products/featured' );

	}

	/**
	 * Get Caldera Forms add-on
	 *
	 * @since 1.4.2
	 *
	 * @return array|string
	 */
	public static function get_cf_addons(){
		return self::get_data( 'products/cf-addons', array( 'per_page' => 50 ) );
	}

	/**
	 * Setup each item
	 *
	 * @since 1.4.8
	 *
	 * @param stdClass $data API response item
	 *
	 * @return stdClass
	 */
	protected static function prepare_item( $data ){
		if (   isset( $data->link )  ) {
			$data->link = add_query_arg( array(
				'utm_source'   => 'wordpress',
				'utm_medium'   => 'extend-page',
				'utm_term'     => 'v' . CFCORE_VER,
				'utm_campaign' => $data->slug
			), $data->link );

			/**
			 * Specify an Affiliate ID to add to items in extend tab
			 *
			 * @since 1.4.8
			 *
			 * @param int|null ID of affiliate
			 */
			$affiliate_id = apply_filters( 'caldera_forms_affiliate_id', null );
			if ( 0 < absint( $affiliate_id ) ) {
                		$data->link = add_query_arg( 'thnx', $affiliate_id, $data->link );
			}
		}

		return $data;

	}

}
