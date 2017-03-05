<?php
$fields = array(
	'default',
	'placeholder',
	Caldera_Forms_Admin_UI::select_field(
		'exp',
		__( 'Expiration Field', 'caldera-forms' ),
		'{{{_field slug="exp" type="credit_card_exp"  }}}',
		__( 'If set, the type of card will be used for verification.', 'caldera-forms' )
	),
	Caldera_Forms_Admin_UI::select_field(
		'exp',
		__( 'Expiration Field', 'caldera-forms' ),
		'{{{_field slug="exp" type="credit_card_exp"  }}}',
		__( 'Link an expiration field for validation.', 'caldera-forms' )
	),
	Caldera_Forms_Admin_UI::select_field(
		'cvc',
		__( 'Secret Code Field', 'caldera-forms' ),
		'{{{_field slug="exp" type="credit_card_cvc"  }}}',
		__( 'Link a secret code field for validation.', 'caldera-forms' )
	),

);

echo Caldera_Forms_Admin_UI::fields( $fields, 'credit_card_number' );

