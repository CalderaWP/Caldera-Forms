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
class Caldera_Forms_Render_FieldsJS implements JsonSerializable {

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
	 * Form instance count
	 *
	 * @since 1.5.0
	 *
	 * @var int
	 */
	protected $form_count;

	/**
	 * Caldera_Forms_Render_FieldsJS constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param array $form Form config
	 * @param int $form_count Form instance count
	 */
	public function __construct( array $form, $form_count ) {
		$this->form = $form;
		$this->form_count = $form_count;
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
					call_user_func( array( $this, $field[ 'type' ] ), $field[ 'ID' ], $field );
				}
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function jsonSerialize() {
		if( empty( $this->data ) ){
			$this->prepare_data();
		}

		return $this->get_data();
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
			'id' => $this->field_id( $field_id )
		);
	}

	protected function wysiwyg( $field_id ){
		$options = $this->wysiqyg_options( $field_id );

		$this->data[ $field_id ] = array(
			'type' => 'wysiwyg',
			'id' => $this->field_id( $field_id ),
			'options' => $options
		);
	}

	/**
	 * @param $field_id
	 *
	 * @return string
	 */
	protected function field_id( $field_id ) {
		return $field_id . '_' . $this->form_count;
	}

	/**
	 * @param $field_id
	 *
	 * @return mixed|void
	 */
	protected function wysiqyg_options( $field_id ) {
		$field = $this->form[ 'fields' ][ $field_id ];
		$options = array();
		if( ! empty( $field[ 'config' ]['language' ] ) ){
			$options[ 'lang' ] = strip_tags( $field[ 'config' ]['language' ] );
		}

		/**
		 * Filter options passed to Trumbowyg when initializing the WYSIWYG editor
		 *
		 * @since 1.5.0
		 *
		 * @see https://alex-d.github.io/Trumbowyg/documentation.html#general
		 *
		 * @param array $options Options will be empty unless language was set in UI
		 * @param array $field Field config
		 * @param array $form Form Config
		 */
		$options = apply_filters( 'caldera_forms_wysiwyg_options', $options, $field, $this->form );

		return $options;
	}

	/**
	 * Setup better_phone fields
	 *
	 * @since 1.5.0
	 *
	 * @param $field_id
	 * @param array $field Field config
	 *
	 * @return void
	 */
	protected function phone_better( $field_id, $field ){
		$this->data[ $field_id ] = array(
			'type' => 'phone_better',
			'id' => $this->field_id( $field_id ),
			'validator' => Caldera_Forms_Field_Util::better_phone_validator( $field_id, $this->form_count ),
			'options' => array(
				'autoHideDialCode' => false,
				'utilsScript' => CFCORE_URL . 'fields/phone_better/assets/js/utils.js'
			)
		);

		if( isset( $field[ 'config' ][ 'invalid_message' ] ) ){
			$this->data[ $field_id ][ 'options' ][ 'invalid' ] = $field[ 'config' ][ 'invalid_message' ];
		}else{
			$this->data[ $field_id ][ 'options' ][ 'invalid' ] = esc_html__( 'Invalid number', 'caldera-forms' );
		}


	}
}