<?php

$elementType = $field['config']['element'];
if(empty($elementType)){
	$elementType = 'div';
}

if(!empty($field['config']['before'])){
	$field['config']['before'] = Caldera_Forms::do_magic_tags($field['config']['before']);
}
if(!empty($field['config']['after'])){
	$field['config']['after'] = Caldera_Forms::do_magic_tags($field['config']['after']);
}


if( !isset( $field['config']['thousand_separator'] ) ){
	$field['config']['thousand_separator'] = ',';
}

if( !isset( $field['config']['decimal_separator'] ) ){
	$field['config']['decimal_separator'] = '.';
}


$target_id = Caldera_Forms_Field_Util::get_base_id( $field, $current_form_count, $form );

$value_field_id = $target_id . '-value';
$attrs = array(
	'type'            => 'hidden',
	'name'            => $field_name,
	'value'           => 0,
	'data-field'      => $target_id,
	'data-calc-field' => $field[ 'ID' ],
	'data-type'       => 'calculation',
	'id'              => $value_field_id
);
$attr_string =  caldera_forms_field_attributes( $attrs, $field, $form );
$syncer = Caldera_Forms_Sync_Factory::get_object( $form, $field, Caldera_Forms_Field_Util::get_base_id( $field, null, $form ) );


?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<<?php echo $elementType . $field_structure['aria']; ?> class="<?php echo $field['config']['classes']; ?>"><?php echo $field['config']['before']; ?>
			<span id="<?php echo esc_attr( $field_id ); ?>" data-calc-display="<?php echo esc_attr( $value_field_id ); ?>"><?php echo $field_value; ?></span><?php echo $field['config']['after']; ?></<?php echo $elementType; ?>>
				<input type="hidden" <?php echo $attr_string; ?> >
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
<?php
Caldera_Forms_Render_Util::add_inline_script( $syncer->get_formula( true ), $form );