<?php

/**
 * Object representation of an entry field - cf_form_entry_values
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Entry_Field  extends Caldera_Forms_Entry_Object {
	
	/** @var  string */
	protected $id;
	
	/** @var  string */
	protected $entry_id;
	
	/** @var  string */
	protected $field_id;
	
	/** @var  string */
	protected $slug;
	
	/** @var  string\array */
	protected $value;


	/**
	 * Apply deserialization/json_decoding if needed to value column
	 *
	 * @since 1.4.0
	 *
	 * @param string $value Value
	 */
	protected function value_set( $value ){
		if( is_array( $value ) ){
			$this->value = $value;
		}elseif( is_serialized( $value  ) ){
			$this->value = unserialize( $value );
		}elseif( 0 === strpos( $value, '{' ) && is_object( $_value = json_decode( $value ) ) ){
			$this->value = (array) $_value;
		}else{
			$this->value = $value;
		}
	}

	/**
	 * Get value and ensure is not still serialized
	 *
	 * @since 1.4.0
	 *
	 * @return array|string|int
	 */
	protected function value_get(){
		
		if ( is_serialized( $this->value ) ) {
			$this->value = unserialize( $this->value );
		} elseif ( is_string( $this->value ) && 0 === strpos( $this->value, '{' ) && is_object( $_value = json_decode( $this->value ) ) ) {
			$this->value = (array) $_value;
		}

		return $this->value;
		
	}



}

