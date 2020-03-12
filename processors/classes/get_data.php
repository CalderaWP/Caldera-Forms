<?php
/**
 * Prepare data from processor
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 CalderaWP LLC
 */

class Caldera_Forms_Processor_Get_Data implements Caldera_Forms_Processor_Data{

	/**
	 * The values from current submission
	 *
	 * @since 1.3.0
	 *
	 * @access private
	 *
	 * @var array|null
	 */
	private $values;

	/**
	 * The errors from current submission
	 *
	 * @since 1.3.0
	 *
	 * @access private
	 *
	 * @var array|null
	 */
	private $errors;

	/**
	 * The fields needed from this processor
	 *
	 * @since 1.3.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $fields;

	/**
	 * Contsructor for class
	 *
	 * @since 1.3.0
	 *
	 * @param array $config Proccessor config
	 * @param array $form Form config
	 * @param array $fields Fields array
	 */
	function __construct( $config, $form, $fields ) {
		if ( ! empty( $fields ) && is_array( $fields )  ) {
			$this->set_fields( $fields, $config, $form );
			$this->set_value( $config, $form );
		}

	}

	/**
	 * Validate, and set fields property
	 *
	 * @since 1.3.0
	 *
	 * @access protected
	 *
	 * @param array $fields Fields array
	 * @param array $config Proccessor config
	 * @param array $form Form config
	 */
	protected function set_fields( $fields, $config, $form ) {
		$message_pattern = __( '%s is required', 'caldera-forms' );
		$default_args = array(
			'message' => false,
			'default' => false,
			'sanatize' => 'strip_tags',
			'magic' => true,
			'required' => true,
		);


		foreach( $fields as $field  => $args ) {
			if ( ( 0 == $field || is_int( $field ) ) ) {
				if ( is_string( $args ) ) {
					$key = $field;
					$fields[ $field ] = $default_args;
					unset( $fields[ $field ] );
				}elseif ( 0 == $field || is_int( $field ) && is_array( $args ) &&isset( $args[ 'id' ]) ) {
					$key = $args[ 'id' ];
					$fields[ $key  ] = $args;
					unset( $fields[ $field ] );
				}else{
					unset( $fields[ $field ] );
					continue;
				}
			}else{
				$key = $field;
			}

			$fields[ $key ] = wp_parse_args( $args, $default_args );

			if ( isset( $config[ $key ] ) ) {
				$_field = Caldera_Forms_Field_Util::get_field_by_slug( str_replace( '%', '', $config[ $key ] ), $form );
			} else {
				$_field = null;
			}

			if ( is_array( $_field ) ) {
				$fields[ $key ][ 'config_field' ] = $_field[ 'ID' ];
			}else{
				$fields[ $key ][ 'config_field' ] = false;
			}
			if ( false === $fields[ $key][ 'message' ] ) {
				$fields[ $key ][ 'message' ] = sprintf( $message_pattern, $args[ 'label' ] );
			}

		}

		$this->fields = $fields;

	}

	/**
	 * Get values from POST data and set in the value property
	 *
	 * @since 1.3.0
	 *
	 * @access protected
	 *
	 * @param $config
	 * @param $form
	 */
	protected function set_value( $config, $form ) {
		foreach ( $this->fields as $field => $args  ) {

			if ( isset( $config[ $field ]) ) {
				if ( $args[ 'magic' ] ) {
					$value = Caldera_Forms::do_magic_tags( $config[ $field ], null, $form );
				} else {
					$value = $config[ $field ];
				}

				$field_id_passed = strpos( $value, 'fld_' );
				if ( false !== $field_id_passed ) {
					$value = Caldera_Forms::get_field_data( $value, $form );
				}

			}else{
				$value = null;
			}

			if ( ! empty( $value ) ) {
				$value = call_user_func( $args['sanatize'], $value );
			}

			/**
			 * Filter value for field of processor
			 *
			 * @since 1.3.1
			 *
			 * @param mixed $value The value of the field.
			 * @param string $field The name of the field.
			 * @param array $args Config for this field.
			 * @param array $config Processor config.
			 * @param array $form Form config.
			 */
			$value = apply_filters( 'caldera_forms_processor_value', $value, $field, $args, $config, $form );

			if ( ! empty( $value )  ) {
				$this->values[ $field ] = $value;

			}else{
				if ( $args[ 'required' ] ) {
					$this->add_error( $args[ 'message' ] );
				}else{
					$this->values[ $field ] = null;
				}
			}

		}

	}


	/**
	 * Add an error message to the errors property
	 *
	 * @since 1.3.0
	 *
	 * @param string $message Message for error
	 */
	public function add_error( $message ) {
		if ( is_null( $this->errors ) ) {
			$this->errors = array(
				'type' => 'error',
				'note' => ''
			);
		}

		$this->errors[ 'note' ] .=  $message . "<br>";

	}

	/**
	 * Get the errors
	 *
	 * @since 1.3.0
	 *
	 * @return array|null
	 */
	public function get_errors() {
		return $this->errors;

	}

	/**
	 * Get the values
	 *
	 * @since 1.3.0
	 *
	 * @return array|null
	 */
	public function get_values() {
		return $this->values;

	}

	/**
	 * Get prepared fields
	 *
	 * @since 1.3.0
	 *
	 * @return array|null
	 */
	public function get_fields() {
		return $this->fields;

	}

	/**
	 * Get one value from the processor
	 *
	 * @since 1.3.5.3
	 *
	 * @param string $field Name of field
	 * @param mixed  $default Optional. Default value to return if none set. @since 1.5.0
	 *
	 * @return mixed
	 */
	public function get_value( $field, $default = null  ){
		if( isset( $this->values[ $field ] ) ){
			return $this->values[ $field ];
		}

		return $default;
	}

}


