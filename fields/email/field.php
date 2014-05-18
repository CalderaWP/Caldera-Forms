<div class="<?php echo $field_wrapper_class; ?>">
	<?php echo $field_label; ?>
	<div class="<?php echo $field_input_class; ?>">
		<input <?php echo $field_placeholder; ?> type="email" class="<?php echo $field_class; ?>" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo htmlentities( $field_value ,ENT_COMPAT|ENT_HTML401, "UTF-8"); ?>">
		<?php echo $field_caption; ?>
	</div>
</div>