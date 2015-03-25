<?php

$has_lang = '';
if( !empty( $field['config']['language'] ) ){
	if( file_exists( CFCORE_PATH . 'fields/date_picker/js/locales/bootstrap-datepicker.' . $field['config']['language'] . '.js' ) ){
		$has_lang = 'data-date-language="' . $field['config']['language'] . '"';
		wp_enqueue_script( 'cf-frontend-date-picker-lang', CFCORE_URL . 'fields/date_picker/js/locales/bootstrap-datepicker.' . $field['config']['language'] . '.js', array('cf-frontend-fields'), null, true);
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
		<input <?php echo $field_placeholder; ?> type="text" data-provide="cfdatepicker" data-field="<?php echo $field_base_id; ?>" class="<?php echo $field_class; ?>  is-cfdatepicker" id="<?php echo $field_id; ?>" <?php echo $has_lang; ?> data-date-format="<?php echo $field['config']['format']; ?>" data-date-start-view="<?php echo $field['config']['start-view']; ?>" <?php echo $is_autoclose; ?> name="<?php echo $field_name; ?>" value="<?php echo $field_value; ?>" <?php echo $field_required; ?>>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>