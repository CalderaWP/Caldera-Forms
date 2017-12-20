<?php


namespace calderawp\calderaforms\pro;


/**
 * Class repository
 * @package calderawp\calderaforms\pro
 */

/**
 * Class Repository
 * @package calderawp\repository
 */
abstract class repository {

	/**
	 * Stores instances
	 *
	 * @since 0.0.1
	 *
	 * @var array
	 */
	protected  $items;

	/**
	 * Repository constructor.
	 *
	 * @since 0.0.1
	 *
	 * @param array $items Array of instances to add when insantiating
	 */
	public function __construct( array  $items = [] ){
		$this->items = $items;
	}

	/**
	 * Is key present?
	 *
	 * @param int|string $key
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	protected function has( $key ){
		return array_key_exists( $key, $this->items );
	}

	/**
	 * Get key
	 *
	 * @param int|string $key Key to find
	 * @param null|mixed $default Optional. Default value to return if not in collection
	 *
	 * @since 0.0.1
	 *
	 * @return mixed
	 */
	protected function get( $key, $default = null ){
		if( $this->has( $key ) ){
			return $this->items[ $key ];
		}

		return $default;
	}

	/**
	 * Set key
	 *
	 * @param int|string $key
	 * @param mixed $value
	 *
	 * @since 0.0.1
	 *
	 * @return repository
	 */
	protected function set( $key, $value ){
		$this->items[ $key ] = $value;
		return $this;
	}

}