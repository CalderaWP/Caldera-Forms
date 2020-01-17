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
use Inpsyde\Wonolog\Data\Error;

/**
 * Looks a wp_die() and try to find and log db errors.
 *
 * @package wonolog
 */
final class WpDieHandlerListener implements FilterListenerInterface {

	use ListenerIdByClassNameTrait;

	/**
	 * @inheritdoc
	 */
	public function listen_to() {

		return [ 'wp_die_ajax_handler', 'wp_die_xmlrpc_handler', 'wp_die_handler' ];
	}

	/**
	 * Run as handler for wp_die() and checks if it was called by
	 * wpdb::bail() or wpdb::print_error() so something gone wrong on db.
	 * After logging error, the method calls original handler.
	 *
	 * @wp-hook wp_die_ajax_handler
	 * @wp-hook wp_die_handler
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function filter( array $args ) {

		$handler = $args ? reset( $args ) : NULL;

		if ( ! $handler || ! is_callable( $handler ) || ! $this->stacktrace_has_db_error() ) {
			return $handler;
		}

		return function ( $message, $title = '', $args = [] ) use ( $handler ) {

			$msg                = filter_var( $message, FILTER_SANITIZE_STRING );
			$context            = $args;
			$context[ 'title' ] = $title;

			// Log the wp_die() error message.
			do_action( \Inpsyde\Wonolog\LOG, new Error( $msg, Channels::DB, $context ) );

			return $handler( $message, $title, $args );
		};
	}

	/**
	 * @return array
	 */
	private function stacktrace_has_db_error() {

		$stacktrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 6 );

		return array_filter( $stacktrace, [ $this, 'stacktrace_item_has_db_error' ] );
	}

	/**
	 * @param array $item
	 *
	 * @return bool
	 */
	private function stacktrace_item_has_db_error( $item ) {

		return
			isset( $item[ 'function' ] )
			&& isset( $item[ 'class' ] )
			&& ( $item[ 'function' ] === 'bail' || $item[ 'function' ] === 'print_error' )
			&& $item[ 'class' ] === 'wpdb';
	}
}
