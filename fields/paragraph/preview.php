<div class="preview-caldera-config-group">
	{{#unless hide_label}}<lable class="control-label">{{label}}</lable>{{/unless}}
	<div class="preview-caldera-config-field">
		<textarea {{#if hide_label}}placeholder="{{label}}"{{/if}} rows="{{config/rows}}" style="resize:none;" class="preview-field-config">{{config/default}}</textarea>
		<span class="help-block">{{caption}}</span>
	</div>
</div>