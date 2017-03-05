<?php
$fields = array(
	Caldera_Forms_Admin_UI::text_field(
		'default_color',
		__( 'Default Color', 'caldera-forms' )
	),
);
echo Caldera_Forms_Admin_UI::fields( $fields, 'color_picker' );

