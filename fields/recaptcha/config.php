<div class="caldera-config-group">
	<label><?php _e('Public Key', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config required" name="{{_name}}[public_key]" value="{{public_key}}">
	</div>
</div>
<div class="caldera-config-group">
	<label><?php _e('Private Key', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config required" name="{{_name}}[private_key]" value="{{private_key}}">
	</div>
</div>
<div class="caldera-config-group">
	<label><?php _e('Theme', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<select class="block-input field-config" name="{{_name}}[theme]">
			<option value="red" {{#is theme value="red"}}selected="selected"{{/is}}><?php _e('Red'); ?></option>
			<option value="white" {{#is theme value="white"}}selected="selected"{{/is}}><?php _e('White'); ?></option>
			<option value="blackglass" {{#is theme value="blackglass"}}selected="selected"{{/is}}><?php _e('Black'); ?></option>
			<option value="clean" {{#is theme value="clean"}}selected="selected"{{/is}}><?php _e('Clean', 'caldera-forms'); ?></option>
		</select>
		<p class="description"><?php _e('Theme changes not available in preview. Update form and reload to see new theme.', 'caldera-forms'); ?></p>
	</div>
</div>
