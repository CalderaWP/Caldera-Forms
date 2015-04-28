<?php
if(!empty($field['config']['placeholder'])){
	$field_placeholder = 'placeholder="'.$field['config']['placeholder'].'"';
}

$mask = '(999)999-9999';
if($field['config']['type'] == 'international'){
	$mask = '+99 99 999 9999';
}

?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<input <?php echo $field_placeholder; ?> type="text" data-inputmask="'mask': '<?php echo $mask; ?>'" data-field="<?php echo $field_base_id; ?>" class="<?php echo $field_class; ?>" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo htmlentities( $field_value ); ?>" <?php echo $field_required; ?>>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
