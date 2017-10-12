<div class="caldera-config-group">
	<label for="{{_id}}_defaults">
        <?php esc_html_e('Default', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<textarea id="{{_id}}_defaults" class="block-input field-config magic-tag-enabled" name="{{_name}}[default]">{{default}}</textarea>
	</div>

</div>

<div class="caldera-config-group">
	<label for="{{_id}}_language">
		<?php esc_html_e('Language Code', 'caldera-forms'); ?>
	</label>
	<div class="caldera-config-field">
	<input type="text" id="{{_id}}_language" class="block-input field-config magic-tag-enabled" name="{{_name}}[language]" value="{{language}}" maxlength="4"/>
		</div>
</div>



<div class="caldera-config-group">
	<label for="{{_id}}_allowed">
		<?php esc_html_e( 'Sanitization Level', 'caldera-forms' ); ?>
	</label>
	<select id="{{_id}}_allowed" class="field-config" name="{{_name}}[allowed]">
		<option value="post" {{#is allowed value="post"}}selected="selected"{{/is}}>
			<?php esc_html_e( 'Permissive', 'caldera-forms' ); ?>
		</option>
		<option value="data" {{#is allowed value="data"}}selected="selected"{{/is}}>
			<?php esc_html_e( 'Restrictive', 'caldera-forms' ); ?>
		</option>
	</select>
</div>

