<?php

// magics!
$syncer = Caldera_Forms_Field_Syncfactory::get_object( $form, $field, Caldera_Forms_Field_Util::get_base_id( $field, null, $form ) );
$sync = $syncer->can_sync();
$default = $syncer->get_default();
if( $sync ){

	echo '<div id="'. esc_attr( $syncer->content_id() ) . '" data-field="' . esc_attr( $field_id ) . '" class="' . esc_attr( $field['config']['custom_class'] ) . '"></div>';

	// create template block
	ob_start();
	echo '<script type="text/html" id="'. esc_attr( $syncer->template_id() ) . '">';
		echo do_shortcode( Caldera_Forms::do_magic_tags( wpautop( $syncer->get_default() ) ) );
	echo '</script>';

	$script_template = ob_get_clean();
	if( ! empty( $form[ 'grid_object' ] ) && is_object( $form[ 'grid_object' ] ) ){
		$form[ 'grid_object' ]->append( $script_template, $field[ 'grid_location' ] );
	}else{
		echo $script_template;
	}
			
}else{
	echo '<div class="' . esc_attr( $field['config']['custom_class'] ) . '">' . do_shortcode( Caldera_Forms::do_magic_tags( $html_template ) ) . '</div>';
}



