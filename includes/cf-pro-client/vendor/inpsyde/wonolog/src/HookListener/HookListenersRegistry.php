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

use Inpsyde\Wonolog\Data\LogDataInterface;

/**
 * Registry for hook listeners.
 *
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
class HookListenersRegistry {

	const ACTION_REGISTER = 'wonolog.register-listeners';
	const FILTER_ENABLED = 'wonolog.hook-listener-enabled';
	const FILTER_PRIORITY = 'wonolog.listened-hook-priority';

	/**
	 * @var HookListenerInterface[]
	 */
	private $listeners = [];

	/**
	 * Initialize the class, fire an hook to allow listener registration and adds the hook that will make log happen
	 */
	public static function initialize() {

		$instance = new static();

		/**
		 * Fires right before hook listeners are registered.
		 *
		 * @param HookListenersRegistry $registry
		 */
		do_action( self::ACTION_REGISTER, $instance );

		array_walk(
			$instance->listeners,
			function ( HookListenerInterface $listener ) use ( $instance ) {

				/**
				 * Filters whether to enable the hook listener.
				 *
				 * @param bool                  $enable
				 * @param HookListenerInterface $listener
				 */
				if ( apply_filters( self::FILTER_ENABLED, TRUE, $listener ) ) {
					$hooks = (array) $listener->listen_to();
					array_walk( $hooks, [ $instance, 'listen_hook' ], $listener );
				}
			}
		);

		unset( $instance->listeners );
		$instance->listeners = [];
	}

	/**
	 * @param HookListenerInterface $listener
	 *
	 * @return HookListenersRegistry
	 */
	public function register_listener( HookListenerInterface $listener ) {

		$id = (string) $listener->id();

		array_key_exists( $id, $this->listeners ) or $this->listeners[ $id ] = $listener;

		return $this;
	}

	/**
	 * Return all registered listeners.
	 *
	 * @return HookListenerInterface[]
	 */
	public function listeners() {

		return array_values( $this->listeners );
	}

	/**
	 * @param string                $hook
	 * @param int                   $i
	 * @param HookListenerInterface $listener
	 *
	 * @return bool
	 */
	private function listen_hook( $hook, $i, HookListenerInterface $listener ) {

		$is_filter = $listener instanceof FilterListenerInterface;

		if ( ! $is_filter && ! $listener instanceof ActionListenerInterface ) {
			return FALSE;
		}

		$callback = $this->hook_callback( $listener, $is_filter );

		$priority = $listener instanceof HookPriorityInterface ? (int) $listener->priority() : PHP_INT_MAX - 10;

		/**
		 * Filters the hook listener priority.
		 *
		 * @param int                   $priority
		 * @param string                $hook
		 * @param HookListenerInterface $listener
		 */
		$filtered = apply_filters( self::FILTER_PRIORITY, $priority, $hook, $listener );
		is_numeric( $filtered ) and $priority = (int) $filtered;

		return $is_filter
			? add_filter( $hook, $callback, $priority, PHP_INT_MAX )
			: add_action( $hook, $callback, $priority, PHP_INT_MAX );
	}

	/**
	 * @param FilterListenerInterface|ActionListenerInterface|HookPriorityInterface $listener
	 * @param bool                                                                  $is_filter
	 *
	 * @return callable
	 */
	private function hook_callback( $listener, $is_filter ) {

		return function () use ( $listener, $is_filter ) {

			$args = func_get_args();

			if ( ! $is_filter ) {
				$log = $listener->update( $args );
				if ( $log instanceof LogDataInterface ) {
					// Log the udate result.
					do_action( \Inpsyde\Wonolog\LOG, $log );
				}
			}

			return $is_filter ? $listener->filter( $args ) : NULL;
		};
	}
}
