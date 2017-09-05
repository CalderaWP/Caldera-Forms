<?php


/**
 * Class Caldera_Forms_Field_New
 */
class Caldera_Forms_Field_New {


	public function add_hooks()
	{
		add_filter( 'caldera_forms_render_field_file', array( $this, 'maybe_update_field_html' ), 9, 6 );
		add_filter( 'caldera_forms_render_field_structure', array( $this, 'filter_field_structure' ), 9, 2 );
		add_filter( 'caldera_forms_field_attributes', array( $this, 'filter_attrs' ), 9 , 3 );
	}

	/**
	 * Change out field field for fields that have a new type.
	 *
	 * @since 1.5.6
	 *
	 * @uses "caldera_forms_render_field_file" filter
	 *
	 * @param $file
	 * @param $type
	 * @param $field_id
	 * @param $field_file
	 * @param $field_structure
	 * @param $form
	 *
	 * @return mixed
	 */
	public  function maybe_update_field_html( $file, $type, $field_id, $field_file, $field_structure, $form ){
		$field = Caldera_Forms_Field_Util::get_field( $field_id, $form, true );

		if( $this->is_new_tmpl_field( $field, $form ) ) {
			if( is_array( $field ) && isset( $field[ 'config' ][ 'tmpl_type' ] ) && 'new' == $field[ 'config' ][ 'tmpl_type' ] ){
				$file = str_replace(  basename( $file ), 'new-field.php', $file );
			}
		}

		return $file;
	}

	public  function is_new_tmpl_field( array $field, array $form ){
		$type = Caldera_Forms_Field_Util::get_type( $field, $form );
		if( $this->has_new( $type ) ) {
			if( is_array( $field ) && isset( $field[ 'config' ][ 'tmpl_type' ] ) && 'new' == $field[ 'config' ][ 'tmpl_type' ] ){
				return true;
			}
		}

		return false;
	}

	public  function filter_field_structure( $field_structure, $form ){
		$field = $field_structure[ 'field' ];
		$type = Caldera_Forms_Field_Util::get_type( $field, $form );
		if( !  $this->is_new_tmpl_field( $field, $form )  ){
			return $field_structure;
		}

		$field_input_class = $field_structure[ 'field_input_class' ];
		$current_form_count = Caldera_Forms_Render_Util::get_current_form_count();

		$field_id_attr = Caldera_Forms_Field_Util::get_base_id( $field, $current_form_count, $form );
		$field_classes = Caldera_Forms_Field_Util::prepare_field_classes( $field, $form );
		switch ( $type ){
			case 'checkbox' :
			default :
				$field_structure[ 'wrapper_before' ] = str_replace( 'div', 'fieldset', $field_structure ['wrapper_before' ] );

				$field_structure[ 'label_before' ] = '<legend>';
				$field_structure[ 'label_after' ] = '</legend>';

			$field_structure[ 'wrapper_after' ] = str_replace( 'div', 'fieldset', $field_structure ['wrapper_after' ] );
				break;
		}

		return $field_structure;
	}

	public function filter_attrs( $attrs, $field, $form ){
		$type = Caldera_Forms_Field_Util::get_type( $field, $form );
		$current_form_count = Caldera_Forms_Render_Util::get_current_form_count();

		if(  $this->is_new_tmpl_field( $field, $form )  ) {
			$id_attr = Caldera_Forms_Field_Util::get_base_id( $field, $current_form_count, $form );

			if ( in_array( 'form-control', $attrs[ 'class' ] ) ) {
				//Is it worth getting this cute?
				unset( $attrs[ array_search( 'form-control', $attrs[ 'class' ] ) ] );
				$attrs[ 'class' ][] = $type;
			}

			if( !empty( $field['required'] ) ){
				$attrs[ 'class' ][] = ' option-required';
				$attrs[ 'data-parsley-required ' ] = 'true';
				$attrs[ ' data-parsley-group' ] = $id_attr;
				$attrs[ 'data-parsley-multiple' ] = $id_attr;
			}

			if( !  empty( $field['config']['inline']  ) ){
				$attrs[ 'class' ][] = 'cf-new-' . $type . '-inline';
				$attrs[ 'class' ][] = $type . '-inline';
			}





			$attrs[ 'data-' . $type . '-field' ] = Caldera_Forms_Field_Util::get_base_id( $field, $current_form_count, $form );
		}
		return $attrs;
	}

	protected  function has_new($type ){
		return in_array( $type, array(
			'checkbox'
		));
	}
}