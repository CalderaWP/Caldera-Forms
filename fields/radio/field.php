<?php echo $wrapper_before; ?>
<?php echo $field_label; ?>
<?php echo $field_before; ?>
<?php

$req_class = '';
if( !empty( $field['required'] ) ){
	$req_class = ' option-required';
}


$field_value = Caldera_Forms_Field_Util::find_select_field_value( $field, $field_value );


if(empty($field['config']['option'])){
	?>

	<input type="radio" id="<?php echo esc_attr( $field_id ); ?>" data-field="<?php echo esc_attr( $field_base_id ); ?>" class="field-config<?php echo $req_class; ?>" name="<?php echo esc_attr( $field_name ); ?>" value="1" <?php if(!empty($field_value)){ ?>checked="checked"<?php } ?> data-radio-field="<?php echo esc_attr( $field_id ); ?> data-type="radio" data-calc-value="<?php echo esc_attr( Caldera_Forms_Field_Util::get_option_calculation_value( $option, $field, $form ) ); ?>" />

<?php }else{
	foreach($field['config']['option'] as $option_key=>$option){
		$checked = false;
		$disabled = false;
		if( $field_value === $option['value'] ) {
			$checked = true;
		}

		if(!isset($option['value'])){
			$option['value'] = $option['label'];
		}

		if( ! empty( $option['disabled' ] ) ){
			$disabled = true;
		}

		?>
		<?php if(empty($field['config']['inline'])){ ?>
			<div class="radio">
		<?php } ?>
		<label<?php if(!empty($field['config']['inline'])){ ?> class="radio-inline"<?php } ?> data-label="<?php echo esc_attr( $option['label'] ); ?>" for="<?php echo esc_attr( $field_id . '_' . $option_key ); ?>"><input type="radio" id="<?php echo esc_attr( Caldera_Forms_Field_Util::opt_id_attr( $field_id, $option_key ) ); ?>" data-field="<?php echo esc_attr( $field_base_id ); ?>" class="<?php echo esc_attr( $field_id . $req_class ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $option['value'] ); ?>" <?php if( $checked ){ ?>checked="checked"<?php } ?> <?php if( $disabled ){ ?>disabled<?php } ?> <?php echo $field_required; ?> data-radio-field="<?php echo esc_attr( $field_id ); ?>" data-type="radio" data-calc-value="<?php echo esc_attr( Caldera_Forms_Field_Util::get_option_calculation_value( $option, $field, $form ) ); ?>" />
			<?php echo $option['label']; ?>
		</label>
		<?php if(empty($field['config']['inline'])){ ?>
			</div>
		<?php } ?>
		<?php
	}
} ?>
<?php echo $field_caption; ?>
<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>