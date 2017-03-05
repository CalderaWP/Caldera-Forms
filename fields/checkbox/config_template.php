<?php
$fields = array(
	Caldera_Forms_Admin_UI::checkbox_field(
		'inline',
		__( 'Inline', 'caldera-forms' ),
		array(
			'inline' => __( 'Enable', 'caldera-forms' )
		)
	),
);
echo Caldera_Forms_Admin_UI::fields( $fields, 'calculation' );
