<?php

if ( is_array( $field_value ) )  {
	if ( isset( $field_value[0] ) ) {
		$field_value = $field_value[0];
	}else{
		$field_value = ' ';
	}

}

$attrs = array(
	'name'        => $field_name,
	'value'       => $field_value,
	'data-field'  => $field_base_id,
	'class'       => $field_class,
	'id'          => $field_id,
);


if(!empty($field['config']['placeholder'])){


	$attrs[ 'placeholder' ] = Caldera_Forms::do_magic_tags( $field['config']['placeholder'] );
}

$attr_string =  caldera_forms_field_attributes( $attrs, $field, $form );

$placeholder = '';

?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<textarea <?php echo $attr_string . ' ' . $field_required . ' ' . $field_structure['aria']; ?>>
			<?php echo esc_html( $field_value ); ?>
		</textarea>
	<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
