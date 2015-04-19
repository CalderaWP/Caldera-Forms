<div class="caldera-config-group">
	<label><?php _e('Type'); ?></label>
	<div class="caldera-config-field">
		<select class="block-input field-config field-button-type" name="{{_name}}[type]">
		<option value="submit" {{#is type value="submit"}}selected="selected"{{/is}}><?php _e('Submit'); ?></option>
		<option value="button" {{#is type value="button"}}selected="selected"{{/is}}><?php _e('Button', 'caldera-forms'); ?></option>
		<option value="next" {{#is type value="next"}}selected="selected"{{/is}}><?php _e('Next Page', 'caldera-forms'); ?></option>
		<option value="prev" {{#is type value="prev"}}selected="selected"{{/is}}><?php _e('Previous Page', 'caldera-forms'); ?></option>
		<option value="reset" {{#is type value="reset"}}selected="selected"{{/is}}><?php _e('Reset', 'caldera-forms'); ?></option>
		</select>
	</div>
</div>
<div class="caldera-config-group">
	<label><?php _e('Class', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config" name="{{_name}}[class]" value="{{class}}">
	</div>
</div>