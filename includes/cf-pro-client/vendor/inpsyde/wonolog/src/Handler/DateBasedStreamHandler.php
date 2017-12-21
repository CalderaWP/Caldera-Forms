<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Wonolog package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Inpsyde\Wonolog\Handler;

use Monolog\Handler\AbstractHandler;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Similar to Monolog RotatingFileHandler, this class overcomes RotatingFileHandler too stringent date format
 * enforcement.
 *
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
final class DateBasedStreamHandler extends AbstractProcessingHandler {

	const VALID_DATE_PLACEHOLDERS = 'dDjlNwzWFMmntoYy';

	/**
	 * @var StreamHandler[]
	 */
	private $handlers = [];

	/**
	 * @var string|callable
	 */
	private $file_format;

	/**
	 * @var string
	 */
	private $date_format;

	/**
	 * @var bool
	 */
	private $locking;

	/**
	 * @param string|callable $file_format
	 * @param string          $date_format
	 * @param bool|int        $level
	 * @param bool            $bubble
	 * @param bool            $locking
	 */
	public function __construct( $file_format, $date_format, $level = Logger::DEBUG, $bubble = TRUE, $locking = TRUE ) {

		if ( ! $this->check_file_format( $file_format ) || ! $this->check_date_format( $date_format ) ) {
			throw new \InvalidArgumentException( 'Invalid file name or date format for ' . __CLASS__ );
		}

		$this->file_format = $file_format;
		$this->date_format = (string) $date_format;
		$this->locking     = (bool) $locking;

		parent::__construct( $level, $bubble );
	}

	/**
	 * @param array $record
	 *
	 * @return StreamHandler
	 */
	public function stream_handler_for_record( array $record ) {

		list( $filename, $file_permissions ) = $this->file_name_for_record( $record );

		if ( isset( $this->handlers[ $filename ] ) ) {
			return $this->handlers[ $filename ];
		}

		$this->close();

		$handler = new StreamHandler(
			$filename,
			$this->getLevel(),
			$this->getBubble(),
			$file_permissions,
			$this->locking
		);

		$this->handlers[ $filename ] = $handler;

		return $handler;
	}

	/**
	 * @inheritdoc
	 */
	protected function write( array $record ) {

		$this->stream_handler_for_record( $record )
			->write( $record );
	}

	/**
	 * @inheritdoc
	 */
	public function close() {

		$this->handlers and array_walk(
			$this->handlers,
			function ( AbstractHandler $handler ) {

				$handler->close();
			}
		);

		unset( $this->handlers );
		$this->handlers = [];
	}

	/**
	 * @param string $file_format
	 *
	 * @return bool
	 */
	private function check_file_format( $file_format ) {

		if ( is_callable( $file_format ) ) {
			return TRUE;
		}

		return
			is_string( $file_format )
			&& filter_var( $file_format, FILTER_SANITIZE_URL ) === $file_format
			&& substr_count( $file_format, '{date}' ) === 1;
	}

	/**
	 * Checks that a date format contains only valida `date()` placeholder and valid separators, but not only separators
	 *
	 * @param $date_format
	 *
	 * @return bool
	 */
	private function check_date_format( $date_format ) {

		if ( ! is_string( $date_format ) ) {
			return FALSE;
		}

		$date_format_no_sep = str_replace( [ '-', '_', '/', '.' ], '', $date_format );

		if ( ! $date_format_no_sep ) {
			return FALSE;
		}

		return rtrim( $date_format_no_sep, self::VALID_DATE_PLACEHOLDERS ) === '';
	}

	/**
	 * @param array $record
	 *
	 * @return array
	 */
	private function file_name_for_record( array $record ) {

		$file_format = $this->file_format;

		if ( is_callable( $file_format ) ) {
			$file_format = $file_format( $record );
			is_callable( $file_format ) and $file_format = NULL;
			$this->check_file_format( $file_format ) or $file_format = '{date}.log';
		}

		$timestamp = $this->record_timestamp( $record );

		$filename = str_replace( '{date}', date( $this->date_format, $timestamp ), $file_format );
		if ( ! filter_var( filter_var( $filename, FILTER_SANITIZE_URL ), FILTER_SANITIZE_URL ) ) {
			throw new \InvalidArgumentException( 'Invalid file name format or date format for ' . __CLASS__ );
		}

		$dir = @dirname( $filename );
		if ( ! $dir || ! wp_mkdir_p( $dir ) ) {
			throw new \RuntimeException( 'It was not possible to create folder ' . $dir );
		}

		$stat      = @stat( $dir );
		$dir_perms = isset( $stat[ 'mode' ] ) ? $stat[ 'mode' ] & 0007777 : 0755;

		return [ $filename, $dir_perms ];
	}

	/**
	 * @param array $record
	 *
	 * @return int
	 */
	private function record_timestamp( array $record ) {

		static $old_timestamp;
		$old_timestamp or $old_timestamp = strtotime( '1 month ago' );

		$timestamp = empty( $record[ 'datetime' ] ) ? NULL : $record[ 'datetime' ];

		if ( is_string( $timestamp ) ) {
			$timestamp = ctype_digit( $timestamp ) ? (int) $timestamp : @strtotime( $timestamp );
			( is_int( $timestamp ) && $timestamp ) or $timestamp = NULL;
		}

		if ( $timestamp instanceof \DateTimeInterface ) {
			$timestamp = $timestamp->getTimestamp();
		}

		$timestamp_now = time();

		// We don't really have a way to see if an integer is a timestamp, but if it's a number that's bigger than
		// 1 month ago timestamp and lower than current timestamp, chances are it is a valid one.
		if ( is_int( $timestamp ) && $timestamp > $old_timestamp && $timestamp <= $timestamp_now ) {
			return $timestamp;
		}

		return $timestamp_now;

	}
}
