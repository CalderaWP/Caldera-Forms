<?php

$mask = '(999)999-9999';
if($field['config']['type'] == 'international'){
	$mask = '+99 (9)99 999-9999';
}

?><div class="<?php echo $field_wrapper_class; ?>">
	<?php echo $field_label; ?>
	<div class="<?php echo $field_input_class; ?>">
		<input <?php echo $field_placeholder; ?> type="text" data-inputmask="'mask': '<?php echo $mask; ?>'" data-field="<?php echo $field_base_id; ?>" class="<?php echo $field_class; ?>" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo htmlentities( $field_value ); ?>" <?php echo $field_required; ?>>
		<?php echo $field_caption; ?>
	</div>
</div>
