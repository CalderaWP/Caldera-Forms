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
	<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_id ); ?>" data-field="<?php echo esc_attr( $field_base_id ); ?>">
	<div id="cap<?php echo $field_id; ?>" class="g-recaptcha" data-theme="<?php echo $field['config']['theme']; ?>" data-sitekey="<?php echo $field['config']['public_key']; ?>"></div>

	<?php echo $field_caption; ?>
<?php echo $field_after; ?>
<?php echo $wrapper_after;

ob_start();
?>
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
		});
	</script><?php

$script_template = ob_get_clean();
Caldera_Forms_Render_Util::add_inline_data( $script_template, $form );
