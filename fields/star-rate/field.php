<?php
$attrs = array(
	'type'        => 'text',
	'name'        => $field_name,
	'value'       => 2,
	'data-field'  => $field_base_id,
	'class'       => $field_class,
	'id'          => $field_id,
);

$attr_string =  caldera_forms_field_attributes( $attrs, $field, $form );


?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<div style="position: relative;">
			<div id="<?php echo esc_attr( Caldera_Forms_Field_Util::star_target( Caldera_Forms_Field_Util::get_base_id( $field, null, $form ) ) ); ?>" style="color:<?php echo esc_attr( $field['config']['track_color'] ); ?>;font-size:<?php echo floatval( $field['config']['size'] ); ?>px;"></div>
			<input <?php echo $attr_string . ' ' . $field_required; ?> style="position: absolute; width: 0px; height: 0px; padding: 0px; bottom: 0px; left: 12px; opacity: 0; z-index: -1000;" />
		</div>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>

