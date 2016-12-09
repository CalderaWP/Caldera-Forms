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
	 * Set if we should use HTML tags or not
	 *
	 * @since 1.4.6
	 *
	 * @param bool $html Use HTML?
	 */
	public function set_html_mode( $html ){
		$this->html = (bool) $html;
	}


	/**
	 * @inheritdoc
	 */
	protected function parse(){
		if( $this->html ){
			$this->set_pattern();
		}

		$out = array();
		$ordered_fields = Caldera_Forms_Forms::get_fields( $this->form );
		if ( ! empty( $ordered_fields ) ) {
			foreach ( $ordered_fields as $field_id => $field ) {

				if ( in_array( $field[ 'type' ], array(
					'button',
					'recaptcha',
					'html'
				) ) ) {
					continue;
				}

				if( Caldera_Forms_Field_Util::is_file_field( $field_id, $this->form ) && Caldera_Forms_Files::is_private( Caldera_Forms_Field_Util::get_field( $field_id, $form ) ) ){
					continue;
				}

				// filter the field to get field data
				$field = apply_filters( 'caldera_forms_render_get_field', $field, $this->form );
				$field = apply_filters( 'caldera_forms_render_get_field_type-' . $field[ 'type' ], $field, $this->form );
				$field = apply_filters( 'caldera_forms_render_get_field_slug-' . $field[ 'slug' ], $field, $this->form );

				if (  null == $this->data ) {
					$field_values = (array) Caldera_Forms::get_field_data( $field_id, $this->form );
				}else{
					$field_values = $this->data;
				}

				if ( isset( $field_values[ 'label' ] ) ) {
					$field_values = $field_values[ 'value' ];
				} else {
					foreach ( $field_values as $field_key => $field_value ) {
						if ( isset( $field_value[ 'label' ] ) && isset( $field_value[ 'value' ] ) ) {
							$field_value[ $field_key ] = $field_value[ 'value' ];
						}

					}
				}

				$field_value = implode( ', ', (array) $field_values );

				if ( $field_value !== null && strlen( $field_value ) > 0 ) {
					if ( $this->html ) {
						$out[] = sprintf( $this->pattern, $field[ 'label' ], $field_value );
					} else {
						$out[] = $field[ 'label' ] . ': ' . $field_value;
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

}