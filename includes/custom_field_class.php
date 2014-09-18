<?php




add_action('caldera_forms_field_settings_template', 'cf_custom_field_classes');
add_filter('caldera_forms_render_field_classes', 'cf_apply_field_classes', 10, 3);


function cf_apply_field_classes($classes, $field, $form){
	
	if(!empty($field['config']['custom_class'])){
		$classes['control_wrapper'] .= ' '.$field['config']['custom_class'];
	}
	return $classes;
}

function cf_custom_field_classes(){
?>
<div class="caldera-config-group">
	<label><?php echo __('Custom Class', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config" name="{{_name}}[custom_class]" value="{{custom_class}}">
	</div>
</div>
<?php
}


add_filter('caldera_forms_get_field_types', 'cf_live_gravatar_field');

function cf_live_gravatar_field($fieldtypes){

	$fieldtypes['live_gravatar'] = array(
		"field"			=>	"Gravatar",
		"file"			=>	CFCORE_PATH . "fields/gravatar/field.php",
		"category"		=>	"User,Special",
		"description" 	=> 'A live gravatar preview',
		"setup"			=>	array(
			"template"	=>	CFCORE_PATH . "fields/gravatar/config.php",
			"preview"	=>	CFCORE_PATH . "fields/gravatar/preview.php",
			"not_supported"	=>	array(
				'entry_list'
			)
		)
	);
	return $fieldtypes;
}


add_action( 'wp_ajax_cf_live_gravatar_get_gravatar', 		'cf_live_gravatar_get_gravatar' );
add_action( 'wp_ajax_nopriv_cf_live_gravatar_get_gravatar', 'cf_live_gravatar_get_gravatar' );


function cf_live_gravatar_get_gravatar(){
	if( empty($_POST['preview'] ) ){
		if( !is_email( $_POST['email'] ) && !empty($_POST['email'])){
			exit;
		}
	}

	if( empty( $_POST['email'] ) && is_user_logged_in() ){
		$_POST['email'] = get_current_user_id();
	}

	echo get_avatar( $_POST['email'], (int) $_POST['size'], $_POST['generator']);	
	exit;
}
