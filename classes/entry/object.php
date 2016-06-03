<?php
/**
 * Base class for object representations of database rows
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
abstract class Caldera_Forms_Entry_Object {

	/**
	 * Translate from a stdClass object to this object type
	 *
	 * @since 1.3.6
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
	 * @since 1.3.6
	 *
	 * @param string $property Name of property
	 * @param mixed $value Property value
	 *
	 * @return bool|mixed
	 */
	public function __set( $property, $value ){
		if( method_exists( $this, $property . '_set'  ) ){
			return call_user_func( $property . '_set', $value );
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
	 * @since 1.3.6
	 *
	 * @param string $property Name of property
	 *
	 * @return mixed|null|void
	 */
	public function __get( $property ) {
		if ( property_exists( $this, $property ) ) {
			return $this->apply_filter( $property, $this->$property );
		} else {
			return null;
		}

	}

	/**
	 * Convert object to array
	 *
	 * @since 1.3.6
	 *
	 * @return array
	 */
	public function to_array( $serialize = true ){
		$vars = get_object_vars(  $this );

		foreach( $vars as $property => $value ){
			if( is_array( $value )   ){
				$value = serialize( $value );
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
	 * @since 1.3.6
	 *
	 * @param string $property Property name
	 * @param mixed $value Property value
	 *
	 * @return mixed|void
	 */
	protected function apply_filter( $property, $value ){
		/**
		 * Filter value before returning
		 *
		 * @since 1.3.6
		 *
		 * @param mixed $value Property value
		 * @param Caldera_Forms_Entry_Object $obj Current class object
		 * @param string $class Name of class
		 */
		return apply_filters( "caldera_forms_entry_$property" , $value, $this, get_class( $this ) );

	}

}
