<?php


add_filter('caldera_forms_view_field_checkbox', 'cf_handle_multi_view', 10, 3);
function cf_handle_multi_view( $data, $field ){

	if( empty( $data ) || !is_array( $data ) ){
		return $data;
	}
	// can put in the value as well.
	$viewer = array();

	foreach( $data as $key=>$value ){

		foreach( $field['config']['option'] as $option_key=>$option ){
			if( $value == $option['value'] ){
				$viewer[$key] = $option['label'] . ' (' . $option['value'] . ')';
			}
		}
		if( !isset( $viewer[$key] ) ){
			$viewer[$key] = $value;
		}
		
	}
	return implode( ', ', $viewer );

}


add_filter('caldera_forms_process_field_file', 'cf_handle_file_upload', 10, 3);
add_filter('caldera_forms_process_field_advanced_file', 'cf_handle_file_upload', 10, 3);

/**
 * Handle uploading of files from file fields
 *
 * @since unknown
 *
 * @param $entry
 * @param $field
 * @param $form
 *
 * @return bool|mixed
 */
function cf_handle_file_upload( $entry, $field, $form ){
	if( ! Caldera_Forms_Field_Util::is_file_field( $field, $form ) ){
		return false;
	}

	// check transdata if string based entry
	if( is_string( $entry ) ){
		$transdata = Caldera_Forms_Transient::get_transient( $entry );

		if( !empty( $transdata ) ){

			return $transdata;
		}

	}

	if( isset($_POST[ '_cf_frm_edt' ] ) ) {
		if ( ! isset( $_FILES )
		     || ( isset( $_FILES[ $field[ 'ID' ] ][ 'size' ][0] ) && 0 == $_FILES[ $field[ 'ID' ] ][ 'size' ][0] )
			|| ( isset( $_FILES[ $field[ 'ID' ] ][ 'size' ] ) && 0 == $_FILES[ $field[ 'ID' ] ][ 'size' ]  )
		) {
			$entry = Caldera_Forms::get_field_data( $field[ 'ID' ], $form, absint( $_POST[ '_cf_frm_edt' ] ) );

			return $entry;
		}
	}
	$required = false;
	if ( isset( $field[ 'required' ] ) &&  $field[ 'required' ] ){
		$required = true;
	}
	if(!empty($_FILES[$field['ID']]['size'])){

		// build wp allowed types
		$allowed = get_allowed_mime_types();
		$wp_allowed = array();
		foreach( $allowed as $ext=>$mime ){
			$exts = explode('|', $ext );
			foreach( $exts as $ext ){
				$wp_allowed[ strtolower( $ext ) ] = true;
			}
		}

		// check if user set allowed types
		if(!empty($field['config']['allowed'])){
			$allowed = array_map('trim', explode(',', trim( $field['config']['allowed'] ) ) );
			$field['config']['allowed'] = array();
			foreach( $allowed as $ext ){
				$ext = strtolower( trim( $ext, '.' ) );
				if( in_array($ext, $wp_allowed ) ){
					$field['config']['allowed'][ $ext ] = true;
				}
			}
		}else{
			//set allowed to only what wp allows
			$field['config']['allowed'] = $wp_allowed;
		}

		// check each file now
		foreach( (array) $_FILES[$field['ID']]['name'] as $file_name ){
			if( empty( $file_name ) ){
				return $entry;
			}
			$filetype = wp_check_filetype( basename( $file_name ), null );
			if( empty( $field['config']['allowed'][ strtolower( $filetype['ext'] ) ] ) ){
				return new WP_Error( 'fail', __('This file type is not allowed. Please try another.', 'caldera-forms') );
			}
		}

		if ( ! function_exists( 'wp_handle_upload' ) ) {
		    require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
		
		$files = array();
		foreach( (array) $_FILES[$field['ID']] as $file_key=>$file_parts ){
			foreach( (array) $file_parts as $part_index=>$part_value ){
				$files[ $part_index ][ $file_key ] = $part_value;
			}
		}

		$uploads = array();
		foreach( $files as $file ){
			if( ! $required && 0 == $file[ 'size' ] ){
				continue;
			}

            if( ! Caldera_Forms_Files::is_private( $field ) ){
                $upload_args = array(
                    'private' => false,
                    'field_id' => $field['ID'],
                    'form_id' => $form['ID']
                );
            }else{
                $upload_args = array(
                    'private' => true,
                    'field_id' => $field['ID'],
                    'form_id' => $form['ID']
                );
            }

			$uploader = Caldera_Forms_Files::get_upload_handler( $form, $field );
			if( is_callable( $uploader) ){
				$upload = call_user_func( $uploader, $file, $upload_args );
			}else{
				return new WP_Error( 'invalid-upload-handler', __( sprintf( 'Invalid file upload handler. See %s', ' https://calderaforms.com/doc/alternative-file-upload-directory/'), 'caldera-forms') );
			}

			if( !empty( $upload['error'] ) ){
				return new WP_Error( 'fail', $upload['error'] );
			}
			$uploads[] = $upload['url'];
			// check media handler
			if( !empty( $field['config']['media_lib'] ) ){
                Caldera_Forms_Files::add_to_media_library( $upload, $field );
			}
		}

		if( count( $uploads ) > 1 ){
			return $uploads;
		}

		if( empty( $uploads ) ){
			return array();
		}

		return $uploads[0];
	}else{
		// for multiples
		if( is_array( $entry ) ){
			foreach( $entry as $index => $line ){
				if( !filter_var( $line, FILTER_VALIDATE_URL ) ){
					unset( $entry[ $index ] );
				}
			}
			return $entry;
		}else{
			if( filter_var( $entry, FILTER_VALIDATE_URL ) ){
				return $entry;
			}
		}

	}

}


add_filter( 'caldera_forms_render_get_field_type-hidden', 'caldera_forms_allow_edit_hidden_fields' );

/**
 * When editing a form from entry viewer, convert hidden fields to text fields to so they too can be edited.
 *
 * @since 1.4.3
 *
 * @uses "caldera_forms_render_get_field_type-hidden" filter
 *
 * @param array $field Field config
 *
 * @return mixed
 */
function caldera_forms_allow_edit_hidden_fields( $field ){
	if( ( ! empty( $_GET[ 'modal' ] ) && 'view_entry' == $_GET[ 'modal' ]  ) || ( ! empty( $_POST[ '_cf_frm_edt' ] ) )  ){
		$field[ 'type' ] = 'text';
	}
	return $field;
};

add_filter( 'caldera_forms_validate_field_phone_better', 'caldera_forms_validate_phone_better', 10, 3 );
/**
 * Prevent phone number fields submitted with country code only from being considered valid.
 *
 * @uses "caldera_forms_validate_field_phone_better" filter
 *
 * @since 1.5.2
 *
 * @param string|mixed $entry
 * @param array $field
 * @param array $form
 *
 * @return WP_Error|string
 */
function caldera_forms_validate_phone_better( $entry, $field, $form ){
	if( empty( $field[ 'required' ] ) ){
		return $entry;
	}

	if( false !== strpos( $entry, '+' ) && 4 >= strlen( $entry ) ){
		return new WP_Error( );
	}

	return $entry;
}
