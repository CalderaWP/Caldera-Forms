<?php

/**
 * Object representation of an entry meta - cf_entry_meta
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Entry_Meta extends Caldera_Forms_Entry_Object {

	/** @var  string */
	protected $entry_id;

	/** @var  string */
	protected $proccess_id;

	/** @var  string */
	protected $meta_key;

	/** @var  string */
	protected $meta_value;

	/**
	 * Apply deserialization if needed to meta_value column
	 *
	 * @since 1.3.6
	 *
	 * @param string $value Meta value
	 */
	protected function set_meta_value( $value ){
		$this->meta_value = maybe_unserialize( $value );
	}
}
