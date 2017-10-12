<div class="caldera-config-group">
    <label for="{{_id}}_placeholder">
        <?php esc_html_e('Placeholder', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input type="text" id="{{_id}}_placeholder" class="block-input field-config" name="{{_name}}[placeholder]" value="{{placeholder}}">
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_rows">
        <?php esc_html_e('Rows', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input id="{{_id}}_rows" type="text" class="block-input field-config" name="{{_name}}[rows]" value="{{rows}}">
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_defaults">
        <?php esc_html_e('Default', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<textarea id="{{_id}}_defaults" class="block-input field-config magic-tag-enabled" name="{{_name}}[default]">{{default}}</textarea>
	</div>
</div>