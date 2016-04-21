<?php
	$is_multiple = null;
	if( !empty( $field['config']['multi_upload'] ) ){
		$is_multiple .= ' multiple="multiple"';
	}
	$uniqu_code = uniqid('trupl');		
	if( $field_required !== null ){
		$field_required = null;
		$is_multiple .= ' data-required="true"';
	}
	if( empty( $field['config']['multi_upload_text'] ) ){
		$field['config']['multi_upload_text'] = __( 'Add File', 'caldera-forms' );
		if( !empty( $field['config']['multi_upload'] ) ){
			$field['config']['multi_upload_text'] = __( 'Add Files', 'caldera-forms' );
		}
	}

?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<div id="<?php echo $field_id; ?>_file_list" data-field="<?php echo $field_id; ?>" class="cf-multi-uploader-list"></div>

		<button type="button" class="btn btn-block cf-uploader-trigger" data-parent="<?php echo $field_id; ?>"><?php echo esc_html( $field['config']['multi_upload_text'] ); ?></button>

		<input style="display:none;" class="cf-multi-uploader" data-controlid="<?php echo $uniqu_code; ?>" <?php echo $field_placeholder; ?> <?php echo $is_multiple; ?> type="file" data-field="<?php echo $field_base_id; ?>" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" <?php echo $field_required; ?>>
		<input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $uniqu_code; ?>">
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>