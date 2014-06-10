<div class="caldera-config-group">
	<label>Default</label>
	<div class="caldera-config-field">		
		<input type="text" id="{{_id}}_default" class="block-input field-config" name="{{_name}}[default]" value="{{default}}">
	</div>
</div>
<div class="caldera-config-group">
	<label>Style</label>
	<div class="caldera-config-field">
		<label><input type="radio" class="field-config {{_id}}_type local" name="{{_name}}[type]" value="local" {{#is type value="local"}}checked="checked"{{/is}}> Local</label>
		<p class="description">(###)###-####</p>
		<label><input type="radio" class="field-config {{_id}}_type international" name="{{_name}}[type]" value="international" {{#is type value="international"}}checked="checked"{{/is}}> International</label>
		<p class="description">+## (#)## ###-####</p>
	</div>
</div>

{{#script}}
	jQuery(function($){

		$('.{{_id}}_type').change(function(){

			if(this.value === 'local'){
				$('#{{_id}}_default').inputmask("(999)999-9999");
			}else{
				$('#{{_id}}_default').inputmask("+99 (9)99 999-9999");
			}
		});

		$('.{{_id}}_type.{{type}}').trigger('change');
	});
{{/script}}