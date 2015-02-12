{{#if config/public_key}}
	{{#if config/private_key}}
		<div id="cap{{id}}" class="g-recaptcha" data-sitekey="{{config/public_key}}" data-theme="{{config/theme}}"></div>
		{{#script}}

		jQuery(document).ready( function(){
			if( typeof grecaptcha === 'object' ){
				
				var captch = jQuery('#cap{{id}}');

				grecaptcha.render( captch[0], {
					"sitekey"	:	"{{config/public_key}}",
					"theme"		:	"{{config/theme}}"
				});
			}
		});

		{{/script}}
	{{else}}
		<p><?php _e('No Secret Key Added', 'caldera-forms'); ?></p>
	{{/if}}
{{else}}
	<p><?php _e('No Site Key Added', 'caldera-forms'); ?></p>
{{/if}}