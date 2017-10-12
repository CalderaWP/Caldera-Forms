<div class="preview-caldera-config-group">
	{{#unless hide_label}}<lable class="control-label">{{label}}{{#if required}} <span style="color:#ff0000;">*</span>{{/if}}</lable>{{/unless}}
	<div class="preview-caldera-config-field">		
		<select class="preview-field-config" {{#if hide_label}}placeholder="{{label}}"{{/if}}>
		<option value=""></option>
		{{#each config/option}}
			<option {{#is ../config/default value="@key"}}selected="selected"{{/is}}>{{label}}</option>
		{{/each}}
		</select>
		<span class="help-block">{{caption}}</span>
	</div>
</div>