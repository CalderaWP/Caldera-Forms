<?php

$btnType = $field['config']['type'];
$btn_action = null;
if($field['config']['type'] == 'next' || $field['config']['type'] == 'prev'){
	$btnType = 'button';
	$btn_action = 'data-page="'.$field['config']['type'].'"';

}

?><?php echo $wrapper_before; ?><?php echo $field_before; ?><input data-field="<?php echo $field_base_id; ?>_btn" <?php echo $btn_action; ?> class="<?php echo $field['config']['class']; ?>" type="<?php echo $btnType; ?>" name="<?php echo $field_name; ?>_btn" value="<?php echo esc_attr( $field['label'] ); ?>" id="<?php echo $field_id; ?>"><?php echo $field_after; ?><?php echo $wrapper_after; ?>
<input class="button_trigger_<?php echo $current_form_count; ?>" type="hidden" data-field="<?php echo $field_base_id; ?>" id="<?php echo $field_id; ?>_btn" name="<?php echo $field_name; ?>" value="<?php echo esc_attr( $field_value ); ?>" autocomplete="off">
<script>
	jQuery( function($){

		$(document).on('click dblclick', '#<?php echo $field_id; ?>', function( e ){
			$('#<?php echo $field_id; ?>_btn').val( e.type ).trigger('change');
		});

	});
</script>