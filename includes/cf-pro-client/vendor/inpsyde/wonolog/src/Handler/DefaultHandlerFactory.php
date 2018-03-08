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

use Inpsyde\Wonolog\LogLevel;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;

/**
 * Wonolog builds a default handler if no custom handler is provided.
 * This class has the responsibility to create an instance of this default handler using sensitive defaults
 * and allowing configuration via hooks and environment variables.
 *
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
class DefaultHandlerFactory {

	const FILTER_FOLDER = 'wonolog.default-handler-folder';
	const FILTER_FILENAME = 'wonolog.default-handler-filename';
	const FILTER_DATE_FORMAT = 'wonolog.default-handler-date-format';
	const FILTER_BUBBLE = 'wonolog.default-handler-bubble';
	const FILTER_USE_LOCKING = 'wonolog.default-handler-use-locking';

	/**
	 * @var HandlerInterface
	 */
	private $default_handler;

	/**
	 * @param HandlerInterface|NULL $handler
	 *
	 * @return static
	 */
	public static function with_default_handler( HandlerInterface $handler = NULL ) {

		$instance                  = new static();
		$instance->default_handler = $handler;

		return $instance;
	}

	/**
	 * @return HandlerInterface
	 */
	public function create_default_handler() {

		if ( $this->default_handler ) {
			return $this->default_handler;
		}

		$this->default_handler = $this->create_default_handler_from_configs();

		return $this->default_handler;
	}

	/**
	 * @return HandlerInterface
	 */
	private function create_default_handler_from_configs() {

		$folder = $this->handler_folder();

		if ( ! $folder ) {
			return new NullHandler();
		}

		list( $filename_format, $date_format ) = $this->handler_file_info();

		$log_level = LogLevel::instance();

		try {
			/**
			 * Filters whether messages bubble up the stack.
			 *
			 * @param bool $bubble
			 */
			$bubble = (bool) apply_filters( self::FILTER_BUBBLE, TRUE );

			/**
			 * Filters whether to try to lock the log file before writing.
			 *
			 * @param bool $use_locking
			 */
			$use_locking = apply_filters( self::FILTER_USE_LOCKING, TRUE );

			$handler = new DateBasedStreamHandler(
				"{$folder}/{$filename_format}",
				$date_format,
				$log_level->default_min_level(),
				$bubble,
				$use_locking
			);
		} catch ( \Exception $e ) {
			$handler = new NullHandler();
		}

		return $handler;
	}

	/**
	 * @return string
	 */
	private function handler_folder() {

		$folder = getenv( 'WONOLOG_DEFAULT_HANDLER_ROOT_DIR' );

		if ( ! $folder && defined( 'WP_CONTENT_DIR' ) ) {
			$folder = rtrim( WP_CONTENT_DIR, '\\/' ) . '/wonolog';
		}

		/**
		 * Filters the handler folder to use.
		 *
		 * @param string $folder
		 */
		$folder = apply_filters( self::FILTER_FOLDER, $folder );
		is_string( $folder ) or $folder = '';

		if ( $folder ) {
			$folder = rtrim( wp_normalize_path( $folder ), '/' );
			wp_mkdir_p( $folder ) or $folder = '';
		}

		$this->maybe_create_htaccess( $folder );

		return $folder;
	}

	/**
	 * @return array
	 */
	private function handler_file_info() {

		/**
		 * Filters the handler filename format to use.
		 *
		 * @param string $format
		 */
		$filename_format = apply_filters( self::FILTER_FILENAME, '{date}.log' );
		is_string( $filename_format ) and $filename_format = ltrim( $filename_format, '\\/' );

		/**
		 * Filters the handler date format to use.
		 *
		 * @param string $format
		 */
		$date_format = apply_filters( self::FILTER_DATE_FORMAT, 'Y/m/d' );

		return [ $filename_format, $date_format ];
	}

	/**
	 * When the log root folder is inside WordPress content folder, the logs are going to be publicly accessible, and
	 * that is in best case a privacy leakage issue, in worst case a security threat.
	 * We try to write an .htaccess file to prevent access to them.
	 * This guarantees nothing, because .htaccess can be ignored depending web server in use and its configuration,
	 * but at least we tried.
	 * To configure a custom log folder outside content folder is also highly recommended in documentation.
	 *
	 * @param string $folder
	 *
	 * @return string
	 */
	private function maybe_create_htaccess( $folder ) {

		if (
			! $folder
			|| ! is_dir( $folder )
			|| ! is_writable( $folder )
			|| file_exists( "{$folder}/.htaccess" )
			|| ! defined( 'WP_CONTENT_DIR' )
		) {
			return $folder;
		}

		$target_dir  = realpath( $folder );
		$content_dir = realpath( WP_CONTENT_DIR );

		// Sorry, we can't allow logs to be put straight in content folder. That's too dangerous.
		if ( $target_dir === $content_dir ) {
			$target_dir .= DIRECTORY_SEPARATOR . 'wonolog';
		}

		// If target dir is outside content dir, its security is up to user.
		if ( strpos( $target_dir, $content_dir ) !== 0 ) {
			return $target_dir;
		}

		// Let's disable error reporting: too much file operations which might fail, nothing can log them, and package
		// is fully functional even if failing happens. Silence looks like best option here.
		set_error_handler( '__return_true' );

		$handle = fopen( "{$folder}/.htaccess", 'w' );

		if ( $handle && flock( $handle, LOCK_EX ) && fwrite( $handle, "Deny from all\n" ) ) {
			flock( $handle, LOCK_UN );
			chmod( "{$folder}/.htaccess", 0444 );
		}

		fclose( $handle );

		restore_error_handler();

		return $target_dir;
	}
}
