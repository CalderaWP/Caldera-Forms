<?php
/**
 * Container for storing Caldera_Forms_Field_Sync objects
 *
 * Should be accessed through Caldera_Forms_Field_Syncfactory
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 *
 * This is adapted from code by Tom McFarlin. Thanks Tom!
 * @see https://tommcfarlin.com/single-design-pattern-3/
 * @see https://gist.github.com/tommcfarlin/f8729d46065d1fb03b9bd3dca2cb8512#file-01-container-php
 */
class Caldera_Forms_Field_Syncs {

	/**
	 * A private reference to the instance of this class.
	 *
	 * @since 1.5.0
	 *
	 * @var    Caldera_Forms_Field_Syncs
	 */
	private static $instance;

	/**
	 * An associative array of objects that are stored in this container.
	 *
	 * @since 1.5.0
	 *
	 * @var    array
	 */
	private static $registry;

	/**
	 * Get an instance of this class.
	 *
	 * * @since 1.5.0
	 *
	 * @return Caldera_Forms_Field_Syncs A reference to a static instance of this class.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {

			self::$instance = new self();
			self::$registry = array();

		}

		return self::$instance;
	}

	/**
	 * A private container used for the pattern implementation.
	 *
	 * @since 1.5.0
	 */
	private function __construct() {}

	/**
	 * Adds an object to this container.
	 *
	 * @since 1.5.0
	 *
	 * @param  string $id       How to uniquely identify the incoming object.
	 * @param  Caldera_Forms_Field_Sync $instance A reference to the instance of the object to store.
	 * @throws Exception        If the specified key already exists in the registry or is not a Caldera_Forms_Field_Sync
	 */
	public function add( $id, $instance ) {
		if( ! is_a( $instance, 'Caldera_Forms_Field_Sync' ) ){
			throw new Exception( __( 'This container is for Caldera_Forms_Field_Sync objects only', 'caldera-forms' ) );
		}

		if ( $this->has( $id ) ) {
			throw new Exception( __( 'This class already exists in the registry.', 'caldera-forms' ) );
		}

		self::$registry[ $id ] = $instance;
	}

	/**
	 * Retrieves the object represented and stored by the incoming ID.
	 *
	 * @since 1.5.0
	 *
	 * @param  string $id The key representing the objected store in the registry.
	 * @return Caldera_Forms_Field_Sync     A reference to the instance of the object to use.
	 * @throws Exception  If the specified key already exists in the registry.
	 */
	public function get( $id ) {
		if ( ! $this->has( $id ) ) {
			throw new Exception( __( 'This class is not in the registry.', 'caldera-forms' ) );
		}

		return self::$registry[ $id ];
	}

	/**
	 * Check if this class exists in the container
	 *
	 * @since 1.5.0
	 *
	 * @param string $id identifier
	 *
	 * @return bool
	 */
	public function has( $id ){
		return array_key_exists( $id, self::$registry );
	}
}