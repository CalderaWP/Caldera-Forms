<div class="caldera-config-group">
	<label><?php echo __('Increment Start', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		{{#unless start}}
		<input class="block-input field-config required" type="number" name="{{_name}}[start]" value="{{start}}">
		<p><?php echo __('Number to start incrementing.', 'caldera-forms'); ?></p>
		{{else}}
		<p><?php echo __('Incremenets started at {{start}}. to reset, delete this and insert a new increment processor.', 'caldera-forms'); ?></p>
		<input type="hidden" name="{{_name}}[start]" value="{{start}}">
		{{/unless}}
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('Increment Field', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		{{{_field slug="field" type="hidden" exclude="system,variables"}}}
		<p><?php echo __('If you want to show the value in the entries, Select a Hidden field in form to capture the value to.', 'caldera-forms'); ?></p>
	</div>
</div>