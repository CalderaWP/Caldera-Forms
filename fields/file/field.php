<?php
	$is_multiple = null;
	if( !empty( $field['config']['multi_upload'] ) ){
		$is_multiple = 'multiple="multiple"';
		$field_name .= '[]';
	}

?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<input <?php echo $field_placeholder; ?> <?php echo $is_multiple; ?> type="file" data-field="<?php echo $field_base_id; ?>" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" <?php echo $field_required; ?>>
		<input type="hidden" name="<?php echo $field_name; ?>" value="true">
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>