<div class="preview-caldera-config-group">
	{{#unless hide_label}}<lable class="control-label">{{label}}{{#if required}} <span style="color:#ff0000;">*</span>{{/if}}</lable>{{/unless}}
	<div class="preview-caldera-config-field">
		<input {{#if hide_label}}placeholder="{{label}}"{{/if}} type="text" style="max-width:100px;" class="preview-field-config" value="{{config/default}}">
		<span class="help-block">{{caption}}</span>
	</div>
</div>