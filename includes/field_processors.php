<?php




add_filter('caldera_forms_process_field_file', 'cf_handle_file_upload', 10, 3);


function cf_handle_file_upload($entry, $field, $form){

	if(!empty($_FILES[$field['ID']]['size'])){
		// check is allowed 
		if(!empty($field['config']['allowed'])){
			$types = explode(',',$field['config']['allowed']);

			foreach($types as &$type){
				$type=trim($type);
			}
			foreach( (array) $_FILES[$field['ID']]['name'] as $file_name ){
				if( empty( $file_name ) ){
					return $entry;
				}
				$check = pathinfo( $file_name );
				if(!in_array( $check['extension'], $types)){
					if(count($types) > 1){
						return new WP_Error( 'fail', __('File type not allowed. Allowed types are: ', 'caldera-forms') . ' '. implode(', ', $types) );
					}else{
						return new WP_Error( 'fail', __('File type needs to be', 'caldera-forms') . ' .' . $types[0] );					
					}
				}
			}

		}
		if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
		
		$files = array();
		foreach( (array) $_FILES[$field['ID']] as $file_key=>$file_parts ){
			foreach( (array) $file_parts as $part_index=>$part_value ){
				$files[ $part_index ][ $file_key ] = $part_value;
			}
		}
		foreach( $files as $file ){
			$upload = wp_handle_upload($file, array( 'test_form' => false ), date('Y/m') );

			if( !empty( $upload['error'] ) ){
				return new WP_Error( 'fail', $upload['error'] );
			}
			$uploads[] = $upload['url'];
		}

		if( count( $uploads ) > 1 ){
			return $uploads;
		}
		return $uploads[0];
	}
}