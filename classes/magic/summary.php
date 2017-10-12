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
class Caldera_Forms_Magic_Summary extends Caldera_Forms_Magic_Parser {

	/**
	 * Is email HTML?
	 *
	 * @since 1.4.6
	 *
	 * @var bool
	 */
	protected $html = true;

	/**
	 * Sprintf pattern for HTML
	 *
	 * @since 1.4.6
	 *
	 * @var string
	 */
	protected $pattern = '';

	/**
	 * Fields ordered
	 *
	 * @since 1.5.0.10
	 *
	 * @var array
	 */
	protected $ordered_fields;

	/**
	 * Set if we should use HTML tags or not
	 *
	 * @since 1.4.6
	 *
	 * @param bool $html Use HTML?
	 */
	public function set_html_mode( $html ){
		$this->html = (bool) $html;
	}

	public function set_fields( $fields ){
		$this->ordered_fields = $fields;
	}


	/**
	 * @inheritdoc
	 */
	protected function parse(){
		if( $this->html ){
			$this->set_pattern();
		}

		$out = array();
		if( empty( $this->ordered_fields ) ){
			$this->ordered_fields = $ordered_fields = Caldera_Forms_Forms::get_fields( $this->form );
		}else{
			$ordered_fields = $this->ordered_fields;
		}

		/**
		 * Modify fields used in summary magic tag
		 *
		 * @since 1.5.0.10
		 *
		 * @param array $ordered_fields Fields in order they will be displayed
		 * @param array $form Form config
		 */
		$this->ordered_fields = $ordered_fields = apply_filters( 'caldera_forms_summary_magic_fields', $ordered_fields, $this->form );


		if ( ! empty( $ordered_fields ) ) {
			$tag_i = -1;
			foreach ( $ordered_fields as $field_id => $field ) {
				$tag_i++;
				$type = Caldera_Forms_Field_Util::get_type( $field, $this->form );
				$not_support = Caldera_Forms_Fields::not_support( $type, 'entry_list' );
				if( $not_support ){
					continue;
				}

				if( Caldera_Forms_Field_Util::is_file_field( $field_id, $this->form ) && Caldera_Forms_Files::is_private( $field )  ){
					continue;

				}

				$field_value = false;
				switch( $type ){
					case 'file'  :
						$field_value = Caldera_Forms_Magic_Doer::magic_image( $field,  $this->get_field_value( $field_id ), $this->form );
						break;
					case 'calculation' :
						$field_value = Caldera_Forms_Magic_Doer::calculation_magic( $field, $this->get_field_value( $field_id ) );
						break;
					case 'credit_card_number' :
						$field_value = $this->get_credit_card_hasher()->credit_card_number( $this->get_field_value( $field_id ) );
						break;
					case 'credit_card_cvc' :
						$field_value = $this->get_credit_card_hasher()->credit_card_cvc( $this->get_field_value( $field_id ) );
						break;
					default :
						if (  null == $this->data ) {
							$field_values = (array) Caldera_Forms::get_field_data( $field_id, $this->form );
						}else{
							if( ! isset( $this->data[ $field_id ] ) ){
								continue;
							}
							$field_values = (array) $this->get_field_value( $field_id );
						}

						if ( isset( $field_values[ 'label' ] ) ) {
							$field_values = $field_values[ 'value' ];
						} else {
							foreach ( $field_values as $field_key => $field_value ) {
								if ( true === is_array( $field_value ) && true === array_key_exists( 'label', $field_value ) && true === array_key_exists( 'value', $field_value ) ) {
									$field_values[ $field_key ] = $field_value[ 'value' ];
								}

							}
						}

						$should_use_label = false;
						if ( is_array( $field ) ) {
							$should_use_label = $this->should_use_label( $field );
						}

						if( $should_use_label ){
							foreach ( $field_values as $field_key => $field_value ) {
								$field_values[ $field_key ] = $this->option_value_to_label( $field_value, $field );
							}
						}


						$field_value = implode( ', ', (array) $field_values );
						break;

				}


				if ( $field_value !== null && ! is_array( $field_value ) && strlen( (string) $field_value ) > 0 ) {

					/**
					 * Change value displayed for field inside Caldera Forms summary magic tag
					 *
					 * @since 1.5.0.10
					 *
					 * @param string $field_value The value
					 * @param array $field Field config
					 * @param array $form Form config
					 */
					$field_value = apply_filters( 'caldera_forms_magic_summary_field_value', $field_value, $field, $this->form );

					if ( $this->html ) {
						$out[ $tag_i ] = sprintf( $this->pattern, $field[ 'label' ], $field_value );
					} else {
						$out[ $tag_i ] = $field[ 'label' ] . ': ' . $field_value;
					}

				}
			}
		}

		// vars
		if ( ! empty( $this_form[ 'variables' ] ) ) {
			foreach ( $this_form[ 'variables' ][ 'keys' ] as $var_key => $var_label ) {
				if ( $this_form[ 'variables' ][ 'types' ][ $var_key ] == 'entryitem' ) {
					$label = ucfirst( str_replace( '_', ' ', $var_label ) );
					if ( $this->html ) {
						$out[] = sprintf( $this->pattern, $label, $this_form[ 'variables' ][ 'values' ][ $var_key ] );
					} else {
						$out[] = $label . ': ' . $this_form[ 'variables' ][ 'values' ][ $var_key ];
					}
				}
			}
		}
		if ( ! empty( $out ) ) {
			$this->tag = implode( "\r\n", $out );
		} else {
			$this->tag = '';
		}

	}


	/**
	 * Set sprintf pattern in pattern property
	 *
	 * @since 1.4.6
	 */
	protected function set_pattern(){
		/**
		 * Change the sprintf pattern for the {summary} magic tag
		 *
		 * @since 1.4.5
		 *
		 * @param string $pattern The sprintf pattern to use
		 * @param array $this_form Form config
		 */
		$this->pattern = apply_filters( 'caldera_forms_summary_magic_pattern', '<strong>%s</strong><div style="margin-bottom:20px;">%s</div>', $this->form );

	}

	/**
	 * Check if field should use label instead of value
	 *
	 * @since 1.4.6
	 *
	 * @param array $field Field config
	 *
	 * @return bool
	 */
	protected function should_use_label( array $field ){
		if( empty( $field[ 'config' ][ 'option' ] ) ){
			return false;
		}

		/**
		 * Set a single-select field to show label instead of value in {summary} magic tag
		 *
		 * @since 1.4.6
		 *
		 * @param bool $use Use or not?
		 * @param array $field Field config
		 * @param array $form Form config
		 */
		return wp_validate_boolean( apply_filters( 'caldera_forms_magic_summary_should_use_label', false, $field, $this->form ) );
	}

	/**
	 * Convert option value to label
	 *
	 * @since 1.4.6
	 *
	 * @param mixed $value Value to find label for
	 * @param array$field
	 *
	 * @return bool|mixed False if not found, else value
	 */
	protected function option_value_to_label( $value, $field ){
		if( empty( $field[ 'config' ][ 'option' ] ) ){
			return false;
		}

		foreach ( $field[ 'config' ][ 'option' ]  as $opt_id => $option ){
			if( $value == $option[ 'value'] ){
				return $option[ 'label' ];
			}

		}

		return false;
	}

}