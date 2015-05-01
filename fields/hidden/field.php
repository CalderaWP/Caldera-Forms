<?php
if ( is_array( $field_value ) )  {
	if ( isset( $field_value[0] ) ) {
		$field_value = $field_value[0];
	}else{
		$field_value = ' ';
	}

}
?>
<input type="hidden" id="<?php echo $field_id; ?>" data-field="<?php echo $field_base_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo htmlentities( $field_value ); ?>">
