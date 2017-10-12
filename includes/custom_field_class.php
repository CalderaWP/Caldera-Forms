<?php




add_action('caldera_forms_field_settings_template', 'cf_custom_field_classes');
add_filter('caldera_forms_render_field_classes', 'cf_apply_field_classes', 10, 3);


function cf_apply_field_classes($classes, $field, $form){
	
	if(!empty($field['config']['custom_class'])){
		$classes['control_wrapper'][] = $field['config']['custom_class'];
	}
	return $classes;
}

function cf_custom_field_classes(){
?>
<div class="caldera-config-group customclass-field">
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
		"category"		=>	__( 'Special' , 'caldera-forms' ),
		"description" 	=> 'A live gravatar preview',
		'icon'          => CFCORE_URL . 'assets/build/images/user.svg',
		"setup"			=>	array(
			"template"	=>	CFCORE_PATH . "fields/gravatar/config.php",
			"preview"	=>	CFCORE_PATH . "fields/gravatar/preview.php",
			"not_supported"	=>	array(
				'entry_list',
				'custom_class'
			)
		)
	);
	return $fieldtypes;
}


add_action( 'wp_ajax_cf_live_gravatar_get_gravatar', 		'cf_live_gravatar_get_gravatar' );
add_action( 'wp_ajax_nopriv_cf_live_gravatar_get_gravatar', 'cf_live_gravatar_get_gravatar' );


function cf_live_gravatar_get_gravatar(){
	$defaults = array(
		'email'	=> '',
		'generator' => 'mystery',
		'size' => 100
	);
	$defaults = array_merge( $defaults, $_POST );
	echo get_avatar( Caldera_Forms::do_magic_tags( $defaults['email'] ), (int) $defaults['size'], $defaults['generator']);
	exit;
}

// field specific stuff.
add_filter( 'caldera_forms_render_field_classes_type-file', 'caldera_forms_file_field_class' );
function caldera_forms_file_field_class($classes){
	$classes['field_wrapper'][] = "file-prevent-overflow";
	return $classes;
}
add_filter( 'caldera_forms_render_field_classes_type-toggle_switch', 'caldera_forms_toggle_switch_field_class' );
function caldera_forms_toggle_switch_field_class($classes){
	$classes['control_wrapper'][] = "cf-toggle-switch";
	return $classes;
}
add_filter( 'caldera_forms_render_field_classes_type-color_picker', 'caldera_forms_color_picker_field_class' );
function caldera_forms_color_picker_field_class($classes){
	$classes['field_wrapper'][] = "input-group";
	$classes['control_wrapper'][] = "minicolor-picker";
	return $classes;
}
