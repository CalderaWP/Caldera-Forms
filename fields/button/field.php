<?php

$btnType = $field['config']['type'];
$btn_action = null;
if($field['config']['type'] == 'next' || $field['config']['type'] == 'prev'){
	$btnType = 'button';
	$btn_action = 'data-page="'.$field['config']['type'].'"';
}elseif( $field['config']['type'] == 'button' && !empty( $field['config']['target'] ) ){
	$field['config']['class'] .= ' cf-form-trigger';
	$btn_action = 'data-target="'. esc_attr( $field['config']['target'] ).'"';


	wp_enqueue_script( 'cf-form-object' );


}



?>
<?php echo $wrapper_before; ?>
<?php if( !empty( $field['config']['label_space'] ) ){ ?>
<label class="control-label">&nbsp;</label>
<?php } ?>
<?php echo $field_before; ?>
<input data-field="<?php echo esc_attr( $field_base_id ); ?>" <?php echo $btn_action; ?> class="<?php echo esc_attr( $field['config']['class'] ); ?>" type="<?php echo esc_attr( $btnType ); ?>" name="<?php echo esc_attr( $field_name ); ?>_btn" value="<?php echo esc_attr( $field['label'] ); ?>" id="<?php echo esc_attr( $field_id ); ?>" <?php echo $field_structure['aria']; ?>>
<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
<input class="button_trigger_<?php echo $current_form_count; ?>" type="hidden" data-field="<?php echo esc_attr( $field_base_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>_btn" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_value ); ?>">
<?php
ob_start();
?>
<script>	
	window.addEventListener("load", function(){

		jQuery(document).on('click dblclick', '#<?php echo $field_id; ?>', function( e ){
			jQuery('#<?php echo $field_id; ?>_btn').val( e.type ).trigger('change');
		});

	});
</script>
<?php
	$script_template = ob_get_clean();
	if( ! empty( $form[ 'grid_object' ] ) && is_object( $form[ 'grid_object' ] ) ){
		$form['grid_object']->append( $script_template, $field['grid_location'] );
	}else{
		echo $script_template;
	}
