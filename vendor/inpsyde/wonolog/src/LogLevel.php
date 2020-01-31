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

/**
 * Utility object used to build default minimum logging level based WordPress and environment settings.
 *
 * It also has a method to check the validity of a value as level identifier.
 *
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
class LogLevel {

	/**
	 * @var int
	 */
	private static $min_level;

	/**
	 * @return LogLevel
	 */
	public static function instance() {

		return new static();
	}

	/**
	 * Returns the minimum default log level based on environment variable or WordPress debug settings
	 * (in this order of priority).
	 *
	 * The level is set once per request and it is filterable.
	 *
	 * @return int
	 */
	public function default_min_level() {

		if ( self::$min_level !== null ) {
			return self::$min_level;
		}

		$env_level = getenv( 'WONOLOG_DEFAULT_MIN_LEVEL' );
		// here $min_level is a string (raw env vars are always strings) or false
		$min_level = $env_level;

		$levels = Logger::getLevels();
		$min_level = $this->check_level( $min_level, $levels );
		// Now here $min_level is surely a integer, but could be 0, and in that case we set it from WP constants
		if ( ! $min_level ) {
			$const     = defined( 'WP_DEBUG_LOG' ) ? 'WP_DEBUG_LOG' : 'WP_DEBUG';
			$min_level = ( defined( $const ) && constant( $const ) ) ? Logger::DEBUG : Logger::ERROR;
		}

		self::$min_level = $min_level;

		return $min_level;
	}

	/**
	 * In Monolog/Wonolog are 2 ways to indicate a logger level: an numeric value and level "names".
	 * Names are defined in the PSR-3 spec, integers are used in Monolog and allow for severity comparison.
	 * This method always return a numerical representation of a log level.
	 *
	 * When a name is provided, the numeric value is obtained looking into a provided array of levels.
	 * If that array is not provided `Monolog\Logger::getLevels()` is used.
	 *
	 * If there's no way to resolve the given level, `0` is returned. Any code that use this method should check that
	 * returned value is a positive number before us it.
	 *
	 * @param int|string $level
	 * @param array      $levels
	 *
	 * @return int
	 */
	public function check_level( $level, array $levels = [] ) {

		if ( ! $level ) {
			return 0;
		}

		if ( is_numeric( $level ) ) {
			return (int) $level > 0 ? (int) $level : 0;
		}

		if ( ! is_string( $level ) ) {
			return 0;
		}

		$level = strtoupper( trim( $level ) );

		$levels or $levels = Logger::getLevels();

		if ( array_key_exists( $level, $levels ) ) {
			return $levels[ $level ];
		}

		return 0;
	}
}
