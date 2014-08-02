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
