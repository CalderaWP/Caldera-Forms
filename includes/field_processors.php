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


function cf_handle_file_upload( $entry, $field, $form ){

	// check transdata if string based entry
	if( is_string( $entry ) ){
		$transdata = get_transient( $entry );
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

		if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
		
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
			$upload = wp_handle_upload($file, array( 'test_form' => false ), date('Y/m') );

			if( !empty( $upload['error'] ) ){
				return new WP_Error( 'fail', $upload['error'] );
			}
			$uploads[] = $upload['url'];
			// check media handler
			if( !empty( $field['config']['media_lib'] ) ){
				// send to media library
				// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );

				// create
				$media_item = array(
					'guid'           => $upload['file'],
					'post_mime_type' => $upload['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $upload['file'] ) ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				);

				// Insert the media_item.
				$media_id = wp_insert_attachment( $media_item, $upload['file'] );

				// Generate the metadata for the media_item, and update the database record.
				$media_data = wp_generate_attachment_metadata( $media_id, $upload['file'] );
				wp_update_attachment_metadata( $media_id, $media_data );

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
