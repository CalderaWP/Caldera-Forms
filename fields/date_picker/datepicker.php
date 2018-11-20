<?php
$has_lang = '';

$attrs = array(
	'type' => 'text',
	'data-provide' => 'cfdatepicker',
	'name' => $field_name,
	'value' => $field_value,
	'id' => Caldera_Forms_Field_Util::get_base_id( $field ),
	'data-field' => $field_base_id,
	'class' => $field_class . ' cfdatepicker is-cfdatepicker',
	'data-date-format' => $field['config']['format']
);

$required = '';
if( $field_structure['field_required'] ){
	$attrs[ 'required'] = true;
	$required = 'required';
}

// prevent errors for fields of previous version
$start_end_atts = '';
if( !empty( $field['config']['start_view'] ) ){
	$attrs[  'data-date-start-view' ] = $field['config']['start_view'];
}

if( !empty( $field['config']['start_date'] ) ){
	$attrs[' data-date-start-date' ] = $field['config']['start_date'];
}
if( !empty( $field['config']['end_date'] ) ){
	$attrs[ 'data-date-end-date' ] = $field['config']['end_date'];
}

if( !empty( $field['config']['language'] ) ){
	if( file_exists( CFCORE_PATH . 'fields/date_picker/js/locales/bootstrap-datepicker.' . $field['config']['language'] . '.js' ) ){
		$attrs[ 'data-date-language' ] = $field['config']['language'];
		wp_enqueue_script( 'cf-frontend-date-picker-lang', CFCORE_URL . 'fields/date_picker/js/locales/bootstrap-datepicker.' . $field['config']['language'] . '.js', array( Caldera_Forms_Render_Assets::field_script_to_localize_slug() ), null, true);
	}
}

if( ! empty( $field[ 'hide_label' ] ) ) {
	if( ! empty( $field[ 'config' ][ 'placeholder' ] ) ){
		$place_holder = $field[ 'config' ][ 'placeholder' ];
	}else{
		$place_holder = $field[ 'label' ];
	}

	$attrs[ 'placeholder' ]  = Caldera_Forms::do_magic_tags( $place_holder );

}

// check for autoclose
$is_autoclose = null;
if( !empty( $field['config']['autoclose'] ) ){
	$attrs[ 'data-date-autoclose' ] = 'true';
}

$attr_string =  caldera_forms_field_attributes( $attrs, $field, $form );
?>

<?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<input  <?php echo  $attr_string . ' ' . $field_structure['aria'] . ' ' . $required; ?>  />
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
