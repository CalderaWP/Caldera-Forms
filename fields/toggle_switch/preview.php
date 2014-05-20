<div class="preview-caldera-config-group">
	{{#unless hide_label}}<lable class="control-label">{{label}}</lable>{{/unless}}
	<div class="preview-caldera-config-field">
		<div class="toggle_option_preview">
		{{#each config/option}}
		<button class="button {{#is default value=true}}button-primary{{/is}}" type="button">{{label}}</button>
		{{/each}}
		</div>
		<span class="help-block">{{caption}}</span>
	</div>
</div>