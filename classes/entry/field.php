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
	
	/** @var  string */
	protected $value;


	/**
	 * Apply deserialization if needed to value column
	 *
	 * @since 1.3.6
	 *
	 * @param string $value Meta value
	 */
	protected function set_value( $value ){
		$this->value = maybe_unserialize( $value );
	}

}

