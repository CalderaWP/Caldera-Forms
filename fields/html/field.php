<?php
$html_template = '';
// magics!
$syncer = Caldera_Forms_Sync_Factory::get_object( $form, $field, Caldera_Forms_Field_Util::get_base_id( $field, null, $form ) );
$sync = $syncer->can_sync();

if( $sync ){
	$default = $syncer->get_default();
	echo '<div id="'. esc_attr( $syncer->content_id() ) . '" data-field="' . esc_attr( $field_id ) . '" class="' . esc_attr( $field['config']['custom_class'] ) . '"></div>';

	// create template block
	ob_start();
	echo '<script type="text/html" id="'. esc_attr( $syncer->template_id() ) . '">';
		echo do_shortcode( Caldera_Forms::do_magic_tags( wpautop( $syncer->get_default() ) ) );
	echo '</script>';

	$script_template = ob_get_clean();
	Caldera_Forms_Render_Util::add_inline_data( $script_template, $form );

}else{
	$html_template = $field[ 'config' ][ 'default' ];
	echo '<div class="' . esc_attr( $field['config']['custom_class'] ) . '">' . do_shortcode( Caldera_Forms::do_magic_tags( $html_template ) ) . '</div>';
}



