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

use Monolog\Handler\HandlerInterface;

/**
 * We want to load this file just once. Being loaded by Composer autoload, and being in WordPress context,
 * we have to put special care on this.
 */
if ( defined( __NAMESPACE__ . '\\LOG' ) ) {
	return;
}

const LOG = 'wonolog.log';
const LOG_PHP_ERRORS = 1;
const USE_DEFAULT_HOOK_LISTENERS = 2;
const USE_DEFAULT_HANDLER = 4;
const USE_DEFAULT_PROCESSOR = 8;
const USE_DEFAULT_ALL = 15;
const USE_DEFAULT_NONE = 0;

/**
 * @param HandlerInterface|NULL $default_handler
 * @param int                   $flags
 * @param int                   $log_hook_priority
 *
 * @return Controller
 */
function bootstrap(
	HandlerInterface $default_handler = NULL,
	$flags = USE_DEFAULT_ALL,
	$log_hook_priority = 100
) {

	static $controller;
	if ( $controller ) {
		// This should run once, but we avoid to break return type, just in case it is called more than once
		return $controller;
	}

	$controller = new Controller();
	is_int( $flags ) or $flags = USE_DEFAULT_NONE;

	if ( $flags & LOG_PHP_ERRORS ) {
		$controller->log_php_errors();
	}

	if ( $flags & USE_DEFAULT_HOOK_LISTENERS ) {
		$controller->use_default_hook_listeners();
	}

	if ( $default_handler || ( $flags & USE_DEFAULT_HANDLER ) ) {
		$controller->use_default_handler( $default_handler );
	}

	if ( $flags & USE_DEFAULT_PROCESSOR ) {
		$controller->use_default_processor();
	}

	return $controller->setup( $log_hook_priority );
}
