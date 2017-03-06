<?php
$fields = array(
	'placeholder',
	'default',
	Caldera_Forms_Admin_UI::checkbox_field(
		'nationalMode',
		__( 'Use Country Code', 'caldera-forms' ),
		array(
			'nationalMode' => __( 'Enable', 'caldera-forms' )
		)
	)
);
Caldera_Forms_Admin_UI::fields( $fields, 'phone_better' );

