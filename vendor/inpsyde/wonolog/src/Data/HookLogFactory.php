<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Wonolog package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Inpsyde\Wonolog\Data;

use Inpsyde\Wonolog\Channels;
use Monolog\Logger;

/**
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
class HookLogFactory {

	/**
	 * @param array $arguments
	 * @param int   $hook_level
	 *
	 * @return LogDataInterface[]
	 */
	public function logs_from_hook_arguments( array $arguments, $hook_level = 0 ) {

		// When no arguments are passed, there's not much we can do
		if ( ! $arguments ) {
			$log = new Log( 'Unknown error.', Logger::DEBUG, Channels::DEBUG );

			return [ $this->maybe_raise_level( $hook_level, $log ) ];
		}

		// First let's see if already formed log objects were passed
		$logs = $this->extract_log_objects_in_args( $arguments, $hook_level );

		// If so, let's just return them
		if ( $logs ) {
			return $logs;
		}

		// Let's determine a log object based on first argument
		$first_arg = reset( $arguments );

		$other_args = array_values( array_slice( $arguments, 1, 2, FALSE ) );

		switch ( TRUE ) {
			case ( is_array( $first_arg ) ) :
				$logs[] = $this->maybe_raise_level( $hook_level, Log::from_array( $first_arg ) );
				break;
			case ( is_wp_error( $first_arg ) ) :
				list( $level, $channel ) = $this->level_and_channel_from_args( $other_args );
				$log    = Log::from_wp_error( $first_arg, $level, $channel );
				$logs[] = $this->maybe_raise_level( $hook_level, $log );
				break;
			case ( $first_arg instanceof \Throwable || $first_arg instanceof \Exception ) :
				list( $level, $channel ) = $this->level_and_channel_from_args( $other_args );
				$log    = Log::from_throwable( $first_arg, $level, $channel );
				$logs[] = $this->maybe_raise_level( $hook_level, $log );
				break;
			case ( is_string( $first_arg ) ) :
				list( $level, $channel ) = $this->level_and_channel_from_args( $other_args );
				$level or $level = Logger::DEBUG;
				$channel or $channel = Channels::DEBUG;
				$logs[] = $this->maybe_raise_level( $hook_level, new Log( $first_arg, $level, $channel ) );
				break;
		}

		return $logs;
	}

	/**
	 * If one or more LogData objects are passed as argument, extract all of them and return remaining objects.
	 *
	 * @param array $args
	 * @param int   $hook_level
	 *
	 * @return LogDataInterface[]
	 */
	private function extract_log_objects_in_args( array $args, $hook_level ) {

		$logs = [];
		foreach ( $args as $arg ) {
			if ( $arg instanceof LogDataInterface ) {
				$logs[] = $this->maybe_raise_level( $hook_level, $arg );
			}

		}

		return $logs;
	}

	/**
	 * @param int              $hook_level
	 * @param LogDataInterface $log
	 *
	 * @return LogDataInterface
	 */
	private function maybe_raise_level( $hook_level, LogDataInterface $log ) {

		if ( $hook_level > $log->level() ) {
			return new Log( $log->message(), $hook_level, $log->channel(), $log->context() );
		}

		return $log;
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	private function level_and_channel_from_args( array $args ) {

		if ( ! $args ) {
			return [ '', '' ];
		}

		$level   = 0;
		$channel = '';

		if ( ! empty( $args[ 0 ] ) && is_scalar( $args[ 0 ] ) ) {
			$level = $args[ 0 ];
		}

		if ( ! empty( $args[ 1 ] ) && is_string( $args[ 1 ] ) ) {
			$channel = $args[ 1 ];
		}

		return [ $level, $channel ];
	}
}
