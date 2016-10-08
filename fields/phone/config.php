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
	<label id="{{_id}}_phone-style">
        <?php esc_html_e('Style', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<label>
            <input type="radio" class="field-config {{_id}}_type local" name="{{_name}}[type]" value="local" {{#is type value="local"}}checked="checked"{{/is}}>
            <?php esc_html_e('Local', 'caldera-forms'); ?>
        </label>
		<p class="description">(999) 999 9999</p>

		<label>
            <input type="radio" class="field-config {{_id}}_type international" name="{{_name}}[type]" value="international" {{#is type value="international"}}checked="checked"{{/is}}>
            <?php esc_html_e('International', 'caldera-forms'); ?>
        </label>
		<p class="description">+99 99 999 9999</p>

		<label>
            <input type="radio" class="field-config {{_id}}_type custom" name="{{_name}}[type]" value="custom" {{#is type value="custom"}}checked="checked"{{/is}}>
            <?php esc_html_e('Custom', 'caldera-forms'); ?>
        </label>
		<p class="description">
            <input type="text" id="{{_id}}_custom" class="field-config" name="{{_name}}[custom]" value="{{custom}}">
        </p>
		<p class="description">
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