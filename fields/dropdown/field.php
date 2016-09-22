<?php 
	echo $wrapper_before;
	if ( isset( $field[ 'slug' ] ) && isset( $_GET[ $field[ 'slug' ] ] ) ) {
		$field_value = Caldera_Forms_Sanitize::sanitize( $_GET[ $field[ 'slug' ] ] );
	}

?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<select id="<?php echo esc_attr( $field_id ); ?>" data-field="<?php echo esc_attr( $field_base_id ); ?>" class="<?php echo esc_attr( $field_class ); ?>" name="<?php echo esc_attr( $field_name ); ?>" <?php echo $field_required; ?> <?php echo $field_structure['aria']; ?>>
		<?php

			if(isset( $field['config'] ) && isset($field['config']['default']) && isset($field['config']['option'][$field['config']['default']])){

				if( $field['config']['default'] === $field_value ){
					$field_value = $field['config']['option'][$field['config']['default']]['value'];
				}

			}else{
				if( empty( $field['config']['placeholder'] ) ){
					echo '<option value="">' . ( !empty($field['hide_label']) ? $field['label'] : null ) . '</option>';
				}else{
					$sel = '';
					if( empty( $field_value ) ){
						$sel = 'selected';
					}
					$placeholder = Caldera_Forms::do_magic_tags( $field['config']['placeholder'] );

					echo '<option value="" disabled ' . $sel . '>' . $placeholder . '</option>';
				}
			}


		if(!empty($field['config']['option'])){
			if(!empty($field['config']['default'])){
				if(!isset($field['config']['option'][$field['config']['default']])){
					echo "<option value=\"\"></option>\r\n";
				}
			}
			foreach($field['config']['option'] as $option_key=>$option){
				if(!isset($option['value'])){
					$option['value'] = $option['label'];
				}

				?>
				<option value="<?php echo esc_attr( $option['value'] ); ?>" <?php if( $field_value == $option['value'] ){ ?>selected="selected"<?php } ?>><?php echo esc_html( $option['label'] ); ?></option>
				<?php
			}
		} ?>
		</select>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
