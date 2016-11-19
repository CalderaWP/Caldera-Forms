<?php
if(!empty($field['config']['placeholder'])){
	$placeholder = Caldera_Forms::do_magic_tags( $field['config']['placeholder'] );
	$field_placeholder = 'placeholder="'. esc_attr( $placeholder ).'"';
}

$syncer = new Caldera_Forms_Field_Sync( $form, $field, $field_base_id );
$sync = $syncer->can_sync();
$default = $syncer->get_default();

?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<input <?php echo $field_placeholder; ?> type="email" data-field="<?php echo esc_attr( $field_base_id ); ?>" class="<?php echo esc_attr( $field_class ); ?>" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo htmlentities( $default ); ?>" <?php echo $field_required; ?> <?php echo $field_structure['aria']; ?> <?php if( ! empty( $sync ) ){ ?>data-binds="<?php echo esc_attr(wp_json_encode( $syncer->get_binds() )); ?>" data-sync="<?php echo esc_attr( $default ); ?>"<?php } ?>>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>