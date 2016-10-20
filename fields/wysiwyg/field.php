<?php

if ( is_array( $field_value ) )  {
	if ( isset( $field_value[0] ) ) {
		$field_value = $field_value[0];
	}else{
		$field_value = ' ';
	}

}

$placeholder = '';
if(!empty($field['config']['placeholder'])){
	$placeholder = Caldera_Forms::do_magic_tags( $field['config']['placeholder'] );

	$field_placeholder = 'placeholder="'. esc_attr( $placeholder ) .'"';
}


?><?php echo $wrapper_before; ?>
<?php echo $field_label; ?>
<?php echo $field_before; ?>
<textarea <?php echo $field_placeholder; ?> data-field="<?php echo esc_attr( $field_base_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" <?php echo $field_required; ?> <?php echo $field_structure['aria']; ?>><?php echo esc_html( $field_value ); ?></textarea>
<?php echo $field_caption; ?>
<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
