<?php
$has_lang = '';

// prevent errors for fields of previous version
$start_end_atts = '';
if( !empty( $field['config']['start_view'] ) ){
	$start_end_atts .= ' data-date-start-view="' . esc_attr( $field['config']['start_view'] ) . '"';
}
if( !empty( $field['config']['start_date'] ) ){
	$start_end_atts .= ' data-date-start-date="' . esc_attr( $field['config']['start_date'] ) . '"';
}
if( !empty( $field['config']['end_date'] ) ){
	$start_end_atts .= ' data-date-end-date="' . esc_attr( $field['config']['end_date'] ) . '"';
}

if( !empty( $field['config']['language'] ) ){
	if( file_exists( CFCORE_PATH . 'fields/date_picker/js/locales/bootstrap-datepicker.' . $field['config']['language'] . '.js' ) ){
		$has_lang = 'data-date-language="' . $field['config']['language'] . '"';
		wp_enqueue_script( 'cf-frontend-date-picker-lang', CFCORE_URL . 'fields/date_picker/js/locales/bootstrap-datepicker.' . $field['config']['language'] . '.js', array('cf-field'), null, true);
	}
}

// check for autoclose
$is_autoclose = null;
if( !empty( $field['config']['autoclose'] ) ){
	$is_autoclose = 'data-date-autoclose="true"';
}

?>
<?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<input <?php echo $field_placeholder; ?> type="text" data-provide="cfdatepicker" data-field="<?php echo esc_attr( $field_base_id ); ?>" class="<?php echo esc_attr( $field_class ); ?>  is-cfdatepicker" id="<?php echo esc_attr( $field_id ); ?>" <?php echo $has_lang; ?> data-date-format="<?php echo $field['config']['format']; ?>" <?php echo $start_end_atts; ?> <?php echo $is_autoclose; ?> name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_value ); ?>" <?php echo $field_required; ?> <?php echo $field_structure['aria']; ?> data-form-id="<?php echo esc_attr( $form[ 'ID' ] ); ?>" >
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
