<?php

/**
 * Handles file uploading from file fields
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 CalderaWP LLC
 */
class Caldera_Forms_Field_Util {

	/**
	 *  Get a field's type
	 *
	 * @since 1.4.4
	 *
	 * @param string|array $field Field ID or config array
	 * @param array|null $form Optional. Form config. MUST USE if $field is ID not config array
	 *
	 * @return bool
	 */
	public static function get_type( $field, array  $form = null ){
		if ( is_string( $field ) && is_array( $form ) ) {
			$field = self::get_field( $field, $form );
		} elseif ( is_string( $field ) && null == $form ) {
			return false;
		}

		return $field[ 'type' ];

	}

	/**
	 * Get field config by ID
	 *
	 * @since 1.4.4
	 *
	 * @param string|array $field Field ID. If you pass an array, that will be returned.
	 * @param array|null $form
	 * @param bool $filter Optional. Apply field filters? Default is false. Added in 1.5.0
	 *
	 * @return bool|array
	 */
	public static function get_field( $field, array $form = null, $filter = false ){
		if ( ! is_array( $field ) ) {
			if ( ! is_array( $form ) ) {
				global $form;
			}

			$fields = Caldera_Forms_Forms::get_fields( $form, false );
			if ( isset( $fields[ $field ] ) ) {
				$field = $fields[ $field ];
			} else {
				return false;
			}
		}

		if ( $filter ) {
			$field = self::apply_field_filters( $field, $form );
		}

		return $field;
	}

	/**
	 * Get field ID attr
	 *
	 * @since 1.5.0
	 *
	 * @param string|array $field Field config or field ID
	 * @param null $current_form_count Optional. Current form count. If is nbull, the default, global will be used.
	 *
	 * @return string
	 */
	public static function get_base_id( $field, $current_form_count = null, $form = null ){
		$field = self::get_field( $field, $form );
		if ( null == $current_form_count ) {
			$current_form_count = Caldera_Forms_Render_Util::get_current_form_count();
		}

		return $field[ 'ID' ] . '_' . $current_form_count;

	}

	/**
	 * Check if a field is a file field of either type
	 *
	 * @since 1.4.4
	 *
	 * @param array|string $field Field ID or config array
	 * @param array|null $form Optional. Form config array. MUST USE if $field is ID not config array.
	 *
	 * @return bool
	 */
	public static function is_file_field( $field, array $form = null ){
		$field = self::get_field( $field, $form );

		return in_array( self::get_type( $field, $form ), Caldera_Forms_Files::types() );
	}

	/**
	 * Get ID attribute for the element that displays star field stars
	 *
	 * @since 1.5.0
	 *
	 * @param string $id_attr ID attribute of actual input
	 *
	 * @return string
	 */
	public static function star_target( $id_attr ){
		return $id_attr . '_stars';
	}

	protected static $field_classes = array();

	/**
	 * Prepare field classes
	 *
	 * @since 1.5.0
	 *
	 * @param array $field Field config array
	 * @param array $form Form config array
	 *
	 * @return array
	 */
	public static function prepare_field_classes( $field, $form ){
		$current_form_count = Caldera_Forms_Render_Util::get_current_form_count();
		if ( ! isset( self::$field_classes[ $form[ 'ID' ] ] ) ) {
			self::$field_classes[ $form[ 'ID' ] ] = array();
		}

		if ( ! isset( self::$field_classes[ $form[ 'ID' ] ][ $current_form_count ] ) ) {
			self::$field_classes[ $form[ 'ID' ] ][ $current_form_count ] = array();
		}

		if ( empty( self::$field_classes[ $form[ 'ID' ] ][ $current_form_count ][ $field[ 'ID' ] ] ) ) {
			self::$field_classes[ $form[ 'ID' ] ][ $current_form_count ][ $field[ 'ID' ] ] = array();
			$field_classes                                                                 = array(
				"control_wrapper"    => array( "form-group" ),
				"field_label"        => array( "control-label" ),
				"field_required_tag" => array( "field_required" ),
				"field_wrapper"      => array(),
				"field"              => array( "form-control" ),
				"field_caption"      => array( "help-block" ),
				"field_error"        => array( "has-error" ),
			);

			if( self::is_file_field( $field, $form ) ){
				$field_classes[ 'field' ] = array();
			}


			$field_classes = apply_filters( 'caldera_forms_render_field_classes', $field_classes, $field, $form );
			$field_classes = apply_filters( 'caldera_forms_render_field_classes_type-' . $field[ 'type' ], $field_classes, $field, $form );
			$field_classes = apply_filters( 'caldera_forms_render_field_classes_slug-' . $field[ 'slug' ], $field_classes, $field, $form );

			self::$field_classes[ $form[ 'ID' ] ][ $current_form_count ][ $field[ 'ID' ] ] = $field_classes;
		}

		return self::$field_classes[ $form[ 'ID' ] ][ $current_form_count ][ $field[ 'ID' ] ];
	}

	/**
	 * Prepare aria attributes for a field
	 *
	 * @since 1.5.0
	 *
	 * @param array $field_structure Field structure
	 *
	 * @return array
	 */
	public static function prepare_aria_attrs( $field_structure, $field ){
		// if has label
		if ( empty( $field[ 'hide_label' ] ) ) {
			// visible label, set labelled by
			$field_structure[ 'aria' ][ 'labelledby' ] = $field[ 'ID' ] . 'Label';
		} else {
			// hidden label, aria label instead
			$field_structure[ 'aria' ][ 'label' ] = $field[ 'label' ];
		}
		// if has caption
		if ( ! empty( $field[ 'caption' ] ) ) {
			$field_structure[ 'aria' ][ 'describedby' ] = $field[ 'ID' ] . 'Caption';
		}


		return $field_structure;
	}

	/**
	 * Get allowed math functions with ability to filter allowed function
	 *
	 * @since 1.5.0
	 *
	 * @param array $form
	 *
	 * @return array
	 */
	public static function get_math_functions( array $form ){
		$math_functions = array(
			'pow',
			'abs',
			'acos',
			'asin',
			'atan',
			'atan2',
			'ciel',
			'cos',
			'exp',
			'floor',
			'log',
			'max',
			'min',
			'random',
			'round',
			'sin',
			'sqrt',
			'tan'
		);

		/**
		 * Filter the allowed math functions
		 *
		 * Useful for removing functions
		 *
		 * Add functions with extreme caution, must be name of a function in PHP global scope and a method of JavaScript Math object.
		 *
		 * @since 1.5.0
		 *
		 * @param array $math_functions Functions allowed
		 * @param array $form Form Config
		 */
		return apply_filters( 'caldera_forms_field_util_math_functions', $math_functions, $form );
	}


	/**
	 * Get the configuration for a field.
	 *
	 * @since 1.5.0
	 *
	 * @param string $slug Slug of field to get config for.
	 * @param array $form Form config array.
	 *
	 * @return bool|mixed|void
	 */
	public static function get_field_by_slug( $slug, $form ){

		foreach ( $form[ 'fields' ] as $field_id => $field ) {

			if ( $field[ 'slug' ] == $slug ) {

				return self::apply_field_filters( $field, $form );

			}
		}

		return false;

	}

	/**
	 * Wrapper for multi-use field filters
	 *
	 * @since 1.5.0
	 *
	 * @param array $field Field config
	 * @param array $form Form config
	 *
	 * @return array
	 */
	public static function apply_field_filters( $field, $form ){

		/**
		 * Filter field config.
		 *
		 * @since unknown
		 *
		 * @param array $field The field config.
		 * @param array $form The form config.
		 */
		$field = apply_filters( 'caldera_forms_render_get_field', $field, $form );

		/**
		 * Filter field config for fields of a given type.
		 *
		 * Filter name is dynamic, based on field type. For example "caldera_forms_render_get_field_type-hidden" or "caldera_forms_render_get_field_type-radio"
		 *
		 * @since unknown
		 *
		 * @param array $field The field config.
		 * @param array $form The form config.
		 */
		$field = apply_filters( 'caldera_forms_render_get_field_type-' . $field[ 'type' ], $field, $form );

		/**
		 * Filter field config for fields with a given slug
		 *
		 * Filter name is dynamic, based on field type. For example "caldera_forms_render_get_field_slug-salsa" or "caldera_forms_render_get_field_slug-chips"
		 *
		 * @since unknown
		 *
		 * @param array $field The field config.
		 * @param array $form The form config.
		 */
		$field = apply_filters( 'caldera_forms_render_get_field_slug-' . $field[ 'slug' ], $field, $form );

		return $field;
	}

	/**
	 * Get types of credit cards we can do UI stuff to with CC field
	 *
	 * @since 1.5.0
	 *
	 * @param array $field Field config
	 * @param array $form Form config
	 *
	 * @return array
	 */
	public static function credit_card_types( $field, $form ){
		$types = array(
			'amex',
			'china_union_pay',
			'dankort',
			'diners_club_carte_blanche',
			'diners_club_international',
			'diners_club_us_and_canada',
			'discover',
			'jcb',
			'laser',
			'maestro',
			'mastercard',
			'visa',
			'visa_electron'
		);

		/**
		 * Change types of credit cards we can do UI stuff to with CC field
		 *
		 * @since 1.5.0
		 *
		 * @param array $field Field config
		 * @param array $form Form config
		 */
		return apply_filters( 'caldera_forms_credit_card_types', $types, $field, $form );
	}

	/**
	 * Check if a form has a type of field
	 *
	 * @since 1.5.0
	 *
	 * @param string $type Type to check for
	 * @param array $form Form config to check in
	 *
	 * @return bool
	 */
	public static function has_field_type( $type, array  $form ){
		$types = wp_list_pluck( $form[ 'fields' ], 'type' );

		return in_array( $type, array_values( $types ) );
	}

	/**
	 * Get field default value
	 *
	 * @since 1.5.0
	 *
	 * @param array|string $field Field config or field ID
	 * @param array $form Form config
	 *
	 * @return bool
	 */
	public static function get_default( $field, array  $form ){
		if ( is_string( $field ) ) {
			$field = self::get_field( $field, $form );
		}

		if ( ! is_array( $field ) || empty( $field[ 'config' ][ 'default' ] ) ) {
			return false;
		}

		return $field[ 'config' ][ 'default' ];

	}

	/**
	 * Check a field's conditional logic
	 *
	 * @since 1.5.0.4
	 *
	 * @param array|string $field Field config or field ID
	 * @param array $form Form config
	 *
	 * @return bool
	 */
	public static function check_conditional( $field, array $form ){
		if ( is_string( $field ) ) {
			$field = self::get_field( $field, $form );
		}

		if ( ! empty( $field[ 'conditions' ][ 'type' ] ) ) {
			$conditional = $field[ 'conditions' ];
			if ( ! empty( $form[ 'conditional_groups' ][ 'conditions' ][ $field[ 'conditions' ][ 'type' ] ] ) ) {
				$conditional = $form[ 'conditional_groups' ][ 'conditions' ][ $field[ 'conditions' ][ 'type' ] ];
			}

			return Caldera_Forms::check_condition( $conditional, $form );
		}

		return true;
	}

	/**
	 * Apply formatting, such as money formatting to a calculation field value
	 *
	 * @since 1.5.0.7
	 *
	 * @param array $field Field config
	 * @param string|float|int $value Value
	 *
	 * @return string
	 */
	public static function format_calc_field( $field, $value ){
		if ( isset( $field[ 'config' ][ 'fixed' ] ) ) {
			$money = true;
		} else {
			$money = false;

		}

		if ( $money ) {
			if ( function_exists( 'money_format' ) ) {
				$value = money_format( '%i', $value );
			} else {
				$value = sprintf( '%01.2f', $value );
			}

		}

		return $value;

	}

	/**
	 * Get a dropdown, radio or toggle's calculation value
	 *
	 * @since 1.5.1
	 *
	 * @param array $option Option configuration
	 * @param array $field Field configuration
	 * @param array $form Form configuration
	 *
	 * @return int|string|float
	 */
	public static function get_option_calculation_value( array $option, array $field, array  $form ){
		$calc_val = 0;
		if ( isset( $option[ 'calc_value' ] ) && '' !== $option[ 'calc_value' ] ) {
			$calc_val = $option[ 'calc_value' ];
		} elseif ( isset( $option[ 'value' ] ) ) {
			$calc_val = $option[ 'value' ];
		} elseif ( isset( $option[ 'label' ] ) ) {
			$calc_val = $option[ 'label' ];
		}

		/**
		 * Change the value to be provided by an option to calculation field.
		 *
		 * @since 1.5.1
		 *
		 * @param int $calc_val Calculate value
		 * @param array $option Option configuration
		 * @param array $field Field configuration
		 * @param array $form Form configuration
		 */
		return apply_filters( 'caldera_forms_get_option_calculation_value', $calc_val, $option, $field, $form );
	}

	/**
	 * Find all option values for a select field
	 *
	 * @since 1.5.1
	 *
	 * @param array $field Field config
	 *
	 * @return array
	 */
	public static function find_option_values( array  $field ){
		$option_values = array();
		if( isset(  $field[ 'config' ][ 'option' ] ) && is_array(  $field[ 'config' ][ 'option' ] ) ){

			foreach ( $field[ 'config' ][ 'option' ] as $opt_id => $option){
				$option_values[ $opt_id ] = isset( $option[ 'value' ] ) ? $option[ 'value' ] : $option[ 'label' ];
			}
		}

		return $option_values;
	}

	/**
	 * Identify the field value for a select field
	 *
	 * @since 1.5.1
	 *
	 * @param array $field Field config
	 * @param string|array $field_value Current value
	 *
	 * @return mixed|null
	 */
	public static function find_select_field_value(   $field, $field_value ){
		//if is checkbox saved as array just return as is.
		if( is_array( $field_value ) ){
			return $field_value;
		}

		if ( ! empty( $field[ 'config' ][ 'option' ] ) ) {
			$option_values = Caldera_Forms_Field_Util::find_option_values( $field );

			if( isset( $field['config'] ) && ! empty( $field[ 'config' ][ 'default_option' ] ) ) {
				$field_value = Caldera_Forms::do_magic_tags(  $field[ 'config' ][ 'default_option' ] );
			}elseif( is_string( $field_value ) && array_key_exists( $field_value, $option_values ) ){
				$field_value = $option_values[ $field_value ];
			}

			if ( ! in_array( $field_value, $option_values ) ) {
				$field_value = null;
			}

		}

		return $field_value;

	}

}