<?php
if(!empty($field['config']['placeholder'])){
	$field_placeholder = 'placeholder="'. esc_attr(Caldera_Forms::do_magic_tags($field['config']['placeholder'])) .'"';
}

$validator = Caldera_Forms_Field_Util::better_phone_validator( $field_id );

?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<input <?php echo $field_placeholder; ?>
			type="tel"  data-field="<?php echo esc_attr( $field_base_id ); ?>"
			class="<?php echo esc_attr( $field_class ); ?>"
			id="<?php echo esc_attr( $field_id ); ?>"
			name="<?php echo esc_attr( $field_name ); ?>"
			value="<?php echo esc_attr( $field_value ); ?>"
			<?php echo $field_required; ?>
			<?php echo $field_structure['aria']; ?>
			data-parsley="<?php echo esc_attr( $validator ); ?>" >
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
