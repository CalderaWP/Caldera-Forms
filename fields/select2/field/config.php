<div class="caldera-config-group">
	<label>
		<?php _e( 'Placeholder', 'caldera-forms' ); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" id="{{_id}}_placeholder" class="block-input field-config" name="{{_name}}[placeholder]" value="{{placeholder}}">
	</div>
</div>
<div class="caldera-config-group">
	<label>
		<?php _e( 'Multiple', 'caldera-forms'); ?>
	</label>
	<div class="caldera-config-field">
		<label>
			<input type="checkbox" id="{{_id}}_is_multi" class="field-config" name="{{_name}}[multi]" value="1" {{#if multi}}checked="checked"{{/if}}> <?php _e( 'Enable multiple selections', 'caldera-forms'); ?></label>
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_color">
		<?php _e('Highlight', 'caldera-forms'); ?>
	</label>
	<div class="caldera-config-field">
		<input id="{{_id}}_color" style="width: 120px;" type="text" class="minicolor-picker field-config" name="{{_name}}[color]" value="{{#if color}}{{color}}{{else}}#5b9dd9{{/if}}">
		<span id="{{_id}}_color_preview" class="preview-color-selector" style="margin-left: -27px; padding-bottom: 4px; padding-top: 4px; background-color: {{#if color}}{{color}}{{else}}#5b9dd9{{/if}};"></span>
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_border">
		<?php _e( 'Border', 'caldera-forms' ); ?>
	</label>
	<div class="caldera-config-field">
		<input id="{{_id}}_border" style="width: 120px;" type="text" class="minicolor-picker field-config" name="{{_name}}[border]" value="{{#if border}}{{border}}{{else}}#4b8dc9{{/if}}">
		<span id="{{_id}}_border_preview" class="preview-color-selector" style="margin-left: -27px; padding-bottom: 4px; padding-top: 4px; background-color: {{#if border}}{{border}}{{else}}#4b8dc9{{/if}};"></span>
	</div>
</div>

{{#script}}
jQuery(function($){
	jQuery('#{{_id}}_color').miniColors({
		change: function(hex, opacity) {
			jQuery('#{{_id}}_color_preview').css('background-color', hex);
		}
	});
	jQuery('#{{_id}}_border').miniColors({
		change: function(hex, opacity) {
			jQuery('#{{_id}}_border_preview').css('background-color', hex);
		}
	});
});
{{/script}}





