<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Wonolog package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Inpsyde\Wonolog\Processor;

/**
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
class ProcessorsRegistry implements \Countable {

	const ACTION_REGISTER = 'wonolog.register-processors';
	const DEFAULT_NAME = 'wonolog.default-processor';

	/**
	 * @var callable[]
	 */
	private $processors = [];

	/**
	 * @var bool
	 */
	private $initialized = FALSE;

	/**
	 * @param callable $processor
	 *
	 * @param string   $name
	 *
	 * @return ProcessorsRegistry
	 */
	public function add_processor( callable $processor, $name = NULL ) {

		is_null( $name ) and $name = $this->build_name( $processor );
		if ( ! is_string( $name ) || array_key_exists( $name, $this->processors ) ) {
			return $this;
		}

		$this->processors[ $name ] = $processor;

		return $this;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function has_processor( $name ) {

		if ( is_callable( $name ) && ! is_string( $name ) ) {
			$name = $this->build_name( $name );
		}

		return is_string( $name ) && array_key_exists( $name, $this->processors );
	}

	/**
	 * @param string $name
	 *
	 * @return callable|null
	 */
	public function find( $name ) {

		if ( ! $this->initialized ) {
			$this->initialized = TRUE;

			/**
			 * Fires right before the first processor is to be registered.
			 *
			 * @param ProcessorsRegistry $processors_registry
			 */
			do_action( self::ACTION_REGISTER, $this );
		}

		if ( is_callable( $name ) && ! is_string( $name ) ) {
			$name = $this->build_name( $name );
		}

		return $this->has_processor( $name ) ? $this->processors[ $name ] : NULL;
	}

	/**
	 * @return int
	 */
	public function count() {

		return count( $this->processors );
	}

	/**
	 * @param callable $callable
	 *
	 * @return string
	 */
	private function build_name( callable $callable ) {

		if ( is_string( $callable ) ) {
			return $callable;
		}

		if ( is_object( $callable ) ) {
			/** @var object $callable */
			return spl_object_hash( $callable );
		}

		$class = $callable[ 0 ];
		if ( is_object( $class ) ) {
			return spl_object_hash( $class ) . $callable[ 1 ];
		}

		return "{$class}::{$callable[1]}";
	}
}
