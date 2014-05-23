{{#script}}
Recaptcha.create("{{config/public_key}}",
"cap{{id}}",{
	theme: "{{config/theme}}"
});
{{/script}}
<div id="cap{{id}}"></div>