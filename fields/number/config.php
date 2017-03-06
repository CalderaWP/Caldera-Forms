<?php
$fields = array(
	'placeholder',
	'default',
	Caldera_Forms_Admin_UI::text_field(
		'min',
		__( 'Minimum', 'caldera-forms' )
	),
	Caldera_Forms_Admin_UI::text_field(
		'max',
		__( 'Maximum', 'caldera-forms' )
	),
	Caldera_Forms_Admin_UI::text_field(
		'step',
		__( 'Step (increment size)', 'caldera-forms' )
	),


);

echo Caldera_Forms_Admin_UI::fields( $fields, 'number' );