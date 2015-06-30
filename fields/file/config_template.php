<div class="caldera-config-group">
	<label for="{{_id}}_attach"><?php echo __('Attach to mailer', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_attach" type="checkbox" class="field-config" name="{{_name}}[attach]" value="1" {{#if attach}}checked="checked"{{/if}}>
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_allow_multiple"><?php echo __('Allow Multiple', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_allow_multiple" type="checkbox" class="field-config" name="{{_name}}[multi_upload]" value="1" {{#if multi_upload}}checked="checked"{{/if}}>
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_allowed"><?php echo __('Allowed Types', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_allowed" type="text" class="field-config" name="{{_name}}[allowed]" value="{{allowed}}">
		<p class="description"><?php echo __('Comma separated eg. jpg,pdf,txt', 'caldera-forms'); ?></p>
	</div>
</div>