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
		__( 'Link an expiration field for validation.', 'caldera-forms' )
	),

);

echo Caldera_Forms_Admin_UI::fields( $fields, 'credit_card_number' );

?>

<div class="caldera-config-group">
	<label for="{{_id}}_placeholder">
		<?php esc_html_e('Placeholder', 'caldera-forms'); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" id="{{_id}}_placeholder" class="block-input field-config" name="{{_name}}[placeholder]" value="{{placeholder}}">
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_default">
		<?php esc_html_e('Default', 'caldera-forms' ); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" id="{{_id}}_default" class="block-input field-config magic-tag-enabled" name="{{_name}}[default]" value="{{default}}">
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_exp">
		<?php esc_html_e('Expiration Field', 'caldera-forms' ); ?>
	</label>
	<div class="caldera-config-field">
		{{{_field slug="exp" type="credit_card_exp"  }}}
	</div>
	<p class="description">
		<?php esc_html_e( 'Link an expiration field for validation.', 'caldera-forms' ); ?>
	</p>
</div>


<div class="caldera-config-group">
	<label for="{{_id}}_cvc">
		<?php esc_html_e('Secret Code Field', 'caldera-forms' ); ?>
	</label>
	<div class="caldera-config-field">
		{{{_field slug="exp" type="credit_card_cvc"  }}}
	</div>
	<p class="description">
		<?php esc_html_e( 'Link a secret code field for validation.', 'caldera-forms' ); ?>
	</p>
</div>