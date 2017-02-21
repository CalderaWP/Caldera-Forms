<div class="caldera-config-group">
	<label for="{{_id}}_placeholder">
        <?php esc_html_e('Placeholder', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input type="text" id="{{_id}}_placeholder" class="block-input field-config" name="{{_name}}[placeholder]" value="{{placeholder}}">
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_default">
        <?php esc_html_e('Default', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input type="text" id="{{_id}}_default" class="block-input field-config" name="{{_name}}[default]" value="{{default}}">
	</div>
</div>
<div class="caldera-config-group">
	<fieldset>
		<legend>
			<?php esc_html_e('Style', 'caldera-forms'); ?>
		</legend>
	</fieldset>

	<div class="caldera-config-field">
		<label for="{{_id}}_phone_type_local">
            <?php esc_html_e('Local', 'caldera-forms'); ?>
        </label>
		<input id="{{_id}}_phone_type_local" aria-describedby="{{_id}}_phone_type_local_desc" type="radio" class="field-config {{_id}}_type local" name="{{_name}}[type]" value="local" {{#is type value="local"}}checked="checked"{{/is}}>

		<p class="description" id="{{_id}}_phone_type_local_desc">(999) 999 9999</p>

	</div>
	<div class="caldera-config-field">
		<label for="{{_id}}_phone_type_international">
            <?php esc_html_e('International', 'caldera-forms'); ?>
        </label>
		<input type="radio" id="{{_id}}_phone_type_international" aria-describedby="{{_id}}_phone_type_international_desc" class="field-config {{_id}}_type international" name="{{_name}}[type]" value="international" {{#is type value="international"}}checked="checked"{{/is}}>

		<p class="description" id="{{_id}}_phone_type_international_desc">+99 99 999 9999</p>
	</div>
	<div class="caldera-config-field">
		<label for="{{_id}}_phone_type_custom">
            <input type="radio" id="{{_id}}_phone_type_custom" aria-describedby="{{_id}}_phone_type_custom_desc" class="field-config {{_id}}_type custom" name="{{_name}}[type]" value="custom" {{#is type value="custom"}}checked="checked"{{/is}}>
            <?php esc_html_e('Custom', 'caldera-forms'); ?>
        </label>
		<p class="description">
			<label for="{{_id}}_custom" class="screen-reader-text">
				<?php esc_html_e( 'Custom Phone Number Format', 'caldera-forms' ); ?>
			</label>
            <input type="text" id="{{_id}}_custom" class="field-config" name="{{_name}}[custom]" value="{{custom}}">
        </p>
		<p class="description" id="{{_id}}_phone_type_custom_desc">
            <?php esc_html_e('Use the digit 9 to indicate a number', 'caldera-forms'); ?>
        </p>
	</div>
</div>

{{#script}}
	jQuery(function($){

		$('.{{_id}}_type').change(function(){

			if(this.value === 'local'){
				$('#{{_id}}_default').inputmask("(999) 999 9999");
			}else if(this.value === 'international'){
				$('#{{_id}}_default').inputmask("+99 99 999 9999");
			}else if(this.value === 'custom'){
				$('#{{_id}}_default').inputmask( $('#{{_id}}_custom').val() );
			}
		});
		$('#{{_id}}_custom').change(function(){
			$('.{{_id}}_type.{{type}}').trigger('change');
		});
		$('.{{_id}}_type.{{type}}').trigger('change');
	});
{{/script}}