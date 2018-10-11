<div class="caldera-config-group">
	<div class="caldera-config-field">
		<label for="{{_id}}_inline">
            <input id="{{_id}}_inline" type="checkbox" class="field-config" name="{{_name}}[inline]" value="1" {{#if inline}}checked="checked"{{/if}}>
            <?php esc_html_e( 'Inline', 'caldera-forms' ); ?>
        </label>
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_agreement">
        <?php esc_html_e( 'Agreement', 'caldera-forms' ); ?>
	</label>
	<div class="caldera-config-field">
        <textarea
				data-id="{{_id}}"
				id="{{_id}}_agreement"
				class="block-input field-config required"
				name="{{_name}}[agreement]"
				aria-describedby="{{_id}}_agreement-description"
				required
		>{{agreement}}</textarea><?php //keep on same line to avoid saving a tab in content ?>
		<p
				class="description"
				id="{{_id}}_agreement-description"
		>
            <?php esc_html_e( 'The terms of the agreement to data collection.', 'caldera-forms' ); ?>
		</p>
	</div>
</div>

<div class="caldera-config-group">
    <label for="{{_id}}_linked_text">
        <?php esc_html_e( 'Linked Text', 'caldera-forms' ); ?>
    </label>
    <div class="caldera-config-field">
        <input
				required
				data-id="{{_id}}"
				id="{{_id}}_linked_text"
				class="block-input field-config required"
				name="{{_name}}[linked_text]"
				aria-describedby="{{_id}}_linked_text-description"
				value="{{linked_text}}"
		/>
		<p
				class="description"
				id="{{_id}}_linked_text-description"
		>
            <?php esc_html_e( 'This text will be linked to Privacy Policy content page.', 'caldera-forms' ); ?>
        </p>
    </div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_title_attr">
        <?php esc_html_e( 'Link\'s title attribute', 'caldera-forms' ); ?>
	</label>
	<div class="caldera-config-field">
		<input
			data-id="{{_id}}"
			id="{{_id}}_title_attr"
			class="block-input field-config"
			name="{{_name}}[title_attr]"
			aria-describedby="{{_id}}_title_attr-description"
			value="{{title_attr}}"
		/>
		<p
			class="description"
			id="{{_id}}_title_attr-description"
		>
		<?php esc_html_e( 'The text to be used as the title attribute of the Privacy Page link (leave empty to not display title attribute).', 'caldera-forms' ); ?>
        </p>
	</div>
</div>
