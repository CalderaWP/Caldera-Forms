<div class="<?php echo $field_wrapper_class; ?> cf-color-picker">
	<?php echo $field_label; ?>
	<div class="picker-row">
		<div class="<?php echo $field_input_class; ?> col-sm-5 col-md-4 input-group">
			<input <?php echo $field_placeholder; ?> id="<?php echo $field_id; ?>" data-field="<?php echo $field_base_id; ?>" type="text" class="<?php echo $field_class; ?> minicolor-picker init_field_type" data-type="color_picker" name="<?php echo $field_name; ?>" value="<?php echo $field_value; ?>" <?php echo $field_required; ?>>
		</div>
	</div>
	<?php echo $field_caption; ?>
</div>