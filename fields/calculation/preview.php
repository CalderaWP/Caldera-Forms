<div class="preview-caldera-config-group">
	{{#unless hide_label}}<lable class="control-label">{{label}}{{#if required}} <span style="color:#ff0000;">*</span>{{/if}}</lable>{{/unless}}
	<div class="preview-caldera-config-field">
		{{#if config/element}}<{{config/element}} class="{{config/classes}}">{{/if}}{{config/before}}0{{#if config/fixed}}.00{{/if}}{{config/after}}{{#if config/element}}</{{config/element}}>{{/if}}
		<span class="help-block">{{caption}}</span>
	</div>
</div>