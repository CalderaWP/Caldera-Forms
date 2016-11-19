<?php
/**
 * Magic tag sync implementation for HTML fields
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Field_SyncHTML  extends Caldera_Forms_Field_Sync {

	/**
	 * Bind field list as field data-field attrs
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $attr_binds;

	/**
	 * Quoted bind field list
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $bind_fields;

	/**
	 * Get the date-field formatted binds
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function get_attr_binds(){
		if ( is_array( $this->attr_binds ) ) {
			return $this->attr_binds;
		} else {
			return array();
		}
	}

	/**
	 * Get the quoted binds list
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function get_bind_fields(){
		if( is_array( $this->bind_fields ) ){
			return $this->bind_fields;
		}else{
			return array();
		}
	}

	/**
	 * @inheritdoc
	 * @since 1.5.0
	 */
	protected function add_bind( $key_id ) {
		$this->binds[] = $key_id;
		$this->attr_binds[] = '[data-field="'.$key_id.'"]';
		$this->bind_fields[] = '"'.$key_id.'"';
	}



}