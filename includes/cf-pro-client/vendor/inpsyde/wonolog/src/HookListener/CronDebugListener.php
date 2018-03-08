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
use Inpsyde\Wonolog\Data\NullLog;

/**
 * Listens to WP Cron requests and logs the performed actions and their performance.
 *
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
final class CronDebugListener implements ActionListenerInterface {

	use ListenerIdByClassNameTrait;

	const IS_CLI = 1;
	const IS_CRON = 2;

	/**
	 * @var bool
	 */
	private static $ran = FALSE;

	/**
	 * @var int
	 */
	private $flags = 0;

	/**
	 * @var array[]
	 */
	private $done = [];

	/**
	 * @param int $flags
	 */
	public function __construct( $flags = 0 ) {

		$this->flags = is_int( $flags ) ? $flags : 0;
	}

	/**
	 * @return string
	 */
	public function listen_to() {

		return 'wp_loaded';
	}

	/**
	 * @return bool
	 */
	public function is_cli() {

		return ( $this->flags & self::IS_CLI ) || ( defined( 'WP_CLI' ) && WP_CLI );
	}

	/**
	 * @return bool
	 */
	public function is_cron() {

		return ( $this->flags & self::IS_CRON ) || ( defined( 'DOING_CRON' ) && DOING_CRON );
	}

	/**
	 * Logs all the cron hook performed and their performance.
	 *
	 * @wp-hook  wp_loaded
	 *
	 * @param array $args
	 *
	 * @return NullLog
	 */
	public function update( array $args ) {

		if ( self::$ran ) {
			return new NullLog();
		}

		if ( $this->is_cron() || $this->is_cli() ) {
			$this->register_event_listener();
		}

		return new NullLog();
	}

	/**
	 * Logs all the cron hook performed and their performance.
	 */
	private function register_event_listener() {

		$cron_array = _get_cron_array();
		if ( ! $cron_array ) {
			return;
		}

		$hooks = array_reduce(
			$cron_array,
			function ( $hooks, $crons ) {

				return array_merge( $hooks, array_keys( $crons ) );
			},
			[]
		);

		$profile_cb = function () {

			$this->cron_action_profile();
		};

		array_walk(
			$hooks,
			function ( $hook ) use ( $profile_cb ) {

				add_action( $hook, $profile_cb, '-' . PHP_INT_MAX );
				add_action( $hook, $profile_cb, PHP_INT_MAX );
			}
		);

		self::$ran = TRUE;
	}

	/**
	 * Run before and after that any cron action ran, logging it and its performance.
	 */
	private function cron_action_profile() {

		if ( ! defined( 'DOING_CRON' ) || ! DOING_CRON ) {
			return;
		}

		$hook = current_filter();
		if ( ! isset( $this->done[ $hook ] ) ) {
			$this->done[ $hook ][ 'start' ] = microtime( TRUE );

			return;
		}

		if ( ! isset( $this->done[ $hook ][ 'duration' ] ) ) {

			$duration = number_format( microtime( TRUE ) - $this->done[ $hook ][ 'start' ], 2 );

			$this->done[ $hook ][ 'duration' ] = $duration . ' s';

			// Log the cron action performed.
			do_action(
				\Inpsyde\Wonolog\LOG,
				new Debug( "Cron action \"{$hook}\" performed.", Channels::DEBUG, $this->done[ $hook ] )
			);
		}
	}
}
