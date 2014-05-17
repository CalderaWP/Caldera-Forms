<div class="<?php echo $field_wrapper_class; ?>">
	<?php echo $field_label; ?>
	<div class="<?php echo $field_input_class; ?>">
		<?php
		if(empty($field['config']['option'])){ ?>
			
			<input type="checkbox" id="<?php echo $field_id; ?>" class="field-config" name="<?php echo $field_name; ?>" value="1" <?php if(!empty($field_value)){ ?>checked="true"<?php } ?>>

		<?php }else{
			foreach($field['config']['option'] as $option_key=>$option){
				?>
				<div><label><input type="checkbox" id="<?php echo $field_id . '_' . $option_key; ?>" class="field-config" name="<?php echo $field_name; ?>" value="<?php echo $option['value']; ?>" <?php if( $field_value == $option['value'] ){ ?>checked="true"<?php } ?>> <?php echo $option['label']; ?></label></div>
				<?php
			}
		} ?>
		<?php echo $field_caption; ?>
	</div>
</div>