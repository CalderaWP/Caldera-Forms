<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Wonolog package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Inpsyde\Wonolog\HookListener;

use Inpsyde\Wonolog\Channels;
use Inpsyde\Wonolog\Data\Debug;
use Inpsyde\Wonolog\Data\Error;
use Inpsyde\Wonolog\Data\LogDataInterface;
use Inpsyde\Wonolog\Data\NullLog;

/**
 * Listens to 'http_api_debug' hook to discover and log WP HTTP API errors.
 *
 * Differentiate between WP cron requests and other HTTP requests.
 *
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
final class HttpApiListener implements ActionListenerInterface {

	use ListenerIdByClassNameTrait;

	private static $http_success_codes = [
		200,
		201,
		202,
		203,
		204,
		205,
		206,
		207,
		300,
		301,
		302,
		303,
		304,
		305,
		306,
		307,
		308,
	];

	/**
	 * @inheritdoc
	 */
	public function listen_to() {

		return 'http_api_debug';
	}

	/**
	 * Log HTTP cron requests.
	 *
	 * @param array $args
	 *
	 * @return LogDataInterface
	 *
	 * @wp-hook 'http_api_debug'
	 */
	public function update( array $args ) {

		/**
		 * @var \WP_Error|array $response
		 * @var string          $context
		 * @var string          $class
		 */
		list( $response, $context, $class ) = $args;
		$http_args = isset( $args[ 3 ] ) ? $args[ 3 ] : [];
		$url       = isset( $args[ 4 ] ) ? $args[ 4 ] : '';

		if ( $this->is_error( $response ) ) {
			return $this->log_http_error( $response, $context, $class, $http_args, $url );
		}

		if ( $this->is_cron( $response, $url ) ) {
			return $this->log_cron( $response, $context, $class, $http_args, $url );
		}

		return new NullLog();
	}

	/**
	 * @param array|\WP_Error $response
	 *
	 * @return bool
	 */
	private function is_error( $response ) {

		if ( is_wp_error( $response ) ) {
			return TRUE;
		}

		if ( ! isset( $response[ 'response' ][ 'code' ] ) ) {
			return FALSE;
		}

		if ( ! is_numeric( $response[ 'response' ][ 'code' ] ) ) {
			return TRUE;
		}

		$code = (int) $response[ 'response' ][ 'code' ];

		return ! in_array( $code, self::$http_success_codes );
	}

	/**
	 * @param array|\WP_Error $response
	 * @param string          $url
	 *
	 * @return bool
	 */
	private function is_cron( $response, $url ) {

		return
			is_array( $response )
			&& basename( parse_url( $url, PHP_URL_PATH ) ) === 'wp-cron.php';
	}

	/**
	 * Log HTTP cron requests.
	 *
	 * @param \WP_Error|array $data
	 * @param string          $context
	 * @param string          $class
	 * @param array           $args
	 * @param string          $url
	 *
	 * @return Debug
	 */
	private function log_cron( $data, $context, $class, array $args = [], $url = '' ) {

		$log_context = [
			'transport'  => $class,
			'context'    => $context,
			'query_args' => $args,
			'url'        => $url,
		];

		if ( is_array( $data ) && isset( $data[ 'headers' ] ) ) {
			$log_context[ 'headers' ] = $data[ 'headers' ];
		}

		return new Debug( 'Cron request', Channels::DEBUG, $log_context );
	}

	/**
	 * Log any error for HTTP API.
	 *
	 * @param \WP_Error|array $data
	 * @param string          $context
	 * @param string          $class
	 * @param array           $args
	 * @param string          $url
	 *
	 * @return Error
	 */
	private function log_http_error( $data, $context, $class, array $args = [], $url = '' ) {

		$msg      = 'WP HTTP API Error';
		$response = is_array( $data ) && isset( $data[ 'response' ] ) && is_array( $data[ 'response' ] )
			? shortcode_atts( [ 'message' => '', 'code' => '', 'body' => '' ], $data[ 'response' ] )
			: [ 'message' => '', 'code' => '', 'body' => '' ];

		if ( is_wp_error( $data ) ) {
			$msg .= ': ' . $data->get_error_message();
		} elseif ( is_string( $response[ 'message' ] ) && $response[ 'message' ] ) {
			$msg .= ': ' . $response[ 'message' ];
		}

		$log_context = [
			'transport'  => $class,
			'context'    => $context,
			'query_args' => $args,
			'url'        => $url,
		];

		if ( $response[ 'body' ] && is_string( $response[ 'body' ] ) ) {
			$log_context[ 'response_body' ] = strlen( $response[ 'body' ] ) <= 300
				? $response[ 'body' ]
				: substr( $response[ 'body' ], 0, 300 ) . '...';
		}

		if ( array_key_exists( 'code', $response ) && is_scalar( $response[ 'code' ] ) ) {
			$msg .= " - Response code: {$response[ 'code' ]}";
			( is_array( $data ) && ! empty( $data[ 'headers' ] ) ) and $log_context[ 'headers' ] = $data[ 'headers' ];
		}

		return new Error( rtrim( $msg, '.' ) . '.', Channels::HTTP, $log_context );
	}
}
