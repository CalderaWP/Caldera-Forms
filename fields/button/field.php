<?php

$btnType = $field['config']['type'];
$btn_action = null;
if($field['config']['type'] == 'next' || $field['config']['type'] == 'prev'){
	$btnType = 'button';
	$btn_action = 'data-page="'.$field['config']['type'].'"';

}


?><?php echo $wrapper_before; ?><?php echo $field_before; ?><input data-field="<?php echo $field_base_id; ?>" <?php echo $btn_action; ?> class="<?php echo $field['config']['class']; ?>" type="<?php echo $btnType; ?>" value="<?php echo $field['label']; ?>" id="<?php echo $field_id; ?>"><?php echo $field_after; ?><?php echo $wrapper_after; ?>