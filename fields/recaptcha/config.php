<input type="hidden" value="1" name="config[fields][{{_id}}][required]" class="field-config">

<p class="description" style="text-align:center;"><?php _e('reCaptcha required keys from Google.', 'caldera-forms'); ?><a href="https://www.google.com/recaptcha" target="_blank"> Visit reCAPTCHA</a></p>
<div class="caldera-config-group">
	<label><?php _e('Site Key', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config required" name="{{_name}}[public_key]" value="{{public_key}}">
	</div>
</div>
<div class="caldera-config-group">
	<label><?php _e('Secret Key', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config required" name="{{_name}}[private_key]" value="{{private_key}}">
	</div>
</div>
<div class="caldera-config-group">
	<label><?php _e('Theme', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<select class="block-input field-config" name="{{_name}}[theme]">
			<option value="light" {{#is theme value="light"}}selected="selected"{{/is}}><?php _e('Light'); ?></option>
			<option value="dark" {{#is theme value="dark"}}selected="selected"{{/is}}><?php _e('Dark'); ?></option>
		</select>
		<p class="description"><?php _e('Theme changes not available in preview. Update form and reload to see new theme.', 'caldera-forms'); ?></p>
	</div>
</div>
