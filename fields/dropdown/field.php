<div class="<?php echo $field_wrapper_class; ?>">
	<?php echo $field_label; ?>
	<div class="<?php echo $field_input_class; ?>">
		<select <?php echo $field_placeholder; ?> id="<?php echo $field_id; ?>" data-field="<?php echo $field_base_id; ?>" class="<?php echo $field_class; ?>" name="<?php echo $field_name; ?>" <?php echo $field_required; ?>>
		<?php
		if(!empty($field['config']['option'])){
			foreach($field['config']['option'] as $option_key=>$option){
				?>
				<option value="<?php echo $option['value']; ?>" <?php if( $field_value == $option['value'] ){ ?>selected="true"<?php } ?>><?php echo $option['label']; ?></option>
				<?php
			}
		} ?>
		</select>
		<?php echo $field_caption; ?>
	</div>
</div>