<?php


/**
 * Helper functions for calculation fields
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_Field_Calculation {

	/**
	 * Determines if checkboxes are calculated as sum of checked options or highest option
	 *
	 * @since 1.5.0.10
	 *
	 * @param array $field Field config
	 * @param array $form Form config
	 *
	 * @return bool
	 */
	public static function checkbox_mode( $field, $form ){

		/**
		 * Determines if checkboxes are calculated as sum of checked options or highest option
		 *
		 * @since 1.5.0.10
		 *
		 * @param bool $sum_mode Set true for sum, false for highest.
		 * @param array $field Field config
		 * @param array $form Form config
		 *
		 */
		return (bool) apply_filters( 'caldera_forms_checkbox_calculate_sum', true, $field, $form );

	}

	/**
	 * Get value of field for calculation
	 *
	 * @since 1.5.0.10
	 *
	 * @param array $field Field config
	 * @param array $form Form config
	 *
	 * @return float
	 */
	public static function get_value( $field, $form ){
		$entry_value = Caldera_Forms::get_field_data( $field[ 'ID' ], $form );
		$type = Caldera_Forms_Field_Util::get_type( $field, $form );
		$number = 0;
		switch( $type ){
			case 'checkbox' :
					if( true == self::checkbox_mode( $field, $form ) ){
						if( ! empty(  $entry_value ) ){
							foreach ( $entry_value as $opt => $value ){
								 $entry_value[ $opt ] = Caldera_Forms_Field_Util::get_option_calculation_value( $opt, $field, $form );
							}

							$number = floatval( array_sum( $entry_value ) );
						}


					}else{
						if( is_array( $entry_value ) ){
							foreach (  $entry_value as $value ){
								if( $value > $number ){
									$number = $value;
								}
							}
						}else{
							$number = floatval( $entry_value );
						}

						$number = self::find_calc_value( $number, $field, $form );
					}
				break;
			case 'radio':
			case 'dropdown':
			case 'toggle_switch' :
				$number = self::find_calc_value( $entry_value, $field, $form );

				break;
			default :
				$number = self::find_value( $entry_value );
				break;

		}

		return floatval( $number );

	}

	/**
	 * Find entry value
	 *
	 * Sums arrays if needed
	 *
	 * @param array|string|int|float $entry_value Field entry value
	 *
	 * @return float
	 */
	protected static function find_value( $entry_value ){
		if ( is_array( $entry_value ) ) {
			foreach ( $entry_value as $i => $v ){
				$x=1;
			}
			$number = floatval( array_sum( $entry_value ) );

			return $number;
		} else {
			$number = floatval( $entry_value );

			return $number;
		}
	}

	/**
	 * Get a dropdown, radio or toggle's calculation value based on selected option and its possible calcualtion value
	 *
	 * @since 1.5.1
	 *
	 * @param int|float|string $entry_value Resulting value
	 * @param array $field Field configuration
	 * @param array $form Form configuration
	 *
	 * @return float|int|string
	 */
	protected static function find_calc_value( $entry_value, $field, $form ){
		foreach ( $field[ 'config' ][ 'option' ] as $opt_id => $option ){
			if( $entry_value == $option[ 'value' ] ){
				$entry_value = Caldera_Forms_Field_Util::get_option_calculation_value( $option, $field, $form );
				break;
			}

		}
		return $entry_value;
	}

	public static function js_function_name( $field_base_id ){
		return 'do_calc_' . esc_attr( $field_base_id );
	}
}