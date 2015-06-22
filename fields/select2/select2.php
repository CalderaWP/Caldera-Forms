<?php



add_filter(	'caldera_forms_process_field_filtered_select2', 'caldera_forms_select2_populate_array' );
add_action( 'caldera_forms_autopopulate_types', 'caldera_forms_select2_populate_options' );
add_filter( 'caldera_forms_render_get_field_type-filtered_select2', 'caldera_forms_filter_select_populate' );


function caldera_forms_select2_populate_array( $entry ){
	
	if( !is_array( $entry ) ){
		return $entry;
	}

	$new_entry = array();
	foreach( $entry as $value ){
		$new_entry[$value] = $value;
	}

	return $new_entry;
}

// auto populate options
function caldera_forms_select2_populate_options(){
	echo "<option value=\"users\"{{#is auto_type value=\"users\"}} selected=\"selected\"{{/is}}>" . __( 'Users' ) . "</option>";
	echo "<option value=\"org_admin_users\"{{#is auto_type value=\"org_admin_users\"}} selected=\"selected\"{{/is}}>" . __( 'Users Organization Admins' ) . "</option>";
}

// add filter options
function caldera_forms_filter_select_populate( $field ){

	if( $field['config']['auto_type'] == 'org_admin_users' ){
		$args = array(
			'meta_key'		=>	'user_type',
			'meta_value'	=>	'organization_admin'
		);
		$users = get_users( $args );
		if( !empty( $users ) ){
			$field['config']['option'] = array();
			foreach( $users as $user ){
				$field['config']['option'][ $user->ID ] = array(
					'value'	=>	$user->ID,
					'label'	=>	$user->data->display_name
				);
			}
		}

	}elseif( $field['config']['auto_type'] == 'users' ){
		$users = get_users();
		if( !empty( $users ) ){
			$field['config']['option'] = array();
			foreach( $users as $user ){
				$field['config']['option'][ $user->ID ] = array(
					'value'	=>	$user->ID,
					'label'	=>	$user->data->display_name
				);
			}
		}

	}
	
	return $field;

}
