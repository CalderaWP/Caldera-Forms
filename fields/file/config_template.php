<div class="caldera-config-group">
	<label role="presentation"></label>
	<div class="caldera-config-field">
		<label for="{{_id}}_attach">
			<input id="{{_id}}_attach" type="checkbox" class="field-config" name="{{_name}}[attach]" value="1" {{#if attach}}checked="checked"{{/if}}>
			<?php esc_html_e('Attach to mailer', 'caldera-forms'); ?>
		</label>
	</div>
</div>

<div class="caldera-config-group">
	<label role="presentation"></label>
	<div class="caldera-config-field">
		<label for="{{_id}}_allow_multiple">
			<input id="{{_id}}_allow_multiple" type="checkbox" class="field-config" name="{{_name}}[multi_upload]" value="1" {{#if multi_upload}}checked="checked"{{/if}}>
			<?php esc_html_e('Allow Multiple', 'caldera-forms'); ?>
		</label>
	</div>
</div>

<div class="caldera-config-group" id="{{_id}}_allow_multiple_text_wrap">
	<label role="presentation"></label>
	<div class="caldera-config-field">
		<label for="{{_id}}_allow_multiple_text">
			<input id="{{_id}}_allow_multiple_text" type="text" class="field-config" name="{{_name}}[multi_upload_text]" value="{{#if multi_upload_text}}{{multi_upload_text}}{{/if}}">
			<?php esc_html_e('Button Text', 'caldera-forms'); ?>
		</label>

	</div>
</div>

<div class="caldera-config-group">
	<label role="presentation"></label>
	<div class="caldera-config-field">
		<label for="{{_id}}_media_library">
			<input id="{{_id}}_media_library" type="checkbox" class="field-config" name="{{_name}}[media_lib]" value="1" {{#if media_lib}}checked="checked"{{/if}}>
			<?php echo esc_html__('Add to Media Library', 'caldera-forms'); ?>
		</label>
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_allowed">
		<?php esc_html_e('Allowed Types', 'caldera-forms'); ?>
	</label>
	<div class="caldera-config-field">
		<input id="{{_id}}_allowed" type="text" class="field-config" name="{{_name}}[allowed]" value="{{allowed}}">
		<p class="description">
			<?php esc_html_e('Comma separated eg. jpg,pdf,txt', 'caldera-forms'); ?>
		</p>
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