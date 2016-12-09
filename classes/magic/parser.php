<?php

/**
 * Abstract class that all magic tag parsing classes should extend
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
abstract class Caldera_Forms_Magic_Parser {


	/**
	 * Form config
	 *
	 * @since 1.4.6
	 *
	 * @var array
	 */
	protected $form;

	/**
	 * The data to use for parsing
	 *
	 * @since 1.4.6
	 *
	 * @var array
	 */
	protected $data;


	/**
	 * Rendered magic tag
	 *
	 * @since 1.4.6
	 *
	 * @var string|null
	 */
	protected $tag;

	/**
	 * Caldera_Forms_Magic_Parser constructor.
	 *
	 * @since 1.4.6
	 *
	 * @param array $form Form config
	 * @param array|null $data Optional (depending on parent) data to use to parse
	 */
	public function __construct( array $form, array $data = null ) {
		$this->form = $form;
		$this->data = $data;

	}

	/**
	 * Get rendered magic tag
	 *
	 * @since 1.4.6
	 *
	 * @return null|string
	 */
	public function get_tag(){
		if( null == $this->tag ){
			$this->parse();
		}

		return $this->tag;
	}

	/**
	 * Parse tag
	 *
	 * Set tag property
	 *
	 * @since 1.4.6
	 */
	protected function parse(){
		//SHOULD OVVERIDE IN PARENT CLASS
		//Would have declared abstract, but 5.2...
		$this->tag = '';
	}

}