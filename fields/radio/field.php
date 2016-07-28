<?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<?php

		$req_class = '';
		if( !empty( $field['required'] ) ){
			$req_class = ' option-required';
		}


		if(empty($field['config']['option'])){

			if(isset( $field['config'] ) && isset($field['config']['default']) && isset($field['config']['option'][$field['config']['default']])){

				if( $field['config']['default'] === $field_value ){
					$field_value = $field['config']['option'][$field['config']['default']]['value'];
				}

			}


			?>
			
			<input type="radio" id="<?php echo esc_attr( $field_id ); ?>" data-field="<?php echo esc_attr( $field_base_id ); ?>" class="field-config<?php echo $req_class; ?>" name="<?php echo esc_attr( $field_name ); ?>" value="1" <?php if(!empty($field_value)){ ?>checked="checked"<?php } ?>>

		<?php }else{
			foreach($field['config']['option'] as $option_key=>$option){
				if(!isset($option['value'])){
					$option['value'] = $option['label'];
				}

				?>
				<?php if(empty($field['config']['inline'])){ ?>
				<div class="radio">
				<?php } ?>
				<label<?php if(!empty($field['config']['inline'])){ ?> class="radio-inline"<?php } ?> data-label="<?php echo esc_attr( $option['label'] ); ?>" for="<?php echo esc_attr( $field_id . '_' . $option_key ); ?>"><input type="radio" id="<?php echo esc_attr( $field_id . '_' . $option_key ); ?>" data-field="<?php echo esc_attr( $field_base_id ); ?>" class="<?php echo esc_attr( $field_id . $req_class ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $option['value'] ); ?>" <?php if( $field_value == $option['value'] || $field_value == $option_key ){ ?>checked="checked"<?php } ?> <?php echo $field_required; ?>> <?php echo $option['label']; ?></label>&nbsp;
				<?php if(empty($field['config']['inline'])){ ?>
				</div>
				<?php } ?>
				<?php
			}
		} ?>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>