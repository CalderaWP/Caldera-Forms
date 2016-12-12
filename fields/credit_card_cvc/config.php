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
	<label for="{{_id}}_default">
		<?php esc_html_e('Credit Card Field', 'caldera-forms' ); ?>
	</label>
	<div class="caldera-config-field">
		{{{_field slug="credit_card_field" type="credit_card_number"  }}}
	</div>
	<p class="description">
		<?php esc_html_e( 'If set, the type of card will be used for verification.', 'caldera-forms' ); ?>
	</p>
</div>


