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

use Inpsyde\Wonolog\Processor\ProcessorsRegistry;
use Monolog\Handler\HandlerInterface;

/**
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
class HandlersRegistry implements \Countable {

	const ACTION_REGISTER = 'wonolog.register-handlers';
	const ACTION_SETUP = 'wonolog.handler-setup';
	const DEFAULT_NAME = 'wonolog.default-handler';

	/**
	 * @var HandlerInterface[]
	 */
	private $handlers = [];

	/**
	 * @var ProcessorsRegistry
	 */
	private $processors_registry;

	/**
	 * @var string[]
	 */
	private $initialized;

	/**
	 * @param ProcessorsRegistry $processors_registry
	 */
	public function __construct( ProcessorsRegistry $processors_registry ) {

		$this->processors_registry = $processors_registry;
	}

	/**
	 * @param HandlerInterface $handler
	 * @param string           $name
	 *
	 * @return HandlersRegistry
	 */
	public function add_handler( HandlerInterface $handler, $name = NULL ) {

		( $name === null ) and $name = spl_object_hash( $handler );
		if ( ! is_string( $name ) || array_key_exists( $name, $this->handlers ) ) {
			return $this;
		}

		$this->handlers[ $name ] = $handler;

		return $this;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function has_handler( $name ) {

		$name instanceof HandlerInterface and $name = spl_object_hash( $name );

		return is_string( $name ) && array_key_exists( $name, $this->handlers );
	}

	/**
	 * @param string|HandlerInterface $name
	 *
	 * @return HandlerInterface|null
	 */
	public function find( $name ) {

		if ( ! is_array( $this->initialized ) ) {
			$this->initialized = [];

			/**
			 * Fires right before the first handler is to be registered.
			 *
			 * @param HandlersRegistry $handlers_registry
			 */
			do_action( self::ACTION_REGISTER, $this );
		}

		$name = $name instanceof HandlerInterface ? spl_object_hash( $name ) : (string) $name;

		if ( ! $this->has_handler( $name ) ) {
			return NULL;
		}

		$handler = $this->handlers[ $name ];

		if ( ! in_array( $name, $this->initialized, TRUE ) ) {
			$this->initialized[] = $name;

			/**
			 * Fires right after a handler has been registered.
			 *
			 * @param HandlerInterface   $handler
			 * @param string             $name
			 * @param ProcessorsRegistry $processors_registry
			 */
			do_action( self::ACTION_SETUP, $handler, $name, $this->processors_registry );
		}

		return $handler;
	}

	/**
	 * @return int
	 */
	public function count() {

		return count( $this->handlers );
	}
}
