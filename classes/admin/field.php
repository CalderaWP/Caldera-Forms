<?php

/**
 * Object representation of an admin UI field settings
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_Admin_Field extends Caldera_Forms_Object{

	/** @var  string */
	protected $type;

	/** @var  string */
	protected $name;

	/** @var  string */
	protected $label;

	/** @var  array */
	protected $options;

	/** @var  array */
	protected $args;

	/**
	 * Handler for $this->description __get()
	 *
	 * @since 1.5.1
	 *
	 * @return string
	 */
	protected function description_get(){
		if( ! is_string( $this->args[ 'description' ] ) ){
			return '';
		}
		return $this->args[ 'description' ];
	}

	/**
	 * Handler for $this->description __set()
	 *
	 * @since 1.5.1
	 *
	 * @param array $options Options for a for a select field
	 *
	 * @return bool
	 */
	protected function options_set( $options ){
		if( is_array( $options ) ){
			$this->options = $options;
			return true;
		}
		return false;
	}

	/**
	 * Handler for $this->options  __get()
	 *
	 * @since 1.5.1
	 *
	 * @return array|string
	 */
	protected function options_get(){
		if( empty( $this->options ) ){
			return array();
		}

		return $this->options;
	}

	/**
	 * Handler for $this->args __get()
	 *
	 * @since 1.5.1
	 *
	 * @return array
	 */
	protected function args_get(){
		if( ! is_array( $this->args ) ){
			$this->args = array();
		}

		return wp_parse_args( $this->args,
			array(
				'description' => '',
				'block'       => false,
				'magic'       => false,
				'classes'     => ''
			)
		);
	}


}