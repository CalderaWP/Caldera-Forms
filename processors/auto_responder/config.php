<div class="caldera-config-group">
	<label for="cf-autoresponder-send-name-{{_id}}">
		<?php esc_html_e('From Name', 'caldera-forms'); ?>
	</label>
	<div class="caldera-config-field">
		<input
            id="cf-autoresponder-send-name-{{_id}}"
            type="text" class="block-input field-config magic-tag-enabled required" name="{{_name}}[sender_name]" value="{{sender_name}}"
        />
	</div>
</div>
<div class="caldera-config-group">
	<label for="cf-autoresponder-from-email-{{_id}}">
        <?php esc_html_e('From Email', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input
            id="cf-autoresponder-from-email-{{_id}}"
            type="text" class="block-input field-config magic-tag-enabled caldera-field-bind required" id="{{_id}}_sender_email" name="{{_name}}[sender_email]" value="{{sender_email}}"
        />
	</div>
</div>
<div class="caldera-config-group">
	<label for="cf-autoresponder-subject-{{_id}}">
		<?php esc_html_e('Email Subject', 'caldera-forms'); ?>
	</label>
	<div class="caldera-config-field">
		<input
            id="cf-autoresponder-subject-{{_id}}"
            type="text" class="block-input field-config magic-tag-enabled caldera-field-bind required" id="{{_id}}_subject" name="{{_name}}[subject]" value="{{subject}}"
        />
	</div>
</div>
<div class="caldera-config-group">
	<label for="cf-autoresponder-to-name-{{_id}}">
		<?php esc_html_e('Recipient Name', 'caldera-forms'); ?>
	</label>
	<div class="caldera-config-field">
		<input
            id="cf-autoresponder-to-name-{{_id}}"
            type="text" class="block-input field-config magic-tag-enabled caldera-field-bind required" id="{{_id}}_recipient_name" name="{{_name}}[recipient_name]" value="{{recipient_name}}"
        />
	</div>
</div>
<div class="caldera-config-group">
	<label for="cf-autoresponder-to-email-{{_id}}">
		<?php esc_html_e('Recipient Email', 'caldera-forms'); ?>
	</label>
	<div class="caldera-config-field">
		<input
            id="cf-autoresponder-to-email-{{_id}}"
            type="text" class="block-input field-config magic-tag-enabled caldera-field-bind required" id="{{_id}}_recipient_email" name="{{_name}}[recipient_email]" value="{{recipient_email}}"
        />
	</div>
</div>
<div class="caldera-config-group">
	<label for="cf-autoresponder-message-{{_id}}">
		<?php esc_html_e('Message', 'caldera-forms'); ?>
	</label>
	<div class="caldera-config-field">
		<textarea
            id="cf-autoresponder-message-{{_id}}"
            rows="6" class="block-input field-config required magic-tag-enabled" name="{{_name}}[message]">{{#if message}}{{message}}{{else}}Hi %recipient_name%.
Thanks for your email.
We'll get back to you as soon as possible!
{{/if}}</textarea>
	</div>
</div>
