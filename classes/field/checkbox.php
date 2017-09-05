<?php


/**
 * Class Caldera_Forms_Field_Checkbox
 */
class Caldera_Forms_Field_Checkbox extends Caldera_Forms_Field_HTML{



	/**
	 * Create HTML form the field element (not the wrapper)
	 *
	 * @since 1.5.6
	 *
	 * @param array $field Field config
	 * @param array $field_structure Prepared field structure
	 * @param array $form Form config
	 *
	 * @return string
	 */
	public static function html( array $field, array $field_structure,array $form ){
		$out = '';
		if( ! empty( $field[ 'description' ] ) ){
			$out .= '<legend>' . esc_html( $field[ 'description' ] ) . '</legend>';
		}

		$id_attr = Caldera_Forms_Field_Util::get_base_id( $field, null, $form );
		$field_name = $field_structure[ 'name' ];
		$field_value = isset( $field_structure[ 'field_value' ] ) ? $field_structure[ 'field_value' ] : '';
		$field_value = Caldera_Forms_Field_Util::find_select_field_value( $field, $field_value );

		if( ! empty( $field[ 'config' ][ 'option' ] ) ){
			foreach( $field[ 'config' ][ 'option' ] as $option_key => $option ){
				$value = $option['value'];

				$attrs = self::create_field_attrs( $field, $field_structure, $value,  $id_attr, 'checkbox', $form );
				if( ! empty( $attrs[ 'aria-required' ] ) ){
					$required = 'required';
				}else{
					$required = '';
				}

				if( in_array( $value, (array) $field_value) ) {
					$attrs[ 'checked' ] = 'checked';
				}

				$attrs[ 'data-calc-value'  ] = Caldera_Forms_Field_Util::get_option_calculation_value( $option, $field, $form );

				$attrs[ 'name' ] = $field_name . '[' . $option_key . ']';

				$attr_string = caldera_forms_field_attributes(
					$attrs,
					$field,
					$form
				);


				$aria = self::aria_string( $field_structure );
				$inline = ! empty( $field['config']['inline']  ) ? true : false;

				$out .=  self::label( $field_structure, $inline, $id_attr, $option_key, $option ) . '<input' .  $required . ' ' . $attr_string   . ' ' . $aria .' />';
			}
		}

		return $out;
	}

	public static function label( $field_structure, $inline, $id_attr, $option_key, $option ){
		$attrs = array(
			'for' => $id_attr . '_' . $option_key,
		);
		if( $inline ){
			$attrs[ 'class' ] = 'cf-new-checkbox-inline';
			$attrs[ 'id' ] = $id_attr . 'Label';
		}
		$attrs = caldera_forms_implode_field_attributes( caldera_forms_escape_field_attributes_array( $attrs ) );

		return '<label ' . $attrs . '>' . $option[ 'label' ] . '</label>';
	}


}