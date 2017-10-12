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
        <?php esc_html_e('Default', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input type="text" id="{{_id}}_default" class="block-input field-config" name="{{_name}}[default]" value="{{default}}">
	</div>
</div>

<div class="caldera-config-group">
	<fieldset>
		<legend>
			<?php esc_html_e('Use Country Code', 'caldera-forms'); ?>
		</legend>

		<input type="checkbox" id="{{_id}}_nationalMode" class="field-config" name="{{_name}}[nationalMode]" {{#if nationalMode}}checked="checked"{{/if}}">
		<label for="{{_id}}_nationalMode">
			<?php esc_html_e( 'If not checked, formatting will be for local use, without country code', 'caldera-forms' ); ?>
		</label>
	</fieldset>
</div>

