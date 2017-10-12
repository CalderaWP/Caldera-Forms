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
	
	/** @var  string|array */
	protected $value;


	/**
	 * Set field value
	 *
	 * NOTE: Does not update in DB
	 *
	 * @since 1.5.0.7
	 *
	 * @param string|array $value New value
	 *
	 * @return array|string
	 */
	public function set_value( $value ){
		$this->value_set( $value );
		return $this->value;

	}

	/**
	 * Get field value
	 *
	 * @since 1.5.0.7
	 *
	 * @return array|int|string
	 */
	public function get_value(){
		return $this->value_get();
	}

	/**
	 * Insert an entry
	 *
	 * Factory to create and save in one go
	 *
	 * @since 1.5.2
	 *
	 * @param array|stdClass $data
	 * @param bool $return_object Optional. If false, the default, row ID is returned, if true object is returned.
	 *
	 * @return int|Caldera_Forms_Entry_Field
	 */
	public static function insert( $data, $return_object = false ){

		//Will remove disallowed keys
		if( ! is_object( $data ) ){
			$data = (object) $data;
		}

		$obj = new self( $data );
		$id = $obj->save();
		if( $return_object  ){
			return $obj;
		}else{
			return $id;
		}

	}

	/**
	 * Save the field value
	 *
	 * @since 1.5.2
	 *
	 * @return int New row ID or 0 on fail
	 */
	public function save(){
		global  $wpdb;
		$wpdb->insert( $wpdb->prefix . 'cf_form_entry_values', $this->to_array() );
		return (int) $wpdb->insert_id;

	}

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

