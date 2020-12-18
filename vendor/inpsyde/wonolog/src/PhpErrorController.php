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

use Monolog\Logger;
use Inpsyde\Wonolog\Data\Log;

/**
 * Handler for PHP core errors, used to log those errors mapping error types to Monolog log levels.
 *
 *
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
class PhpErrorController {

	private static $errors_level_map = [
		E_USER_ERROR        => Logger::ERROR,
		E_USER_NOTICE       => Logger::NOTICE,
		E_USER_WARNING      => Logger::WARNING,
		E_USER_DEPRECATED   => Logger::NOTICE,
		E_RECOVERABLE_ERROR => Logger::ERROR,
		E_WARNING           => Logger::WARNING,
		E_NOTICE            => Logger::NOTICE,
		E_DEPRECATED        => Logger::NOTICE,
		E_STRICT            => Logger::NOTICE,
		E_ERROR             => Logger::CRITICAL,
		E_PARSE             => Logger::CRITICAL,
		E_CORE_ERROR        => Logger::CRITICAL,
		E_CORE_WARNING      => Logger::CRITICAL,
		E_COMPILE_ERROR     => Logger::CRITICAL,
		E_COMPILE_WARNING   => Logger::CRITICAL,
	];

	/**
	 * @var array
	 */
	private static $super_globals_keys = [
		'_REQUEST',
		'_ENV',
		'GLOBALS',
		'_SERVER',
		'_FILES',
		'_COOKIE',
		'_POST',
		'_GET',
	];

	/**
	 * Error handler.
	 *
	 * @param  int        $num
	 * @param  string     $str
	 * @param  string     $file
	 * @param  int        $line
	 * @param  array|null $context
	 *
	 * @return bool
	 */
	public function on_error( $num, $str, $file, $line, $context = NULL ) {

		$level = isset( self::$errors_level_map[ $num ] )
			? self::$errors_level_map[ $num ]
			: NULL;

		$report_silenced = apply_filters(
			'wonolog.report-silenced-errors',
			error_reporting() !== 0,
			$num,
			$str,
			$file,
			$line
		);

		if ( $level === NULL || ! $report_silenced ) {
			return FALSE;
		}

		$log_context = [];
		if ( $context ) {
			$skip_keys   = array_merge( array_keys( $GLOBALS ), self::$super_globals_keys );
			$skip        = array_fill_keys( $skip_keys, '' );
			$log_context = array_filter( array_diff_key( (array) $context, $skip ) );
		}

		$log_context[ 'file' ] = $file;
		$log_context[ 'line' ] = $line;

		// Log the PHP error.
		do_action(
			\Inpsyde\Wonolog\LOG,
			new Log( $str, $level, Channels::PHP_ERROR, $log_context )
		);

		return FALSE;
	}

	/**
	 * Uncaught exception handler.
	 *
	 * @param  \Throwable $e
	 *
	 * @throws \Throwable
	 */
	public function on_exception( $e ) {

		// Log the PHP exception.
		do_action(
			\Inpsyde\Wonolog\LOG,
			new Log(
				$e->getMessage(),
				Logger::CRITICAL,
				Channels::PHP_ERROR,
				[
					'exception' => get_class( $e ),
					'file'      => $e->getFile(),
					'line'      => $e->getLine(),
					'trace'     => $e->getTraceAsString(),
				]
			)
		);

		// after logging let's reset handler and throw the exception
		restore_exception_handler();
		throw $e;
	}

	/**
	 * Checks for a fatal error, work-around for `set_error_handler` not working with fatal errors.
	 */
	public function on_fatal() {

		$last_error = error_get_last();
		if ( ! $last_error ) {
			return;
		}

		$error = array_merge( [ 'type' => -1, 'message' => '', 'file' => '', 'line' => 0 ], $last_error );

		$fatals = [
			E_ERROR,
			E_PARSE,
			E_CORE_ERROR,
			E_CORE_WARNING,
			E_COMPILE_ERROR,
			E_COMPILE_WARNING,
		];

		if ( in_array( $error[ 'type' ], $fatals, TRUE ) ) {
			$this->on_error( $error[ 'type' ], $error[ 'message' ], $error[ 'file' ], $error[ 'line' ] );
		}
	}
}
