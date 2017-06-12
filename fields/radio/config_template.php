<div class="caldera-config-group">

	<div class="caldera-config-field">
		<label for="{{_id}}_inline">
            <input id="{{_id}}_inline" type="checkbox" class="field-config" name="{{_name}}[inline]" value="1" {{#if inline}}checked="checked"{{/if}}>
            <?php esc_html_e('Inline', 'caldera-forms'); ?>
        </label>
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_default">
		<?php esc_html_e('Default Value'); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" id="{{_id}}_default" class="block-input field-config magic-tag-enabled" name="{{_name}}[default]" value="{{default}}" placeholder='Matched against values'>
	</div>
</div>