<?php
/**
 * Form abstraction for use by API responses
 *
 * Has special handling for field collections
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_API_Form  implements  ArrayAccess {

	/**
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $form;

	/**
	 * Fields of fields that can be used in this context
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Current REST API request
	 *
	 * @since 1.5.0
	 *
	 * @var WP_REST_Request
	 */
	protected $request;

	/**
	 * Caldera_Forms_API_Form constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param array $form Form config
	 */
	public function __construct( array  $form ) {
		$this->form = $form;
		//JOSH - don't call $this->set_fields() here, or WP_REST_Request object will not be available to filter.
	}

	/**
	 * Set current REST request in object
	 *
	 * @since 1.5.0
	 *
	 * @param WP_REST_Request $request
	 */
	public function set_request( WP_REST_Request $request ){
		$this->request = $request;
	}

	/**
	 * Get the form config as an array
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function toArray(){
		return $this->form;
	}

	/**
	 * Get a field config IF it should be returned in REST API response.
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_id Field ID
	 *
	 * @return array|null
	 */
	public function get_field( $field_id ){
		$this->maybe_set_fields();
		if( $this->is_api_field( $field_id )){
			return $this->is_api_field( $field_id );
		}

		return null;

	}

	/**
	 * Check if a field should be returned in REST API response.
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_id Field ID
	 *
	 * @return bool
	 */
	public function is_api_field( $field_id ){
		$this->maybe_set_fields();
		return isset( $this->fields[ $field_id ] );
	}

	/**
	 * Get all fields that should be returned in REST API response.
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function get_fields(){
		$this->maybe_set_fields();
		return $this->fields;
	}

	/**
	 * Get all fields that should be returned in REST API response's entry list fields
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function get_entry_list_fields(){
		$entry_list_fields = Caldera_Forms_Forms::entry_list_fields( $this->form, true );
		foreach ( $entry_list_fields as $field_id => $field ){
			if( ! isset( $this->fields[ $field_id ] ) ){
				unset( $entry_list_fields[ $field_id ] );
			}

		}

		return $entry_list_fields;

	}

	/**
	 * Set fields property
	 *
	 * @since 1.5.0
	 */
	protected function set_fields(){
		$this->fields = Caldera_Forms_Forms::get_fields( $this->form, true, true );
		if( ! empty( $this->fields ) ){
			foreach ( $this->fields as $field_id => $field ){

				/**
				 * Prevent a field from being shown in API responses
				 *
				 * @since 1.5.0
				 *
				 * @param bool $show If false, field is not returned.
				 * @param string $field_id ID of field
				 * @param array $field Field config
				 * @param array $form Form config
				 * @param WP_REST_Request $request Current REST API request
				 */
				if( false == apply_filters( 'caldera_forms_api_show_field', true, $field_id, $field, $this->form, $this->request ) ){
					unset( $this->fields[ $field_id ] );
					unset( $this->form[ 'fields' ][ $field_id ] );
				}

			}

		}
	}

	/**
	 * Lazy-loader for fields property
	 *
	 * @since 1.5.0
	 */
	private function maybe_set_fields(){
		if( empty( $this->fields ) ){
			$this->set_fields();
		}

	}

	/**
	 * @inheritdoc
	 */
	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->form[] = $value;
		} else {
			$this->form[$offset] = $value;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function offsetExists($offset) {
		return isset($this->form[$offset]);
	}

	/**
	 * @inheritdoc
	 */
	public function offsetUnset($offset) {
		unset($this->form[$offset]);
	}

	/**
	 * @inheritdoc
	 */
	public function offsetGet($offset) {
		return isset($this->form[$offset]) ? $this->form[$offset] : null;
	}



}