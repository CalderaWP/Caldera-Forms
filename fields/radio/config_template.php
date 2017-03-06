<?php

$fields = array(
	Caldera_Forms_Admin_UI::checkbox_field(
		'inline',
		__( 'Inline Options', 'caldera-forms' ),
		array(
			'inline' => __( 'Inline Options', 'caldera-forms' ),
		)
	)
);
echo Caldera_Forms_Admin_UI::fields( $fields, 'radio' );