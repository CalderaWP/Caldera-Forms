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
	 * Holds instance of credit card hasher class
	 *
	 * @since 1.5.0.7
	 *
	 * @var Caldera_Forms_Field_Credit
	 */
	protected $credit_card_hasher;

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

		/**
		 * Filter data for use by magic parsers extending the Caldera_Forms_Magic_Parser class sunch as summary
		 *
		 * @since 1.5.2
		 *
		 * @param array $data Data to use
		 * @param array $form Form config
		 */
		$this->data = apply_filters( 'caldera_forms_magic_parser_data', $data, $this->form );

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

	/**
	 * Getter for field data
	 *
	 * @since 1.5.0.7
	 *
	 * @param string $field_id Field ID to get
	 *
	 * @return mixed|null
	 */
	protected function get_field_value( $field_id ){
		if( ! is_array( $this->data ) ){
			$this->data = Caldera_Forms::get_submission_data( $this->form );
		}

		$value = null;
		if( isset( $this->data[ $field_id ]  ) ){
			$value = $this->data[ $field_id ];
		}

		//Add filter?
		return $value;
	}

	/**
	 * Get credit card hasher class
	 *
	 * @since 1.5.0.7
	 *
	 * @return Caldera_Forms_Field_Credit
	 */
	protected function get_credit_card_hasher(){
		if( null === $this->credit_card_hasher ){
			$this->credit_card_hasher = new Caldera_Forms_Field_Credit;
		}

		return $this->credit_card_hasher;

	}
}