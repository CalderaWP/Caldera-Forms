<div class="preview-caldera-config-group">
	{{#unless hide_label}}<lable class="control-label">{{label}}{{#if required}} <span style="color:#ff0000;">*</span>{{/if}}</lable>{{/unless}}
	<div class="preview-caldera-config-field">
		<{{config/element}} class="{{config/classes}}">{{config/before}}0{{#if config/fixed}}.00{{/if}}{{config/after}}</{{config/element}}>
		<span class="help-block">{{caption}}</span>
	</div>
</div>