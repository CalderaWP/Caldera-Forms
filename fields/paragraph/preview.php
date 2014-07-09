<div class="preview-caldera-config-group">
	{{#unless hide_label}}<lable class="control-label">{{label}}{{#if required}} <span style="color:#ff0000;">*</span>{{/if}}</lable>{{/unless}}
	<div class="preview-caldera-config-field">
		<textarea {{#if hide_label}}placeholder="{{label}}"{{else}}placeholder="{{config/placeholder}}"{{/if}} rows="{{config/rows}}" style="resize:none;" class="preview-field-config">{{config/default}}</textarea>
		<span class="help-block">{{caption}}</span>
	</div>
</div>