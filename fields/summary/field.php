<?php
/**
 * Summary field
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
$syncer = Caldera_Forms_Sync_Factory::get_object( $form, $field, Caldera_Forms_Field_Util::get_base_id( $field, null, $form ) );
$el_classes = array( 'caldera-forms-summary-field' );
if( ! empty( $field[ 'config' ][ 'custom_class' ]  ) ){
	$el_classes[] = $field[ 'config' ][ 'custom_class' ];
}

$attrs = array(
	'data-field' =>$field[ 'ID'],
	'class' => $el_classes,
	'id' => $syncer->content_id(),
	'name' => $field_structure['name'],
	'value' => Caldera_Forms_Field_HTML::find_default( $field, $form ),
	'data-type' => 'summary'
);

$attr_string = caldera_forms_field_attributes(
	$attrs,
	$field,
	$form
);

$syncer = Caldera_Forms_Sync_Factory::get_object( $form, $field, Caldera_Forms_Field_Util::get_base_id( $field, null, $form ) );
$sync = $syncer->can_sync();

if( $sync ){
	$default = $syncer->get_default();
	echo '<div ' . $attr_string . '></div>';

	// create template block
	ob_start();
	echo '<script type="text/html" id="'. esc_attr( $syncer->template_id() ) . '">';
	echo $syncer->get_default();
	echo '</script>';

	$script_template = ob_get_clean();
	if( ! empty( $form[ 'grid_object' ] ) && is_object( $form[ 'grid_object' ] ) ){
		$form[ 'grid_object' ]->append( $script_template, $field[ 'grid_location' ] );
	}else{
		echo $script_template;
	}

}