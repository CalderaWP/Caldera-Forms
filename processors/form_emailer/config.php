<div class="caldera-config-group">
	<label><?php echo __('Recipient Email', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		<input type="email" class="block-input field-config required" name="{{_name}}[recipient]" value="{{recipient}}">
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('Email Subject', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config required" name="{{_name}}[subject]" value="{{subject}}">
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('Sender Name', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		{{{_field slug="sender" type="name,text" required="true"}}}
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('Sender Email', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		{{{_field slug="sender_email" type="email" required="true"}}}
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('Message', 'caldera-forms'); ?> </label>
	<div class="caldera-config-field">
		{{{_field slug="message" type="paragraph" required="true"}}}
	</div>
</div>