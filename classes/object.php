<?php
/**
 * Generic object that is like stdClass but only allows get/set of defiend properties
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
abstract class Caldera_Forms_Object {

	public function __construct( stdClass $obj = null ) {
		if( null !== $obj ){
			$this->set_form_object( $obj );
		}

	}

	/**
	 * Translate from a stdClass object to this object type
	 *
	 * @since 1.4.0
	 *
	 * @param \stdClass $obj
	 */
	public function set_form_object( stdClass $obj ){
		foreach( $obj as $property => $value ){
			$this->$property = $value;
		}
	}

	/**
	 * Set allowed properties
	 *
	 * @since 1.4.0
	 *
	 * @param string $property Name of property
	 * @param mixed $value Property value
	 *
	 * @return bool|mixed
	 */
	public function __set( $property, $value ){
		$setter = $property . '_set';
		if( method_exists( $this, $setter  ) ){
			return call_user_func( array( $this, $setter ), $value );
		}elseif (  ( is_string( $value ) ||  is_numeric( $value ) ) && property_exists( $this, $property ) ){
			$this->$property = $value;
			return true;
		}else{
			return false;
		}

	}

	/**
	 * Get allowed property value
	 *
	 * @since 1.4.0
	 *
	 * @param string $property Name of property
	 *
	 * @return mixed|null|void
	 */
	public function __get( $property ) {
		$getter = $property . '_get';
		if( method_exists( $this, $getter  ) ){
			return call_user_func( array( $this, $getter ) );
		}
		if ( property_exists( $this, $property ) ) {
			return $this->apply_filter( $property, $this->$property );
		} else {
			return null;
		}

	}

	/**
	 * Convert object to array
	 *
	 * @since 1.4.0
	 *
	 * @param bool $serialize_arrays Optional. To return arrays serialized. If true, arrays are serialized, if false, serialized data is unserialized. Default is true.
	 *
	 * @return array
	 */
	public function to_array(  $serialize_arrays = true ){
		$vars = get_object_vars(  $this );

		foreach( $vars as $property => $value ){
			if( is_array( $value )   ){
				if ( $serialize_arrays ) {
					$value = serialize( $value );
				}
			}

			if( ! $serialize_arrays && is_serialized( $value ) ){
				$value = unserialize( $value );
			}

			$vars[ $property ] = $value;
		}

		return $vars;

	}

	/**
	 * Filter value
	 *
	 * Called whenever property is accessed via __get()
	 *
	 * @since 1.4.0
	 *
	 * @param string $property Property name
	 * @param mixed $value Property value
	 *
	 * @return mixed|void
	 */
	public function apply_filter( $property, $value ){
		$prefix = $this->get_prefix();
		$filter_name = 'caldera_forms_' . $prefix . '_' . $property;

		/**
		 * Filter value before returning
		 *
		 * @since 1.4.0
		 *
		 * @param mixed $value Property value
		 * @param Caldera_Forms_Object $obj Current class object
		 * @param string $class Name of class
		 */
		return apply_filters( $filter_name, $value, $this, get_class( $this ) );

	}

	/**
	 * Get prefix to use in parents.
	 *
	 * Used to form filter for getters. Better to ovveride and hardcode.
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	protected function get_prefix(){
		$class = explode( '_', get_class( $this ) );
		end( $class );
		return  key( $class );

	}

}
