<?php
if( ! defined( 'ABSPATH' ) ){
    exit;
}
$fields = array(
	Caldera_Forms_Admin_UI::number_field(
		'width',
		__( 'Width', 'caldera-forms' ),
		__( 'Width of section break element, as a percentage.', 'caldera-forms' ),
		array(
			'min' => 1,
			'max' => 100
		)

	),
);
echo Caldera_Forms_Admin_UI::fields( $fields, 'section-break' );

