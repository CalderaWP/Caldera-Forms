<div class="<?php echo $field_wrapper_class; ?>">
	<?php echo $field_label; ?>
	<div class="<?php echo $field_input_class; ?>">
		<input <?php echo $field_placeholder; ?> type="file" data-field="<?php echo $field_base_id; ?>" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" <?php echo $field_required; ?>>
		<input type="hidden" name="<?php echo $field_name; ?>" vale="true">
		<?php echo $field_caption; ?>
	</div>
</div>