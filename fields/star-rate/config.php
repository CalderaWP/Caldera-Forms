<div class="caldera-config-group">
	<label for="{{_id}}_number"><?php echo __('Number of Stars', 'caldera-forms-register'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_number" type="number" class="field-config" name="{{_name}}[number]" value="{{number}}" style="width:70px;">
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_size"><?php echo __('Star Size', 'caldera-forms-register'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_size" type="number" class="field-config" name="{{_name}}[size]" value="{{size}}" style="width:70px;">px
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_space"><?php echo __('Star Spacing', 'caldera-forms-register'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_space" type="number" class="field-config" name="{{_name}}[space]" value="{{space}}" style="width:70px;">px
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_single"><?php echo __('Single Select', 'caldera-forms-register'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_single" type="checkbox" class="field-config" name="{{_name}}[single]" value="1" {{#if single}}checked="checked"{{/if}}>
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_color"><?php echo __('Star Color', 'caldera-forms-register'); ?></label>
	<div class="caldera-config-field">
		<input type="text" class="minicolor-picker field-config" id="{{_id}}_color" name="{{_name}}[color]" value="{{color}}">
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_cancel"><?php echo __('Include Cancel', 'caldera-forms-register'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_cancel" type="checkbox" class="field-config" name="{{_name}}[cancel]" value="1" {{#if cancel}}checked="checked"{{/if}}>
	</div>
</div>

{{#script}}
jQuery(function($){
	jQuery('#{{_id}}_color').miniColors();
});
{{/script}}