<?php



/**
 * Filters shortcode attributes
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 CalderaWP LLC
 */
class Caldera_Forms_Shortcode_Atts {

	/**
	 * Defaults to set from shortcode
	 *
	 * @since 1.5.0.7
	 *
	 * @var array
	 */
	protected static $defaults;

	/**
	 * Setup field defaults form shortocde attributees
	 *
	 * @since 1.5.0.7
	 *
	 * @uses "shortcode_atts_caldera_form" filter
	 * @uses "shortcode_atts_caldera_form_modal" filter
	 *
	 * @param array $out
	 * @param array $pairs
	 * @param array $atts
	 * @param string $shortcode
	 *
	 * @return array
	 */
	public static function allow_default_set( $out, $pairs, $atts, $shortcode ){
		$form = array();
		if ( isset( $atts[ 'id' ] ) ) {
			$form = Caldera_Forms_Forms::get_form( $atts[ 'id' ] );

		}

		if ( empty( $form ) && isset( $atts[ 'ID' ] ) ) {
			$form = Caldera_Forms_Forms::get_form( $atts[ 'ID' ] );

		}

		if ( empty( $form ) && isset( $form[ 'name' ] ) ) {
			$form = Caldera_Forms_Forms::get_form( $atts[ 'name' ] );
		}
		if( ! empty( $form ) ){
			$fields = Caldera_Forms_Forms::get_fields( $form );
			$field_ids = array_keys( $fields );
			if( ! empty( $field_ids ) ){
				foreach ( $atts as $att => $value ){
					if( in_array( $att, $field_ids ) ){
						self::$defaults[ $att ] = $value;
						$out[ $att ] = $value;
					}
				}
			}
		}

		if( ! empty( self::$defaults ) ){
			add_filter( 'caldera_forms_render_get_field', array( __CLASS__, 'set_default' ), 19 );
		}

		return $atts;

	}

	/**
	 * Set field default
	 *
	 * @since 1.5.0.7
	 *
	 * @uses "caldera_forms_render_get_field" filter
	 *
	 * @param array $field Field config
	 *
	 * @return array
	 */
	public static function set_default( $field ){
		if( array_key_exists( $field[ 'ID' ], self::$defaults ) ){
			$field[ 'config' ][ 'default' ] = self::$defaults[ $field[ 'ID' ] ];
		}

		return $field;

	}

}