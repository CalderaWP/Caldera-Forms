<div class="preview-caldera-config-group">
	{{#unless hide_label}}<lable class="control-label">{{label}}{{#if required}} <span style="color:#ff0000;">*</span>{{/if}}</lable>{{/unless}}
	<div class="preview-caldera-config-field">
		<div class="toggle_option_preview{{#is config/orientation value="vertical"}} toggle_vertical{{/is}}">
		{{#each config/option}}
		<button class="button {{#is ../config/option value="@key"}}button-primary{{/is}}" type="button">{{label}}</button>
		{{/each}}
		</div>
		<span class="help-block">{{caption}}</span>
	</div>
</div>