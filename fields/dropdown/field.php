<?php 
	echo $wrapper_before;
	if ( isset( $field[ 'slug' ] ) && isset( $_GET[ $field[ 'slug' ] ] ) ) {
		$field_value = Caldera_Forms_Sanitize::sanitize( $_GET[ $field[ 'slug' ] ] );
	}

$attrs = array(
	'name' => $field_name,
	'value' => $field_value,
	'data-field' => $field_base_id,
	'class' => $field_class,
	'id' => $field_id,
);
$attr_string =  caldera_forms_field_attributes( $attrs, $field, $form );

?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<select <?php echo $attr_string . ' ' . $field_required . ' ' . $field_structure['aria']; ?> >
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
