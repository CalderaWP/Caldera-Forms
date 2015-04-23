<?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<?php

		$req_class = '';
		if( !empty( $field['required'] ) ){
			$req_class = ' option-required';
		}

		if(!empty($field['config']['option'])){
			
			if(isset( $field['config'] ) && isset($field['config']['default']) && isset($field['config']['option'][$field['config']['default']])){
				//if( $field['config']['option'][$field['config']['default']]['value'] )
				if( $field['config']['default'] === $field_value ){
					$field_value = (array) $field['config']['option'][$field['config']['default']]['value'];
				}

			}
			
			/*<input type="checkbox" id="<?php echo $field_id; ?>" data-field="<?php echo $field_base_id; ?>" class="field-config" name="<?php echo $field_name; ?>" value="1" <?php if(!empty($field_value)){ ?>checked="true"<?php } ?>>
			*/
			
			foreach($field['config']['option'] as $option_key=>$option){
				if(!isset($option['value'])){
					$option['value'] = htmlspecialchars( $option['label'] );
				}
				?>
				<?php if(empty($field['config']['inline'])){ ?>
				<div class="checkbox">
				<?php } ?>
				<label<?php if(!empty($field['config']['inline'])){ ?> class="checkbox-inline"<?php } ?> for="<?php echo $field_id . '_' . $option_key; ?>"><input type="checkbox" data-label="<?php echo esc_attr( $option['label'] );?>" data-field="<?php echo $field_base_id; ?>" id="<?php echo $field_id . '_' . $option_key; ?>" class="<?php echo $field_id . $req_class; ?>" name="<?php echo $field_name; ?>[<?php echo $option_key; ?>]" value="<?php echo $option['value']; ?>" <?php if( in_array( $option['value'], (array) $field_value) ){ ?>checked="checked"<?php } ?>> <?php echo $option['label']; ?></label>&nbsp;
				<?php if(empty($field['config']['inline'])){ ?>
				</div>
				<?php } ?>
				<?php
			}
		} ?>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
