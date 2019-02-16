<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Wonolog package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Inpsyde\Wonolog\Processor;

/**
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
class WpContextProcessor {

	/**
	 * @var bool
	 */
	private $is_rest_request;

	/**
	 * @param array $record The complete log record containing 'message', 'context'
	 *                      'level', 'level_name', 'channel', 'datetime' and 'extra'
	 *
	 * @return array
	 */
	public function __invoke( array $record ) {

		$record[ 'extra' ][ 'wp' ] = [
			'doing_cron' => defined( 'DOING_CRON' ) && DOING_CRON,
			'doing_ajax' => defined( 'DOING_AJAX' ) && DOING_AJAX,
			'is_admin'   => is_admin(),
		];

		// When doing_rest() returns false before 'parse_request' we can't be sure request will not be recognized as a
		// REST request later and so we don't say `doing_rest` is false if we are not sure about that.
		$doing_rest = $this->doing_rest();
		if ( $doing_rest || did_action( 'parse_request' ) ) {
			$record[ 'extra' ][ 'wp' ][ 'doing_rest' ] = $doing_rest;
		}

		if ( did_action( 'init' ) ) {
			$record[ 'extra' ][ 'wp' ][ 'user_id' ] = get_current_user_id();
		}

		if ( is_multisite() ) {
			$record[ 'extra' ][ 'wp' ][ 'ms_switched' ] = ms_is_switched();
			$record[ 'extra' ][ 'wp' ][ 'site_id' ]     = get_current_blog_id();
			$record[ 'extra' ][ 'wp' ][ 'network_id' ]  = get_current_network_id();
		}

		return $record;
	}

	/**
	 * @return bool
	 */
	private function doing_rest() {

		if ( isset( $this->is_rest_request ) ) {
			return $this->is_rest_request;
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$this->is_rest_request = TRUE;

			return TRUE;
		}

		if ( get_option( 'permalink_structure' ) && empty( $GLOBALS[ 'wp_rewrite' ] ) ) {
			// Rewrites are used, but it's too early for global rewrites be there.
			// Let's instantiate it, or `get_rest_url()` will fail.
			// This is exactly how WP does it, so it will do nothing bad. In worst case, WP will override it.
			$GLOBALS[ 'wp_rewrite' ] = new \WP_Rewrite();
		}

		$rest_url              = set_url_scheme( get_rest_url() );
		$current_url           = set_url_scheme( add_query_arg( [] ) );
		$this->is_rest_request = strpos( $current_url, set_url_scheme( $rest_url ) ) === 0;

		return $this->is_rest_request;
	}
}
