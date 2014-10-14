<?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
		<?php echo $field_before; ?>
			<input <?php echo $field_placeholder; ?> id="<?php echo $field_id; ?>" data-field="<?php echo $field_base_id; ?>" type="text" class="<?php echo $field_class; ?> minicolor-picker init_field_type" data-type="color_picker" name="<?php echo $field_name; ?>" value="<?php echo $field_value; ?>" <?php echo $field_required; ?>>
		<?php echo $field_after; ?>
	<?php echo $field_caption; ?>
<?php echo $wrapper_after; ?>