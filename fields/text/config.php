<div class="caldera-config-group">
	<label for="{{_id}}_placeholder">
		<?php esc_html_e('Placeholder', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<input type="text" id="{{_id}}_placeholder" class="block-input field-config" name="{{_name}}[placeholder]" value="{{placeholder}}">
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_default">
		<?php esc_html_e('Default', 'caldera-forms'); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" id="{{_id}}_default" class="block-input field-config magic-tag-enabled" name="{{_name}}[default]" value="{{default}}">
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}-type_override">
		<?php esc_html_e( 'HTML5 Type', 'caldera-forms'); ?>
	</label>
	<div class="caldera-config-field">
		<select class="field-config {{_id}}_type_override" name="{{_name}}[type_override]" id="{{_id}}-type_override" aria-describedby="{{_id}}-type_override-description">
			<option {{#is type_override value="text"}}selected="selected"{{/is}}value="text">text</option>
			<option {{#is type_override value="date"}}selected="selected"{{/is}}value="date">date</option>
			<option {{#is type_override value="month"}}selected="selected"{{/is}}value="month">month</option>
			<option {{#is type_override value="number"}}selected="selected"{{/is}}value="number">number</option>
			<option {{#is type_override value="search"}}selected="selected"{{/is}}value="search">search</option>
			<option {{#is type_override value="tel"}}selected="selected"{{/is}}value="tel">tel</option>
			<option {{#is type_override value="time"}}selected="selected"{{/is}}value="time">time</option>
			<option {{#is type_override value="url"}}selected="selected"{{/is}}value="url">url</option>
			<option {{#is type_override value="week"}}selected="selected"{{/is}}value="week">week</option>
		</select>
		<p class="description" id="{{_id}}-type_override-description">
			<?php esc_html_e('Change the field type.','caldera-forms');?>
		</p>
	</div>
</div>

<div class="caldera-config-group">
	<label><?php esc_html_e('Masked Input', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<label><input type="checkbox" class="field-config {{_id}}_masked" name="{{_name}}[masked]" value="1" {{#if masked}}checked="checked"{{/if}}> <?php _e('Enable input mask', 'caldera-forms'); ?></label>
	</div>
</div>
<div id="{{_id}}_maskwrap">
	<div class="caldera-config-group">
		<label><?php esc_html_e('Mask', 'caldera-forms'); ?></label>
		<div class="caldera-config-field">		
			<input type="text" id="{{_id}}_mask" class="block-input field-config" name="{{_name}}[mask]" value="{{mask}}">
		</div>
	</div>
	<div class="caldera-config-group">
		<p class="description"><?php esc_html_e('e.g.', 'caldera-forms' ); ?>: aaa-99-999-a9-9*</p>
		<ul>
			<li>9 : <?php esc_html_e('numeric', 'caldera-forms'); ?></li>
			<li>a : <?php esc_html_e('alphabetical', 'caldera-forms'); ?></li>
			<li>* : <?php esc_html_e('alphanumeric', 'caldera-forms'); ?></li>
			<li>[9 | a | *] : <?php esc_html_e('optional', 'caldera-forms'); ?></li>
			<li>{int | * | +} : <?php esc_html_e('length', 'caldera-forms'); ?></li>
		</ul>
		<p class="description"><?php esc_html_e('Any length character only', 'caldera-forms'); ?>: [a{*}]</p>
		<p class="description"><?php esc_html_e('Any length number only', 'caldera-forms'); ?>: [9{*}]</p>
		<p class="description"><?php esc_html_e('email', 'caldera-forms'); ?>: *{+}@*{2,}.*{2,}[.[a{2,}][.[a{2,}]]]</p>

	</div>
</div>

<?php
