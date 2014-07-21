<div class="preview-caldera-config-group">
	{{#unless hide_label}}<lable class="control-label">{{label}}{{#if required}} <span style="color:#ff0000;">*</span>{{/if}}</lable>{{/unless}}
	<div class="preview-caldera-config-field">
		{{#if config/showval}}<div class="col-xs-9" style="margin: {{#if config/pollyfill}}2px{{else}}6px{{/if}} 0px;">{{else}}<div style="margin: {{#if config/pollyfill}}2px{{else}}6px{{/if}} 0px;">{{/if}}
			<input id="{{id}}_rangeslider" type="range" data-trackcolor="{{config/trackcolor}}" data-color="{{config/color}}" data-handle="{{config/handle}}" data-handleborder="{{config/handleborder}}" min="{{config/min}}" max="{{config/max}}" step="{{config/step}}" value="{{config/default}}" style="width:100%">
		</div>{{#if config/showval}}
		<div class="col-xs-3">
			{{config/prefix}}<span id="{{id}}_value">{{config/default}}</span>{{config/suffix}}
		</div>{{/if}}
		<span class="help-block">{{caption}}</span>
	</div><div class="clearfix"></div>
</div>
{{#script}}
	var rangeslide{{id}} = jQuery('#{{id}}_rangeslider').rangeslider({
		onSlide: function(position, value) {
			jQuery('#{{id}}_value').html(value);
		},
		polyfill: {{#if config/pollyfill}}true{{else}}false{{/if}}
	});
	rangeslide{{id}}.parent().find('.rangeslider').css('backgroundColor', rangeslide{{id}}.data('trackcolor'));
	rangeslide{{id}}.parent().find('.rangeslider__fill').css('backgroundColor', rangeslide{{id}}.data('color'));
	rangeslide{{id}}.parent().find('.rangeslider__handle').css('backgroundColor', rangeslide{{id}}.data('handle'));
	rangeslide{{id}}.parent().find('.rangeslider__handle').css('borderColor', rangeslide{{id}}.data('handleborder'));
{{/script}}