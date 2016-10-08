<?php
/**
 * Prepares fieldjs config
 *
 * Will be placed in .cf-fieldjs-config for each field
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Render_FieldsJS {

	/**
	 * Form config
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $form;

	/**
	 * Prepared field data
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Caldera_Forms_Render_FieldsJS constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param array $form Form config
	 */
	public function __construct( array $form ) {
		$this->form = $form;
		$this->data = array();
	}

	/**
	 * Prepare data for each field
	 *
	 * @since 1.5.0
	 */
	public function prepare_data(){

		if( ! empty( $this->form[ 'fields' ] ) ){
			foreach( $this->form[ 'fields' ] as $field ){
				if( method_exists( $this, $field[ 'type' ] ) ){
					call_user_func( array( $this, $field[ 'type' ] ), $field[ 'ID' ] );
				}
			}
		}
	}

	/**
	 * Get prepared data
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function get_data(){
		return $this->data;
	}

	/**
	 * Callback for processing button data
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_id Field ID
	 */
	protected function button( $field_id ){
		$this->data[ $field_id ] = array(
			'type' => 'button',
			'id' => $field_id
		);
	}
}