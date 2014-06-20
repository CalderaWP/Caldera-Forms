<script type="text/html" id="import-form-tmpl">
	<form class="new-form-form" action="admin.php?page=caldera-forms&import=true" enctype="multipart/form-data" method="POST">
		<?php
		wp_nonce_field( 'cf-import', 'cfimporter' );
		do_action('caldera_forms_import_form_template_start');
		?>
		<p class="description"><?php echo __('Import a Caldera Form from a .json export file.', 'caldera-forms'); ?></p>
		<input type="file" class="new-form-name" name="import_file" required="required">		
		<p class="import-warning" style="color:#ff0000;"><?php echo __('This will overwrite a form if it already exists.', 'caldera-forms'); ?></p>

		<hr>
		<button type="submit" class="button button-primary" style="float:right;"><?php echo __('Import Form', 'caldera-forms'); ?></button>
		<?php
		do_action('caldera_forms_import_form_template_end');
		?>
	</form>
</script>
<script type="text/html" id="new-form-tmpl">
	<form class="new-form-form ajax-trigger" data-action="create_form" data-active-class="disabled" data-load-class="disabled" data-callback="new_form_redirect" data-before="serialize_modal_form" data-modal-autoclose="new_form">
		<?php
		do_action('caldera_forms_new_form_template_start');
		?>
		<div class="caldera-config-group">
			<label for=""><?php echo __('Form Name', 'caldera-forms'); ?></label>
			<div class="caldera-config-field">
				<input type="text" class="new-form-name block-input field-config" name="name" value="" required="required">
			</div>
		</div>
		<div class="caldera-config-group">
			<label for=""><?php echo __('Description', 'caldera-forms'); ?></label>
			<div class="caldera-config-field">
				<textarea class="block-input field-config" name="description" value=""></textarea>
			</div>
		</div>
		<?php
		do_action('caldera_forms_new_form_template_end');
		?>
	</form>
</script>
<script type="text/html" id="forms-list-alt-tmpl">

	{{#if entries}}
		<div class="list form-panel postbox">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th><?php echo __('ID', 'caldera-forms'); ?></th>
						<th><?php echo __('Submitted', 'caldera-forms'); ?></th>
						{{#each fields}}
						<th>{{this}}</th>
						{{/each}}
						<th></th>
					</tr>
				</thead>
				<tbody>
				{{#each entries}}
					<tr>
						<td>{{_entry_id}}</td>
						<td>{{_date}}</td>
						{{#each data}}
						<td>{{{this}}}</td>
						{{/each}}
						<td style="text-align: right;"><?php do_action('caldera_forms_entry_actions'); ?></td>
					</tr>
				{{/each}}
				</tbody>
			</table>
		</div>
	{{else}}
	<p class="description"><?php echo __('No entries yet.', 'caldera-forms'); ?></p>
	{{/if}}
</script>
<script type="text/html" id="view-entry-tmpl">
<div class="form-panel">
	<table class="table table-condensed">
		<thead>
			<tr>
				<th><?php echo __('Field', 'caldera-forms'); ?></th>
				<th><?php echo __('Value', 'caldera-forms'); ?></th>
			</tr>
		</thead>
		<tbody>
		{{#each data}}
			<tr>
				<th>{{label}}</th>
				<td>{{{value}}}</td>
			</tr>
		{{/each}}
		</tbody>
	</table>
</div>
</script>