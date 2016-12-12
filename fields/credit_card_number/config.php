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