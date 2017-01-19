<div class="preview-caldera-config-group">
	{{#unless hide_label}}<label class="control-label">{{label}}{{#if required}} <span style="color:#ff0000;">*</span>{{/if}}</label>{{/unless}}
	<div class="preview-caldera-config-field">
		<textarea rows="{{config/rows}}" style="resize:none;" class="preview-field-config">{{config/default}}</textarea>
		<span class="help-block">{{caption}}</span>
	</div>
</div>