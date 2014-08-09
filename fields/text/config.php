<div class="caldera-config-group">
	<label>Placeholder</label>
	<div class="caldera-config-field">
		<input type="text" id="{{_id}}_placeholder" class="block-input field-config" name="{{_name}}[placeholder]" value="{{placeholder}}">
	</div>
</div>
<div class="caldera-config-group">
	<label>Default</label>
	<div class="caldera-config-field">
		<input type="text" id="{{_id}}_default" class="block-input field-config magic-tag-enabled" name="{{_name}}[default]" value="{{default}}">
	</div>
</div>

<div class="caldera-config-group">
	<label>Masked Input</label>
	<div class="caldera-config-field">
		<label><input type="checkbox" class="field-config {{_id}}_masked" name="{{_name}}[masked]" value="1" {{#if masked}}checked="checked"{{/if}}> Enable input mask</label>
	</div>
</div>
<div class="caldera-config-group" id="{{_id}}_maskwrap">
	<label>Mask</label>
	<div class="caldera-config-field">		
		<input type="text" id="{{_id}}_mask" class="block-input field-config" name="{{_name}}[mask]" value="{{mask}}">
	</div>
	<p class="description">e.g (aaa-99-999-a9-9*)</p>
	<ul>
		<li>9 : numeric</li>
		<li>a : alphabetical</li>
		<li>* : alphanumeric</li>
		<li>[9 | a | *] : optional</li>
		<li>{int | * | +} : length</li>
	</ul>
	<p class="description">Any length character only: [a{*}]</p>
	<p class="description">Any length number only: [9{*}]</p>
	<p class="description">email: *{+}@*{2,}.*{2,}[.[a{2,}][.[a{2,}]]]</p>

</div>

{{#script}}
	jQuery(function($){

		$('.{{_id}}_masked').change(function(){
			if( $(this).prop('checked') ){
				$('#{{_id}}_maskwrap').show();
				$('#{{_id}}_default').inputmask($('#{{_id}}_mask').val());
			}else{
				$('#{{_id}}_maskwrap').hide();
				$('#{{_id}}_default').inputmask("");
			}
		});	
		$('#{{_id}}_mask').change(function(){
			$('.{{_id}}_masked').trigger('change');
		});
		$('.{{_id}}_masked').trigger('change');
	});
{{/script}}