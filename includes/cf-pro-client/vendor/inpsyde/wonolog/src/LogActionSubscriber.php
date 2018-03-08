<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Wonolog package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Inpsyde\Wonolog;

use Inpsyde\Wonolog\Data\HookLogFactory;
use Inpsyde\Wonolog\Data\LogDataInterface;
use Monolog\Logger;

/**
 * Main package object, where "things happen".
 *
 * It is the object that is used to listed to `wonolog.log` actions, build log data from received arguments and
 * pass them to Monolog for the actual logging.
 *
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
class LogActionSubscriber {

	const ACTION_LOGGER_ERROR = 'wonolog.logger-error';

	/**
	 * @var Channels
	 */
	private $channels;

	/**
	 * @var HookLogFactory
	 */
	private $log_factory;

	/**
	 * @param Channels            $channels
	 * @param HookLogFactory|NULL $factory
	 */
	public function __construct( Channels $channels, HookLogFactory $factory = NULL ) {

		$this->channels    = $channels;
		$this->log_factory = $factory ? : new HookLogFactory();
	}

	/**
	 * @wp-hook wonolog.log
	 * @wp-hook wonolog.log.debug
	 * @wp-hook wonolog.log.info
	 * @wp-hook wonolog.log.notice
	 * @wp-hook wonolog.log.warning
	 * @wp-hook wonolog.log.error
	 * @wp-hook wonolog.log.critical
	 * @wp-hook wonolog.log.alert
	 * @wp-hook wonolog.log.emergency
	 */
	public function listen() {

		if ( ! did_action( Controller::ACTION_LOADED ) ) {
			return;
		}

		$logs = $this->log_factory->logs_from_hook_arguments( func_get_args(), $this->hook_level() );

		array_walk( $logs, [ $this, 'update' ] );
	}

	/**
	 * @param LogDataInterface $log
	 *
	 * @return bool
	 */
	public function update( LogDataInterface $log ) {

		if ( ! did_action( Controller::ACTION_LOADED ) || $log->level() < 1 ) {
			return FALSE;
		}

		try {

			return $this->channels
				->logger( $log->channel() )
				->addRecord( $log->level(), $log->message(), $log->context() );

		} catch ( \Throwable $e ) {
			/**
			 * Fires when the logger encounters an error.
			 *
			 * @param LogDataInterface      $log
			 * @param \Exception|\Throwable $e
			 */
			do_action( self::ACTION_LOGGER_ERROR, $log, $e );

			return FALSE;
		} catch ( \Exception $e ) {
			/** This action is documented in src/LogActionSubscriber.php */
			do_action( self::ACTION_LOGGER_ERROR, $log, $e );

			return FALSE;
		}
	}

	/**
	 * @return int
	 */
	private function hook_level() {

		$current_filter = current_filter();
		if ( $current_filter === LOG ) {
			return 0;
		}

		$parts = explode( '.', $current_filter, 3 );
		if ( isset( $parts[ 2 ] ) ) {
			return LogLevel::instance()
				->check_level( $parts[ 2 ] );
		}

		return Logger::DEBUG;
	}
}
