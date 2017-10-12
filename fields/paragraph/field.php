<?php
if ( is_array( $field_value ) )  {
	if ( isset( $field_value[0] ) ) {
		$field_value = $field_value[0];
	}else{
		$field_value = ' ';
	}

}

$syncer = Caldera_Forms_Sync_Factory::get_object( $form, $field, $field_base_id );
$sync = $syncer->can_sync();
$field_value = $default = $syncer->get_default();


$attrs = array(
	'name' => $field_name,
	'value' => $field_value,
	'data-field' => $field_base_id,
	'class' => $field_class,
	'id' => $field_id,
);

if( ! empty( $field['config']['rows'] ) ){
	$attrs[ 'rows' ] = absint( $field[ 'config' ][ 'rows' ] );
}else{
	$attrs[ 'rows' ] = 5;
}

if(!empty($field['config']['placeholder'])){
	$attrs[ 'placeholder' ] = Caldera_Forms::do_magic_tags( $field['config']['placeholder'] );
}

if( isset( $entry_data, $entry_data[ $field[ 'ID' ] ] ) ){
	$field['config']['default'] = $entry_data[ $field[ 'ID' ] ];
}

if(!empty($field['config']['default'])){
	$attrs[ 'default' ] = $field_value =  Caldera_Forms::do_magic_tags( $field['config']['default'] );
}


if( ! empty( $sync ) ){
	$attrs[ 'data-binds' ] = wp_json_encode($syncer->get_binds() );
	$attrs[ 'data-sync' ] = $field['config']['default'];

}


$attr_string =  caldera_forms_field_attributes( $attrs, $field, $form );


?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<textarea <?php echo $attr_string . ' ' . $field_required . ' ' . $field_structure['aria']; ?> ><?php echo esc_html( $field_value ); ?></textarea>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
