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
			$check = pathinfo($_FILES[$field['ID']]['name']);
			if(!in_array( $check['extension'], $types)){
				if(count($types) > 1){
					return array('_fail'=>__('File type not allowed. Allowed types are: ', 'caldera-forms') . ' '. implode(', ', $types) );
				}else{
					return array('_fail'=>__('File type needs to be', 'caldera-forms') . ' .' . $types[0] );	
				}
			}

		}


		$upload = wp_upload_bits( $_FILES[$field['ID']]['name'], null, file_get_contents($_FILES[$field['ID']]['tmp_name']) );

		if(empty($upload['error'])){
			return $upload['url'];
		}else{
			return new WP_Error( 'fail', $upload['error'] );
		}
	}
}