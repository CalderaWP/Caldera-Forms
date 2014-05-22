<div class="caldera-config-group">
	<label><?php echo __('From Name', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config required" name="{{_name}}[sender_name]" value="{{sender_name}}">
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('From Email', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config required" name="{{_name}}[sender_email]" value="{{sender_email}}">
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('Email Subject', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config required" name="{{_name}}[subject]" value="{{subject}}">
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('Recipient Name', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		{{{_field slug="recipient_name" type="name,text" required="true"}}}
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('Recipient Email', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		{{{_field slug="recipient_email" type="email" required="true"}}}
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('Message', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		<textarea rows="6" class="block-input field-config required" name="{{_name}}[message]">{{#if message}}{{message}}{{else}}Hi %recipient_name%.
Thanks for your email.
We'll get get back to you as soon as possible!
{{/if}}</textarea>
	</div>
</div>