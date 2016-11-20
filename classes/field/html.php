<?php
/**
 * Base class for creating field element HTML
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
abstract  class Caldera_Forms_Field_HTML   {


	/**
	 * Create HTML form the field element (not the wrapper)
	 *
	 * This must be ovveriden in subclass
	 *
	 * @since 1.5.0
	 *
	 * @param array $field Field config
	 * @param array $field_structure Prepared field structure
	 * @param array $form Form config
	 *
	 * @return string
	 */
	public static function html( array $field, array $field_structure,array $form ){
		return '';
	}

	/**
	 * Create the aria attributes string
	 *
	 * @since 1.5.0
	 *
	 * @param array $field_structure Prepared field structure
	 *
	 * @return string
	 */
	protected static function aria_string( $field_structure ){
		if ( ! empty( $field_structure[ 'aria' ] ) ) {
			$attrs = caldera_forms_escape_field_attributes_array( $field_structure[ 'aria' ], 'aria-' );
			return caldera_forms_implode_field_attributes( $attrs );
		}

		return '';
	}


	/**
	 * Get placeholder string for field
	 *
	 * @since 1.5.0
	 *
	 * @param array $field Field config
	 *
	 * @return string
	 */
	protected static function place_holder_string( array  $field ){
		if ( ! empty( $field[ 'config' ][ 'placeholder' ] ) ) {
			return 'placeholder="' . esc_attr( Caldera_Forms::do_magic_tags( $field[ 'config' ][ 'placeholder' ] ) ) . '"';
		}

		return '';

	}



}