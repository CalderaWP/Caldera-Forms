<div class="caldera-config-group">
	<label><?php echo __('Orientation', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<select name="{{_name}}[orientation]" class="block-input field-config">
		<option value="horizontal" {{#is orientation value="horizontal"}}selected="selected"{{/is}}><?php echo __('Horizontal', 'caldera-forms'); ?></option>
		<option value="justified" {{#is orientation value="justified"}}selected="selected"{{/is}}><?php echo __('Justified', 'caldera-forms'); ?></option>
		<option value="vertical" {{#is orientation value="vertical"}}selected="selected"{{/is}}><?php echo __('Vertical', 'caldera-forms'); ?></option>
		</select>
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __('Active Class', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<input type="text" value="{{#if selected_class}}{{selected_class}}{{else}}btn-success{{/if}}" name="{{_name}}[selected_class]" class="block-input field-config">
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __('Inactive Class', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<input type="text" value="{{#if default_class}}{{default_class}}{{else}}btn-default{{/if}}" name="{{_name}}[default_class]" class="block-input field-config">
	</div>
</div>