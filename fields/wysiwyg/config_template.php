<?php

$fields = array(
	Caldera_Forms_Admin_UI::textarea_default( __( 'Default', 'caldera-forms' ) ),
	Caldera_Forms_Admin_UI::text_field(
		'language',
		__( 'Language Code', 'caldera-forms' )
	),
	Caldera_Forms_Admin_UI::select_field(
		'allowed',
		__( 'Sanitization Level', 'caldera-forms' ),
		array(
			'post' =>  __( 'Permissive', 'caldera-forms' ),
			'data' =>  __( 'Restrictive', 'caldera-forms' )
		)
	)
);
echo Caldera_Forms_Admin_UI::fields( $fields, 'wysiwyg' );
