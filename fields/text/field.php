<?php
if(!empty($field['config']['placeholder'])){
	$field_placeholder = 'placeholder="'.$field['config']['placeholder'].'"';
}

$mask = null;
if(!empty($field['config']['masked'])){
	$mask = "data-inputmask=\"'mask': '".$field['config']['mask']."'\" ";
}

?><div class="<?php echo $field_wrapper_class; ?>">
	<?php echo $field_label; ?>
	<div class="<?php echo $field_input_class; ?>">
		<input <?php echo $field_placeholder; ?> type="text" <?php echo $mask; ?> data-field="<?php echo $field_base_id; ?>" class="<?php echo $field_class; ?>" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo htmlentities( $field_value ); ?>" <?php echo $field_required; ?>>
		<?php echo $field_caption; ?>
	</div>
</div>
