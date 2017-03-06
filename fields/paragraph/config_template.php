<?php

$fields = array(
	'placeholder',
	Caldera_Forms_Admin_UI::text_field(
		'rows',
		__( 'Rows', 'caldera-forms' )
	),
	Caldera_Forms_Admin_UI::textarea_default( __( 'Default', 'caldera-forms' ) )
);
echo Caldera_Forms_Admin_UI::fields( $fields, 'paragraph' );
