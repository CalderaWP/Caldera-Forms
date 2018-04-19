<div class="preview-caldera-config-group">
	{{#unless hide_label}}<lable class="control-label">{{label}}{{#if required}} <span style="color:#ff0000;">*</span>{{/if}}</lable>{{/unless}}
	<div class="preview-caldera-config-field">
		<div id="{{id}}_stars_preview" style="color:{{config/track_color}};font-size:{{config/size}}px;"></div>
		<span class="help-block">{{caption}}</span>
	</div>
</div>
{{#script}}
	jQuery('#{{id}}_stars_preview').raty({
		starOff	: 'raty-{{config/type}}-off',
		starOn : 'raty-{{config/type}}-on',
		hints: [1,2,3,4,5],
		spaceWidth: {{config/space}},
		number: {{config/number}},
		starType: 'f',
		starColor: '{{config/color}}',
		trackColor: '{{config/track_color}}',
		numberMax: 100
		{{#if config/cancel}},cancel: true{{/if}}
		{{#if config/single}},single: true{{/if}}
	});
{{/script}}