<?php echo $wrapper_before; ?>
<?php echo $field_label; ?>
<?php if(false !== strpos($field_input_class, 'has-error')){
	echo '<span class="has-error">';
		echo $field_caption;
	echo '</span>';
}
if( empty( $field['config']['public_key'] ) ){
	$field['config']['public_key'] = null;
}
?>
<?php echo $field_before; ?>
<input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $field_id; ?>" data-field="<?php echo $field_base_id; ?>">
<div id="<?php echo $field_id; ?>" class="g-recaptcha" data-theme="<?php echo $field['config']['theme']; ?>" data-sitekey="<?php echo $field['config']['public_key']; ?>"></div>
<?php echo $field_caption; ?>
<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
<script>
jQuery( function($){
	$(document).on('click', '.reset_<?php echo $field_id; ?>', function(e){
		e.preventDefault();
		var widget = $('#<?php echo $field_id; ?>');
		widget.empty();
		grecaptcha.render( widget[0], { "sitekey" : "<?php echo $field['config']['public_key']; ?>", "theme" : "<?php echo $field['config']['theme']; ?>" } );
	} );
});
</script>