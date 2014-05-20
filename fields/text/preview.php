<div class="preview-caldera-config-group">
	{{#unless hide_label}}<lable class="control-label">{{label}}</lable>{{/unless}}
	<div class="preview-caldera-config-field">
		<input {{#if hide_label}}placeholder="{{label}}"{{/if}} type="text" class="preview-field-config" value="{{config/default}}">
		<span class="help-block">{{caption}}</span>
	</div>
</div>