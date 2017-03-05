<?php
$fields = array(
	'default',
	'placeholder',
	Caldera_Forms_Admin_UI::select_field(
		'credit_card_field',
		__( 'Credit Card Field', 'caldera-forms' ),
		'{{{_field slug="credit_card_field" type="credit_card_number"  }}}',
		__( 'If set, the type of card will be used for verification.', 'caldera-forms' )
	),

);

echo Caldera_Forms_Admin_UI::fields( $fields, 'credit_card_cvc' );
