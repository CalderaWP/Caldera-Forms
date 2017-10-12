<div class="caldera-config-group">
	<label><?php echo __('URL', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled required" name="{{_name}}[url]" value="{{url}}">
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('Redirect Message', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled required" name="{{_name}}[message]" value="{{#if message}}{{message}}{{else}}<?php _e('Redirecting', 'caldera-forms'); ?>{{/if}}">
		<p class="description"><?php _e('Message text shown when redirecting in Ajax mode.', 'caldera-forms'); ?></p>
	</div>
</div>