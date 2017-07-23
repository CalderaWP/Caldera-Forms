<?php
	$is_multiple = null;
	$has_preview = !empty($field['config']['image_with_preview']);

	if( $has_preview ) {
		 $wrapper_before = str_replace('class="', 'class=" has-drag-n-drop ', $wrapper_before);
	}

	if( !$has_preview && !empty( $field['config']['multi_upload'] ) ){
		$is_multiple .= ' multiple="multiple"';
	}
	$uniqu_code = uniqid('trupl');		
	$required_check = '';
	if( $field_required !== null ){
		$required_check = 'required="required"';
		$field_required = null;
		$is_multiple .= ' data-required="true"';

	}
	if( empty( $field['config']['multi_upload_text'] ) ){
		$field['config']['multi_upload_text'] = __( 'Add File', 'caldera-forms' );
		if( !empty( $field['config']['multi_upload'] ) ){
			$field['config']['multi_upload_text'] = __( 'Add Files', 'caldera-forms' );
		}
	}
	$accept_tag = array();
	if ($has_preview) {
		$accept_tag = array('.jpg,.jpeg,.jpe,.gif,.png');
	} else {
		if( !empty( $field['config']['allowed'] ) ){
			$allowed = array_map('trim', explode(',', trim( $field['config']['allowed'] ) ) );
			$field['config']['allowed'] = array();
			foreach( $allowed as $ext ){
				$ext = trim( $ext, '.' );
				$file_type = wp_check_filetype( 'tmp.'. $ext );
				$field['config']['allowed'][] = $file_type['type'];
				$accept_tag[] = '.' . $ext;
			}
		}else{
			$allowed = get_allowed_mime_types();
			$field['config']['allowed'] = array();
			foreach( $allowed as $ext=>$mime ){
				$field['config']['allowed'][] = $mime;
				$accept_tag[] = '.' . str_replace('|', ',.', $ext );
			}
		}
	}

	$accept_tag = 'accept="' . esc_attr( implode(',', $accept_tag) ) . '"';

	$field['config']['max_size'] = wp_max_upload_size();

	$field['config']['notices'] = array(
		'file_exceeds_size_limit' => esc_html__( 'File exceeds the maximum upload size for this site.', 'caldera-forms' ),
		'zero_byte_file' => esc_html__( 'This file is empty. Please try another.', 'caldera-forms' ),
		'invalid_filetype' => esc_html__( 'This file type is not allowed. Please try another.', 'caldera-forms' ),
	);

?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<div id="<?php echo esc_attr( $field_id ); ?>_file_list" data-id="<?php echo esc_attr( $field_id ); ?>" data-field="<?php echo esc_attr( $field_base_id ); ?>" class="cf-multi-uploader-list"></div>

		<button id="<?php echo esc_attr( $field_id ); ?>_trigger" type="button" class="btn btn-block cf-uploader-trigger" data-parent="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['config']['multi_upload_text'] ); ?></button>

		<input style="display:none;" <?php echo $accept_tag; ?> class="cf-multi-uploader" data-config="<?php echo esc_attr( json_encode( $field['config'] ) ); ?>" data-controlid="<?php echo esc_attr( $uniqu_code ); ?>" <?php echo $field_placeholder; ?> <?php echo $is_multiple; ?> type="file" data-field="<?php echo esc_attr( $field_base_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" <?php echo $field_required; ?>>
		<input style="display:none;" type="text" id="<?php echo esc_attr( $field_id ); ?>_validator" data-parsley-file-type="true" <?php echo $required_check; ?>>
		<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $uniqu_code ); ?>">
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>

<style type="text/css" media="screen">
	.form-group.has-drag-n-drop {
		border: 3px dashed #ccc;
		padding: 10px;
		text-align: center;
	}

	.form-group.has-drag-n-drop .btn {
		background: #fafafa;
		border: 1px solid #ccc;
		font-weight: 700;
		padding: 10px;
	}

	.form-group.has-drag-n-drop label {
		font-size: 1.5em;
		color: rgba(0,0,0, .3);
		font-weight: 700;
	}
</style>