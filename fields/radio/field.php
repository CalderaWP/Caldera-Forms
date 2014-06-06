<div class="<?php echo $field_wrapper_class; ?>">
	<?php echo $field_label; ?>
	<div class="<?php echo $field_input_class; ?>">
		<?php
		if(empty($field['config']['option'])){ ?>
			
			<input type="radio" id="<?php echo $field_id; ?>" data-field="<?php echo $field_base_id; ?>" class="field-config" name="<?php echo $field_name; ?>" value="1" <?php if(!empty($field_value)){ ?>checked="true"<?php } ?>>

		<?php }else{
			foreach($field['config']['option'] as $option_key=>$option){
				?>
				<?php if(empty($field['config']['inline'])){ ?>
				<div class="radio">
				<?php } ?>
				<label<?php if(!empty($field['config']['inline'])){ ?> class="radio-inline"<?php } ?>><input type="radio" id="<?php echo $field_id . '_' . $option_key; ?>" data-field="<?php echo $field_base_id; ?>" class="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo $option['value']; ?>" <?php if( $field_value == $option['value'] ){ ?>checked="true"<?php } ?> <?php echo $field_required; ?>> <?php echo $option['label']; ?></label>&nbsp;
				<?php if(empty($field['config']['inline'])){ ?>
				</div>
				<?php } ?>
				<?php
			}
		} ?>
		<?php echo $field_caption; ?>
	</div>
</div>