<div class="caldera-config-group">
	<label for="{{_id}}_default">
		<?php esc_html_e('Default', 'caldera-forms'); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" id="{{_id}}_default" class="block-input field-config" name="{{_name}}[default]" value="{{default}}">
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_number">
        <?php esc_html_e('Number of Stars', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input id="{{_id}}_number" type="number" class="field-config" name="{{_name}}[number]" value="{{number}}" style="width:70px;">
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_type">
        <?php esc_html_e('Star Type', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<select id="{{_id}}_type" class="field-config" name="{{_name}}[type]">
			<option value="star" {{#is type value="star"}}selected="selected"{{/is}}><?php esc_html_e('Star', 'caldera-forms'); ?></option>
			<option value="heart" {{#is type value="heart"}}selected="selected"{{/is}}><?php esc_html_e('Heart', 'caldera-forms'); ?></option>
			<option value="face" {{#is type value="face"}}selected="selected"{{/is}}><?php esc_html_e('Face', 'caldera-forms'); ?></option>
			<option value="dot" {{#is type value="dot"}}selected="selected"{{/is}}><?php esc_html_e('Dot', 'caldera-forms'); ?></option>
		</select>
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_size">
        <?php esc_html_e('Star Size', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input id="{{_id}}_size" type="number" class="field-config" name="{{_name}}[size]" value="{{size}}" style="width:70px;">px
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_space">
        <?php esc_html_e('Star Spacing', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input id="{{_id}}_space" type="number" class="field-config" name="{{_name}}[space]" value="{{space}}" style="width:70px;">px
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_single">
        <?php esc_html_e('Single Select', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input id="{{_id}}_single" type="checkbox" class="field-config" name="{{_name}}[single]" value="1" {{#if single}}checked="checked"{{/if}}>
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_color">
        <?php esc_html_e('Star Color', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input type="text" class="color-field field-config" id="{{_id}}_color" name="{{_name}}[color]" value="{{color}}">
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_track_color">
        <?php esc_html_e('Track Color', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input type="text" class="color-field field-config" id="{{_id}}_track_color" name="{{_name}}[track_color]" value="{{track_color}}">
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_cancel">
        <?php esc_html_e('Include Cancel', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input id="{{_id}}_cancel" type="checkbox" class="field-config" name="{{_name}}[cancel]" value="1" {{#if cancel}}checked="checked"{{/if}}>
	</div>
</div>