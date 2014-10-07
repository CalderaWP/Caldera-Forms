<div class="<?php echo $field_wrapper_class; ?>">
	<?php echo $field_label; ?>
	<div class="<?php echo $field_input_class; ?>">
		<select <?php echo $field_placeholder; ?> id="<?php echo $field_id; ?>" data-field="<?php echo $field_base_id; ?>" class="<?php echo $field_class; ?>" name="<?php echo $field_name; ?>" <?php echo $field_required; ?>>
		<?php

			if(isset( $field['config'] ) && isset($field['config']['default']) && isset($field['config']['option'][$field['config']['default']])){
				//if( $field['config']['option'][$field['config']['default']]['value'] )
				if( $field['config']['default'] === $field_value ){
					$field_value = $field['config']['option'][$field['config']['default']]['value'];
				}

			}else{
				echo '<option value="">' . ( !empty($field['hide_label']) ? $field['label'] : null ) . '</option>';
			}


		if(!empty($field['config']['option'])){
			if(!empty($field['config']['default'])){
				if(!isset($field['config']['option'][$field['config']['default']])){
					echo "<option value=\"\"></option>\r\n";
				}
			}
			foreach($field['config']['option'] as $option_key=>$option){
				if(!isset($option['value'])){
					$option['value'] = htmlspecialchars( $option['label'] );
				}

				?>
				<option value="<?php echo $option['value']; ?>" <?php if( $field_value == $option['value'] ){ ?>selected="true"<?php } ?>><?php echo $option['label']; ?></option>
				<?php
			}
		} ?>
		</select>
		<?php echo $field_caption; ?>
	</div>
</div>