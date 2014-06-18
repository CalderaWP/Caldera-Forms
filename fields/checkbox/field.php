<div class="<?php echo $field_wrapper_class; ?>">
	<?php echo $field_label; ?>
	<div class="<?php echo $field_input_class; ?>">
		<?php
		if(empty($field['config']['option'])){ ?>
			
			<input type="checkbox" id="<?php echo $field_id; ?>" data-field="<?php echo $field_base_id; ?>" class="field-config" name="<?php echo $field_name; ?>" value="1" <?php if(!empty($field_value)){ ?>checked="true"<?php } ?>>

		<?php }else{
			foreach($field['config']['option'] as $option_key=>$option){
				?>
				<?php if(empty($field['config']['inline'])){ ?>
				<div class="checkbox">
				<?php } ?>
				<label<?php if(!empty($field['config']['inline'])){ ?> class="checkbox-inline"<?php } ?>><input type="checkbox" data-field="<?php echo $field_base_id; ?>" id="<?php echo $field_id . '_' . $option_key; ?>" class="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>[<?php echo $option_key; ?>]" value="<?php echo $option['value']; ?>" <?php if( in_array( $option['value'], (array) $field_value) || in_array( $option_key, (array) $field_value) ){ ?>checked="true"<?php } ?>> <?php echo $option['label']; ?></label>&nbsp;
				<?php if(empty($field['config']['inline'])){ ?>
				</div>
				<?php } ?>
				<?php
			}
		} ?>
		<?php echo $field_caption; ?>
	</div>
</div>