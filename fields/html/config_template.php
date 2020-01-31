<div class="caldera-config-group">
	<label for="{{_id}}editor">
        <?php esc_html_e('Content', 'caldera-forms'); ?>
    </label>
</div>

<div style="position:relative;">
	<textarea class="block-input field-config magic-tag-enabled" name="{{_name}}[default]" id="{{_id}}editor" style="resize:vertical; height:200px;">{{default}}</textarea>
</div>

<div style="clear:both;"></div>

<div class="caldera-config-group">
        <label for="{{_id}}_show_in_summary">
			<?php echo esc_html__('Email Summary?', 'caldera-forms' ); ?>
        </label>
        <div class="caldera-config-field">
            <input
                    id="{{_id}}_show_in_summary"
                    type="checkbox"
                    class="field-config"
                    name="{{_name}}[show_in_summary]"
                    value="1"
                    aria-describedby="{{_id}}_show_in_summary-desc"
                    {{#if show_in_summary}}checked="checked" {{/if}}
            />
            <p
                    id="{{_id}}_show_in_summary-desc"
            >
				<?php echo esc_html__('HTML field content is not shown in emails. Check here to add this content to the {summary} magic tag.', 'caldera-forms' ); ?>
            </p>
    </div>
</div>