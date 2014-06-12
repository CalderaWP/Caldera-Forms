<div class="<?php echo $field_wrapper_class; ?>">
	<?php echo $field_label; ?>
	<div class="<?php echo $field_input_class; ?>">
		<input <?php echo $field_placeholder; ?> type="text" data-provide="datepicker" data-field="<?php echo $field_base_id; ?>" class="<?php echo $field_class; ?>  is-datepicker" id="<?php echo $field_id; ?>" data-date-format="<?php echo $field['config']['format']; ?>" name="<?php echo $field_name; ?>" value="<?php echo $field_value; ?>" <?php echo $field_required; ?>>
		<?php echo $field_caption; ?>
	</div>
</div>