<?php

/**
 * Class for creating input field element HTML
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Field_Input extends Caldera_Forms_Field_HTML{

	/**
	 * @inheritdoc
	 */
	public static function html( array $field, array $field_structure, array $form, $value = null ){
		$type = Caldera_Forms_Field_Util::get_type( $field );
		$field_base_id = Caldera_Forms_Field_Util::get_base_id( $field, null, $form );
		if ( null === $value ) {
			if( ! empty( $field_structure [ 'field_value' ] ) ){
				$value = Caldera_Forms::do_magic_tags( $field_structure [ 'field_value' ], null, $form );
			}else{
				$value = self::find_default( $field, $form );
			}



		}

		$sync =  $syncer = $default = false;
		if( in_array( $type, self::sync_fields() ) ){
			$syncer = Caldera_Forms_Sync_Factory::get_object( $form, $field, $field_base_id );
			$sync = $syncer->can_sync();
			$default = $syncer->get_default();
		}

		if( 'text' == $type && !empty( $field['config']['type_override'] ) ){
			$type = $field['config']['type_override'];
		}
		$required = '';

		$field_classes = Caldera_Forms_Field_Util::prepare_field_classes( $field, $form );
		$mask = self::get_mask_string( $field );
		if( ! empty( $mask ) ){
			Caldera_Forms_Render_Assets::enqueue_script( 'inputmask' );
		}

		$place_holder = self::place_holder_string( $field );
		$attrs = array(
			'type' => $type,
			'data-field' =>$field[ 'ID'],
			'class' => $field_classes[ 'field' ],
			'id' => $field_base_id,
			'name' => $field_structure['name'],
			'value' => $value,
			'data-type' => $type
		);


		if( ! empty( $field[ 'hide_label' ] ) && empty( $place_holder ) ){
			$place_holder  = self::place_holder_string( $field, $field[ 'label' ] );
		}

		if( 'number' === $type ){
			foreach( array(
				'min',
				'max',
				'step'
			) as $index ){
				if( isset( $field[ 'config' ][ $index ] ) && ( 0 === $field[ 'config' ][ $index ] || '0' === $field[ 'config' ][ $index ] || ! empty( $field[ 'config' ][ $index ] ) )){
					$attrs[ $index ] = $field[ 'config' ][ $index ];
				}
			}
			$attrs[ 'data-parsley-type' ] = 'number';
		}elseif ( 'phone_better' === $type ){
			$attrs[ 'type' ] = 'tel';
		}elseif ( 'credit_card_number' === $type ){
			$attrs[ 'type' ] = 'tel';
			$attrs[ 'class' ][] = 'cf-credit-card ';
			$attr[ 'data-parsley-creditcard' ] = Caldera_Forms_Field_Util::credit_card_types( $field, $form );
		}elseif( 'credit_card_exp' === $type ){
			$attrs[ 'type' ] = 'tel';
			$attr[ 'data-parsley-creditcard' ] = '';
		}elseif ( 'credit_card_cvv' == $type ){
			$attrs[ 'type' ] = 'tel';
			$attr[ 'data-parsley-creditcard' ] = '';
		}elseif ( 'hidden' === $type ){
            if ( ! empty( $field[ 'config' ][ 'custom_class' ] ) ) {
                $attrs['class'] = $field['config']['custom_class'];
            }
        }

		if( $field_structure['field_required'] ){
			$required = 'required';
			$attrs[ 'aria-required' ] = 'true';
		}

		if( $sync ){
			$attrs[ 'data-binds' ] = wp_json_encode( $syncer->get_binds() );
			$attrs[ 'data-sync' ] = $default;
		}

		$attr_string = caldera_forms_field_attributes(
			$attrs,
			$field,
			$form
		);

		$aria = self::aria_string( $field_structure );

		return '<input ' .  $place_holder . ' ' . $mask . ' ' .  $required . ' ' . $attr_string   . ' ' . $aria .' >';

	}

	/**
	 * Defined which fields use sync
	 *
	 * @sine 1.5.0
	 *
	 * @return array
	 */
	protected static function sync_fields(){
		return array(
			'text',
			'email',
			'html',
			'number',
			'hidden',
			'url',
			'phone_better',
			'paragraph'
		);
	}


	/**
	 * Get input mask config string
	 *
	 * @since 1.5.0
	 *
	 * @param array $field
	 *
	 * @return string
	 */
	protected static function get_mask_string( array  $field ){
		$mask = '';
		if ( 'phone' != Caldera_Forms_Field_Util::get_type( $field ) ) {
			if ( ! empty( $field[ 'config' ][ 'masked' ] ) ) {
				$mask = $field[ 'config' ][ 'mask' ];
			}
		} else {
			$mask = '(999)999-9999';
			if( $field['config']['type'] == 'international' ){
				$mask = '+99 99 999 9999';
			}elseif ( $field['config']['type'] == 'custom' ) {
				$mask = $field['config']['custom'];
			}

		}

		if( ! empty( $mask ) ){
			$mask = "data-inputmask=\"'mask': '" . $mask . "'\" ";
		}

		return $mask;
	}


}