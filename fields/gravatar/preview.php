<div style="text-align:center;" class="grav-trigger" data-preview="true" data-autoload="true" data-size="{{config/size}}" data-generator="{{config/generator}}" data-email="{{config/email}}" data-action="cf_live_gravatar_get_gravatar" data-target="#grav{{id}}"><span style="overflow: hidden;border-radius:{{#if config/border_radius}}{{config/border_radius}}{{else}}3{{/if}}px; border:{{config/border_size}}px solid {{#if config/border_color}}{{config/border_color}}{{else}}#efefef{{/if}};display: inline-block;"><span style="border-radius:{{#if config/border_radius}}{{config/border_radius}}{{else}}3{{/if}}px;width:{{#if config/size}}{{config/size}}{{else}}100{{/if}}px;height:{{#if config/size}}{{config/size}}{{else}}100{{/if}}px;display:block;" id="grav{{id}}"></span></span></div>

{{#script}}
	jQuery('.grav-trigger').baldrick({
		request     : ajaxurl
	});
{{/script}}