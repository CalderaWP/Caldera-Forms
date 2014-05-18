<div class="<?php echo $field_wrapper_class; ?>">
	<?php echo $field_label; ?>
	<div class="<?php echo $field_input_class; ?>">
		<textarea  <?php echo $field_placeholder; ?> class="<?php echo $field_class; ?>" rows="<?php echo $field['config']['rows']; ?>" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" <?php echo $field_required; ?>><?php echo htmlentities( $field_value ,ENT_COMPAT|ENT_HTML401, "UTF-8"); ?></textarea>
		<?php echo $field_caption; ?>
	</div>
</div>

<?php



?>