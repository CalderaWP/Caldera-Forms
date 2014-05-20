<div class="preview-caldera-config-group">
	{{#unless hide_label}}<lable class="control-label">{{label}}</lable>{{/unless}}
	<div class="preview-caldera-config-field">
		<select class="preview-field-config" {{#if hide_label}}placeholder="{{label}}"{{/if}}>
		{{#each config/option}}
			<option {{#is default value=true}}selected="selected"{{/is}}>{{label}}</option>
		{{/each}}
		</select>
		<span class="help-block">{{caption}}</span>
	</div>
</div>