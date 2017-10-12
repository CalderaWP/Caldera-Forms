<div class="caldera-config-group">
	<label><?php echo __('Name', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled required" name="{{_name}}[sender_name]" value="{{sender_name}}">
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('Email', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled caldera-field-bind required" id="{{_id}}_sender_email" name="{{_name}}[sender_email]" value="{{sender_email}}">
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('URL', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled caldera-field-bind" id="{{_id}}_url" name="{{_name}}[url]" value="{{url}}">
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('Content', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled caldera-field-bind" id="{{_id}}_content" name="{{_name}}[content]" value="{{content}}">
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __('Error Message', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled caldera-field-bind" id="{{_id}}_error" name="{{_name}}[error]" value="{{#if error}}{{error}}{{else}}<?php echo __('Sorry, that looked very spammy, try rephrasing things', 'caldera-forms'); ?>{{/if}}">
	</div>
</div>
