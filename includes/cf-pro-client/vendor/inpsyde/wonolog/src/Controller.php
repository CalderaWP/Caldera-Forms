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

use Inpsyde\Wonolog\Handler\DefaultHandlerFactory;
use Inpsyde\Wonolog\Handler\HandlersRegistry;
use Inpsyde\Wonolog\HookListener\HookListenerInterface;
use Inpsyde\Wonolog\HookListener\HookListenersRegistry;
use Inpsyde\Wonolog\Processor\ProcessorsRegistry;
use Inpsyde\Wonolog\Processor\WpContextProcessor;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

/**
 * "Entry point" for package bootstrapping.
 *
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
class Controller {

	const ACTION_LOADED = 'wonolog.loaded';
	const ACTION_SETUP = 'wonolog.setup';
	const FILTER_DISABLE = 'wonolog.disable';

	/**
	 * Initialize Wonolog.
	 *
	 * @param int $priority
	 *
	 * @return Controller
	 */
	public function setup( $priority = 100 ) {

		if ( did_action( self::ACTION_SETUP ) ) {
			return $this;
		}

		// We use WONOLOG_DISABLE instead of WONOLOG_ENABLE so that not defined (default) means enabled.
		$disable_by_env = filter_var( getenv( 'WONOLOG_DISABLE' ), FILTER_VALIDATE_BOOLEAN );

		/**
		 * Filters whether to completely disable Wonolog.
		 *
		 * @param bool $disable
		 */
		if ( apply_filters( self::FILTER_DISABLE, $disable_by_env ) ) {
			return $this;
		}

		/**
		 * Fires right before Wonolog is set up.
		 */
		do_action( self::ACTION_SETUP );

		$processor_registry = new ProcessorsRegistry();
		$handlers_registry  = new HandlersRegistry( $processor_registry );
		$subscriber         = new LogActionSubscriber( new Channels( $handlers_registry, $processor_registry ) );
		$listener           = [ $subscriber, 'listen' ];

		add_action( LOG, $listener, $priority, PHP_INT_MAX );

		foreach ( Logger::getLevels() as $level => $level_code ) {
			// $level_code is from 100 (DEBUG) to 600 (EMERGENCY) this makes hook priority based on level priority
			add_action( LOG . '.' . strtolower( $level ), $listener, $priority + ( 601 - $level_code ), PHP_INT_MAX );
		}

		add_action( 'muplugins_loaded', [ HookListenersRegistry::class, 'initialize' ], PHP_INT_MAX );

		/**
		 * Fires right after Wonolog has been set up.
		 */
		do_action( self::ACTION_LOADED );

		return $this;
	}

	/**
	 * Tell Wonolog to use the PHP errors handler.
	 *
	 * @param int|null $error_types bitmask of error types constants, default to E_ALL | E_STRICT
	 *
	 * @return Controller
	 */
	public function log_php_errors( $error_types = NULL ) {

		static $done = FALSE;
		if ( $done ) {
			return $this;
		}

		$done = TRUE;
		is_int( $error_types ) or $error_types = E_ALL | E_STRICT;

		$controller = new PhpErrorController();
		register_shutdown_function( [ $controller, 'on_fatal', ] );
		set_error_handler( [ $controller, 'on_error' ], $error_types );
		set_exception_handler( [ $controller, 'on_exception', ] );

		// Ensure that channel Channels::PHP_ERROR error is there
		add_filter(
			Channels::FILTER_CHANNELS,
			function ( array $channels ) {

				$channels[] = Channels::PHP_ERROR;

				return $channels;
			},
			PHP_INT_MAX
		);

		return $this;
	}

	/**
	 * Tell Wonolog to use a default handler that can be passed as argument or build using settings customizable via
	 * hooks.
	 *
	 * @param HandlerInterface $handler
	 *
	 * @return Controller
	 */
	public function use_default_handler( HandlerInterface $handler = NULL ) {

		static $done = FALSE;
		if ( $done ) {
			return $this;
		}

		$done = TRUE;

		add_action(
			HandlersRegistry::ACTION_REGISTER,
			function ( HandlersRegistry $registry ) use ( $handler ) {

				$handler = DefaultHandlerFactory::with_default_handler( $handler )
					->create_default_handler();

				$registry->add_handler( $handler, HandlersRegistry::DEFAULT_NAME );
			},
			1
		);

		return $this;
	}

	/**
	 * Tell Wonolog to make given handler available to loggers with given id. If one or more channels are passed,
	 * the handler will be attached to related Monolog loggers.
	 *
	 * @param HandlerInterface $handler
	 * @param string[]         $channels
	 * @param string|NULL      $handler_id
	 *
	 * @return Controller
	 */
	public function use_handler( HandlerInterface $handler, array $channels = [], $handler_id = NULL ) {

		add_action(
			HandlersRegistry::ACTION_REGISTER,
			function ( HandlersRegistry $registry ) use ( $handler_id, $handler ) {

				$registry->add_handler( $handler, $handler_id );
			},
			1
		);

		is_null( $handler_id ) and $handler_id = $handler;

		add_action(
			Channels::ACTION_LOGGER,
			function ( Logger $logger, HandlersRegistry $handlers ) use ( $handler_id, $channels ) {

				if ( $channels === [] || in_array( $logger->getName(), $channels, TRUE ) ) {
					$logger->pushHandler( $handlers->find( $handler_id ) );
				}
			},
			10,
			2
		);

		return $this;
	}

	/**
	 * Tell Wonolog to use default log processor.
	 *
	 * @param callable $processor
	 *
	 * @return Controller
	 */
	public function use_default_processor( callable $processor = null ) {

		static $done = FALSE;
		if ( $done ) {
			return $this;
		}

		$done = TRUE;

		add_action(
			ProcessorsRegistry::ACTION_REGISTER,
			function ( ProcessorsRegistry $registry ) use ($processor) {
				$processor or $processor = new WpContextProcessor();

				$registry->add_processor( $processor, ProcessorsRegistry::DEFAULT_NAME );
			}
		);

		return $this;
	}

	/**
	 * Tell Wonolog to make given processor available to loggers with given id. If one or more channels are passed,
	 * the processor will be attached to related Monolog loggers.
	 *
	 * @param callable $processor
	 * @param string[] $channels
	 *
	 * @param          $processor_id
	 *
	 * @return Controller
	 */
	public function use_processor( callable $processor, array $channels = [], $processor_id = NULL ) {

		add_action(
			ProcessorsRegistry::ACTION_REGISTER,
			function ( ProcessorsRegistry $registry ) use ( $processor_id, $processor ) {

				$registry->add_processor( $processor, $processor_id );
			}
		);

		is_null( $processor_id ) and $processor_id = $processor;

		add_action(
			Channels::ACTION_LOGGER,
			function (
				Logger $logger,
				HandlersRegistry $handlers,
				ProcessorsRegistry $processors
			) use ( $processor_id, $channels ) {

				if ( $channels === [] || in_array( $logger->getName(), $channels, TRUE ) ) {

					$logger->pushProcessor( $processors->find( $processor_id ) );
				}
			},
			10,
			3
		);

		return $this;
	}

	/**
	 * Tell Wonolog to make given processor available to loggers with given id. If one or more channels are passed,
	 * the processor will be attached to related Monolog loggers.
	 *
	 * @param callable    $processor
	 * @param string[]    $handlers
	 * @param string|null $processor_id
	 *
	 * @return Controller
	 */
	public function use_processor_for_handlers( callable $processor, array $handlers = [], $processor_id = NULL ) {

		add_action(
			ProcessorsRegistry::ACTION_REGISTER,
			function ( ProcessorsRegistry $registry ) use ( $processor_id, $processor ) {

				$registry->add_processor( $processor, $processor_id );
			}
		);

		is_null( $processor_id ) and $processor_id = $processor;

		add_action(
			HandlersRegistry::ACTION_SETUP,
			function (
				HandlerInterface $handler,
				$handler_id,
				ProcessorsRegistry $processors
			) use ( $processor_id, $handlers ) {

				if ( $handlers === [] || in_array( $handler_id, $handlers, TRUE ) ) {
					$handler->pushProcessor( $processors->find( $processor_id ) );
				}
			},
			10,
			3
		);

		return $this;
	}

	/**
	 * Tell Wonolog to use all default hook listeners.
	 *
	 * @return Controller
	 */
	public function use_default_hook_listeners() {

		static $done = FALSE;
		if ( $done ) {
			return $this;
		}

		$done = TRUE;

		add_action(
			HookListenersRegistry::ACTION_REGISTER,
			function ( HookListenersRegistry $registry ) {

				$registry
					->register_listener( new HookListener\DbErrorListener() )
					->register_listener( new HookListener\FailedLoginListener() )
					->register_listener( new HookListener\HttpApiListener() )
					->register_listener( new HookListener\MailerListener() )
					->register_listener( new HookListener\QueryErrorsListener() )
					->register_listener( new HookListener\CronDebugListener() )
					->register_listener( new HookListener\WpDieHandlerListener() );
			}
		);

		return $this;
	}

	/**
	 * Tell Wonolog to use given hook listener.
	 *
	 * @param HookListenerInterface $listener
	 *
	 * @return Controller
	 */
	public function use_hook_listener( HookListenerInterface $listener ) {

		add_action(
			HookListenersRegistry::ACTION_REGISTER,
			function ( HookListenersRegistry $registry ) use ( $listener ) {

				$registry->register_listener( $listener );
			}
		);

		return $this;
	}
}
