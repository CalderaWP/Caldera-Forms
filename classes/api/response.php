<?php

/**
 * Response object all non-error REST API requests should return
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_API_Response extends \WP_REST_Response {

	/**
	 * @inheritdoc
	 *
	 * @since 1.4.4
	 */
	public function __construct( $data = null, $status = 200, $headers = array() ) {
		parent::__construct( $data, $status, $headers );
		if ( empty( $data ) ) {
			$this->set_status( 404 );
		}

	}

	public function set_total_header( $total ){
		$this->header( 'X-CF-API-TOTAL', (int) $total );
	}

	public function set_total_pages_header( $total_pages ){
		$this->header( 'X-CF-API-TOTAL-PAGES', (int) $total_pages );
	}


}