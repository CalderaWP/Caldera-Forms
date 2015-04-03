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
<div id="cap<?php echo $field_id; ?>" class="g-recaptcha" data-theme="<?php echo $field['config']['theme']; ?>" data-sitekey="<?php echo $field['config']['public_key']; ?>"></div>
<script>
jQuery( function($){
	function init_recaptcha(){
		var captch = $('#cap<?php echo $field_id; ?>');
		captch.empty();
		grecaptcha.render( captch[0], { "sitekey" : "<?php echo $field['config']['public_key']; ?>", "theme" : "<?php echo $field['config']['theme']; ?>" } );
	}
	jQuery(document).on('click', '.reset_<?php echo $field_id; ?>', function(e){
		e.preventDefault();
		init_recaptcha();
	} );
	setTimeout( function(){
		init_recaptcha();
	}, 1000);
});
</script><?php echo $field_caption; ?>
<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
