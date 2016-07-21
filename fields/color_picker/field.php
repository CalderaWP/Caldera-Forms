<?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
		<?php echo $field_before; ?>
			<input <?php echo $field_placeholder; ?> id="<?php echo esc_attr( $field_id ); ?>" data-field="<?php echo esc_attr( $field_base_id ); ?>" type="text" class="<?php echo esc_attr( $field_class ); ?> minicolor-picker init_field_type" data-type="color_picker" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_value ); ?>" <?php echo $field_required; ?> <?php echo $field_structure['aria']; ?>>
		<?php echo $field_after; ?>
	<?php echo $field_caption; ?>
<?php echo $wrapper_after; ?>