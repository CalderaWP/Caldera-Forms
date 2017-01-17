<div class="caldera-config-group">
	<label for="{{_id}}_orientation">
        <?php esc_html_e('Orientation', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<select id="{{_id}}_orientation" name="{{_name}}[orientation]" class="block-input field-config">
		<option value="horizontal" {{#is orientation value="horizontal"}}selected="selected"{{/is}}><?php esc_html_e('Horizontal', 'caldera-forms'); ?></option>
		<option value="justified" {{#is orientation value="justified"}}selected="selected"{{/is}}><?php esc_html_e('Justified', 'caldera-forms'); ?></option>
		<option value="vertical" {{#is orientation value="vertical"}}selected="selected"{{/is}}><?php esc_html_e('Vertical', 'caldera-forms'); ?></option>
		</select>
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_activeclass">
        <?php esc_html_e('Active Class', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input id="{{_id}}_activeclass"type="text" value="{{#if selected_class}}{{selected_class}}{{else}}btn-success{{/if}}" name="{{_name}}[selected_class]" class="block-input field-config">
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_inactiveclass">
        <?php esc_html_e('Inactive Class', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input id="{{_id}}_inactiveclass"type="text" value="{{#if default_class}}{{default_class}}{{else}}btn-default{{/if}}" name="{{_name}}[default_class]" class="block-input field-config">
	</div>
</div>