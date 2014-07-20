<?php
if(!empty($field['config']['placeholder'])){
	$field_placeholder = 'placeholder="'.$field['config']['placeholder'].'"';
}
?><div class="<?php echo $field_wrapper_class; ?>">
	<?php echo $field_label; ?>
	<div class="<?php echo $field_input_class; ?>">
		<textarea  <?php echo $field_placeholder; ?> data-field="<?php echo $field_base_id; ?>" class="<?php echo $field_class; ?>" rows="<?php echo $field['config']['rows']; ?>" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" <?php echo $field_required; ?>><?php echo htmlentities( $field_value ); ?></textarea>
		<?php echo $field_caption; ?>
	</div>
</div>

<?php



?>