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
use Inpsyde\Wonolog\LogLevel;
use Monolog\Logger;

/**
 * Generic log data object.
 *
 * It is a value object used to pass data to wonolog.
 *
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
final class Log implements LogDataInterface {

	use LogDataTrait;

	/**
	 * @var array
	 */
	private static $filters = [
		self::MESSAGE => FILTER_SANITIZE_STRING,
		self::LEVEL   => FILTER_SANITIZE_NUMBER_INT,
		self::CHANNEL => FILTER_SANITIZE_STRING,
		self::CONTEXT => [ 'filter' => FILTER_UNSAFE_RAW, 'flags' => FILTER_REQUIRE_ARRAY ],
	];

	/**
	 * @var int
	 */
	private $level;

	/**
	 * @param \WP_Error  $error
	 * @param string|int $level   A string representing the level, e.g. `"NOTICE"` or an integer, very likely via Logger
	 *                            constants, e.g. `Logger::NOTICE`
	 * @param string     $channel Channel name
	 *
	 * @return Log
	 */
	public static function from_wp_error( \WP_Error $error, $level = Logger::NOTICE, $channel = '' ) {

		$log_level = LogLevel::instance();
		$level     = $log_level->check_level( $level ) ? : Logger::NOTICE;

		$message = $error->get_error_message();
		$context = $error->get_error_data() ?: [];

		if ( $channel ) {
			return new static( $message, $level, $channel, $context );
		}

		$channel = WpErrorChannel::for_error( $error )
			->channel();

		// Raise level for "guessed" channels
		if ( $channel === Channels::SECURITY && $level < Logger::ERROR ) {
			$level = Logger::ERROR;
		} elseif ( $channel !== Channels::DEBUG && $level < Logger::WARNING ) {
			$level = Logger::WARNING;
		}

		return new static( $message, $level, $channel, $context );
	}

	/**
	 * @param \Throwable $throwable
	 * @param int|string $level     A string representing the level, e.g. `"NOTICE"` or an integer, very likely
	 *                              via Logger constants, e.g. `Logger::NOTICE`
	 * @param string                $channel
	 * @param array                 $context
	 *
	 * @return Log
	 */
	public static function from_throwable(
		$throwable,
		$level = Logger::ERROR,
		$channel = Channels::DEBUG,
		array $context = []
	) {

		// We can't do type hint to support both PHP 7 Throwable and PHP 5 Exception
		if ( ! $throwable instanceof \Throwable && ! $throwable instanceof \Exception ) {
			$throwable = new \InvalidArgumentException(
				sprintf( '%s expects a throwable instance as first argument.', __METHOD__ )
			);
		}

		$log_level = LogLevel::instance();
		$level     = $log_level->check_level( $level ) ? : Logger::ERROR;

		$channel or $channel = Channels::DEBUG;

		$context[ 'throwable' ] = [
			'class' => get_class( $throwable ),
			'file'  => $throwable->getFile(),
			'line'  => $throwable->getLine(),
			'trace' => $throwable->getTrace(),
		];

		return new static( $throwable->getMessage(), $level, $channel, $context );
	}

	/**
	 * @param array $log_data
	 *
	 * @return Log
	 */
	public static function from_array( array $log_data ) {

		$defaults = [
			self::MESSAGE => 'Unknown error',
			self::LEVEL   => Logger::DEBUG,
			self::CHANNEL => Channels::DEBUG,
			self::CONTEXT => [],
		];

		$log_level = LogLevel::instance();
		$levels    = Logger::getLevels();

		if ( isset( $log_data[ self::LEVEL ] ) && is_string( $log_data[ self::LEVEL ] ) ) {
			$log_data[ self::LEVEL ] = $log_level->check_level( $log_data[ self::LEVEL ], $levels );
		}

		$log_data = array_filter( filter_var_array( $log_data, self::$filters ) );

		$data = array_merge( $defaults, $log_data );

		return new static(
			$data[ self::MESSAGE ],
			$data[ self::LEVEL ],
			$data[ self::CHANNEL ],
			$data[ self::CONTEXT ]
		);
	}

	/**
	 * @param string     $message
	 * @param int|string $level
	 * @param string     $channel
	 * @param array      $context
	 */
	public function __construct(
		$message = '',
		$level = Logger::DEBUG,
		$channel = Channels::DEBUG,
		array $context = []
	) {

		$this->level   = (int) $level;
		$this->message = (string) $message;
		$this->channel = (string) $channel;
		$this->context = $context;
	}

	/**
	 * @param array $log_data
	 *
	 * @return Log
	 */
	public function merge_array( array $log_data ) {

		$base = [
			self::MESSAGE => $this->message(),
			self::LEVEL   => $this->level(),
			self::CHANNEL => $this->channel(),
			self::CONTEXT => $this->context(),
		];

		return self::from_array( shortcode_atts( $base, $log_data ) );
	}

	/**
	 * @param LogDataInterface $log
	 *
	 * @return Log
	 */
	public function merge( LogDataInterface $log ) {

		$log_data = [
			self::MESSAGE => $log->message(),
			self::LEVEL   => $log->level(),
			self::CHANNEL => $log->channel(),
			self::CONTEXT => $log->context(),
		];

		return $this->merge_array( $log_data );
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return Log
	 * @throws \InvalidArgumentException
	 */
	public function with( $key, $value ) {

		if ( ! is_string( $key ) || ! array_key_exists( $key, self::$filters ) ) {
			throw new \InvalidArgumentException( 'Invalid Log key.' );
		}

		return $this->merge_array( [ $key => $value ] );
	}

	/**
	 * @inheritdoc
	 */
	public function level() {

		return $this->level;
	}
}
