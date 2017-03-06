<?php
$fields = array(
	Caldera_Forms_Admin_UI::textarea_default( __( 'Content', 'caldera-forms' ) )
);
echo Caldera_Forms_Admin_UI::fields( $fields, 'html' );