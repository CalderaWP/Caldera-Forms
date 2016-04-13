<div class="caldera-config-group">
	<label for="{{_id}}_attach"><?php echo __('Attach to mailer', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_attach" type="checkbox" class="field-config" name="{{_name}}[attach]" value="1" {{#if attach}}checked="checked"{{/if}}>
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_allow_multiple"><?php echo __('Allow Multiple', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_allow_multiple" type="checkbox" class="field-config" name="{{_name}}[multi_upload]" value="1" {{#if multi_upload}}checked="checked"{{/if}}>
	</div>
</div>

<div class="caldera-config-group" id="{{_id}}_allow_multiple_text_wrap">
	<label for="{{_id}}_allow_multiple_text"><?php echo __('Button Text', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_allow_multiple_text" type="text" class="field-config" name="{{_name}}[multi_upload_text]" value="{{#if multi_upload_text}}{{multi_upload_text}}{{/if}}">
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_allowed"><?php echo __('Allowed Types', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_allowed" type="text" class="field-config" name="{{_name}}[allowed]" value="{{allowed}}">
		<p class="description"><?php echo __('Comma separated eg. jpg,pdf,txt', 'caldera-forms'); ?></p>
	</div>
</div>


{{#script}}
	jQuery(function($){

		$('#{{_id}}_allow_multiple').change(function(){

			if( $(this).prop('checked') ){
				$('#{{_id}}_allow_multiple_text_wrap').show();
			}else{
				$('#{{_id}}_allow_multiple_text_wrap').hide();
			}
		});	

		$('#{{_id}}_allow_multiple').trigger('change');
	});
{{/script}}