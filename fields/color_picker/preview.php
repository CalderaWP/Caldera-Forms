<div class="preview-caldera-config-group cf-color-picker">
	{{#unless hide_label}}<lable class="control-label">{{label}}{{#if required}} <span style="color:#ff0000;">*</span>{{/if}}</lable>{{/unless}}
	<div class="preview-caldera-config-field">
		<input {{#if hide_label}}placeholder="{{label}}"{{/if}} type="text" style="max-width:100px;" class="preview-field-config minicolor-picker" data-type="color_picker" value="{{config/default}}"><span style="background-color: {{config/default}};" class="preview-color-selector" data-for="{{id}}"></span>
		<span class="help-block">{{caption}}</span>
	</div>
</div>