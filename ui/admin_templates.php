<script type="text/html" id="bulk-actions-active-tmpl">
	<option selected="selected" value=""><?php echo __('Bulk Actions', 'caldera-forms'); ?></option>
	<option value="export"><?php echo __('Export Selected', 'caldera-forms'); ?></option>
	<option value="trash"><?php echo __('Move to Trash', 'caldera-forms'); ?></option>
</script>
<script type="text/html" id="bulk-actions-trash-tmpl">
	<option selected="selected" value=""><?php echo __('Bulk Actions', 'caldera-forms'); ?></option>
	<option value="export"><?php echo __('Export Selected', 'caldera-forms'); ?></option>
	<option value="active"><?php echo __('Restore', 'caldera-forms'); ?></option>
	<option value="delete"><?php echo __('Delete Permanently', 'caldera-forms'); ?></option>
</script>
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
		<div class="list form-panel postbox" data-form="{{form}}">
			<table class="table table-condensed cf-table-viewer">
				<thead>
					<tr>
						<th style="width:16px;"><input type="checkbox" class="cf-bulkcheck"></th>
						<th><?php echo __('ID', 'caldera-forms'); ?></th>
						<th><?php echo __('Submitted', 'caldera-forms'); ?></th>
						{{#each fields}}
						<th>{{this}}</th>
						{{/each}}
						<th style="width: 100px;"></th>
					</tr>
				</thead>
				<tbody>
				{{#if entries}}
				{{#each entries}}
					<tr id="entry_row_{{_entry_id}}">
						<td style="width:16px;"><input type="checkbox" class="cf-entrycheck" value="{{_entry_id}}"></td>
						<td>{{_entry_id}}</td>
						<td>{{_date}}</td>
						{{#each data}}
						<td>{{{this}}}</td>
						{{/each}}
						<td style="text-align: right; width: 100px;"><?php do_action('caldera_forms_entry_actions'); ?></td>
					</tr>
				{{/each}}
				{{else}}
					<tr><td colspan="100"><?php echo __('No entries found', 'caldera-forms'); ?></td></tr>
				{{/if}}
				</tbody>
			</table>
		</div>
	{{else}}
	<p class="description"><?php echo __('No entries yet.', 'caldera-forms'); ?></p>
	{{/if}}
</script>
<script type="text/html" id="view-entry-tmpl">
{{#if user}}
<div class="modal-side-bar has-avatar">
	<span class="user-avatar user-avatar-{{user/ID}}"{{#if user/name}} title="{{user/name}}"{{/if}}>
	{{{user/avatar}}}
	</span>
	{{#if meta}}
	<ul class="modal-side-tabs">
		<li><a href="#main-entry-panel" class="modal-side-tab active"><?php echo __('Entry', 'caldera-forms'); ?></a></li>
		{{#each meta}}
			<li><a href="#meta-{{@key}}" class="modal-side-tab">{{name}}</a></li>
		{{/each}}
	</ul>
	{{/if}}
</div>
{{/if}}
<div class="form-panel{{#if user}} modal-inside{{/if}}">
<div id="main-entry-panel" class="tab-detail-panel">
		<h4><?php echo __('Submitted', 'caldera-forms'); ?> <small class="description">{{date}}</small></h4>
		<hr>
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
				<td>{{{view}}}</td>
			</tr>
		{{/each}}
		</tbody>
	</table></div>

	{{#if meta}}
	{{#each meta}}
	<div id="meta-{{@key}}" class="tab-detail-panel" style="display:none;">
	<h4>{{name}}</h4>
	<hr>
	{{#unless template}}
		<table class="table table-condensed">		
				{{#each data}}
				<thead>
				{{#if title}}
				<tr>
					<th colspan="2" class="active">{{title}}</th>
				</tr>
				{{/if}}
				<tr>
					<th><?php echo __('Field', 'caldera-forms'); ?></th>
					<th><?php echo __('Value', 'caldera-forms'); ?></th>
				</tr>
				</thead>
				<tbody>
				{{#each entry}}		
				<tr>
					<th>{{meta_key}}</th>
					<td>{{{meta_value}}}</td>
				</tr>
				{{/each}}
				</tbody>
				{{/each}}		
		</table>
	{{/unless}}
	<?php do_action('caldera_forms_entry_meta_templates'); ?>
	</div>
	{{/each}}
	{{/if}}
</div>
</script>
<script type="text/javascript">

jQuery(function($){

	$('body').on('change', '.cf-bulkcheck', function(){

		var checked = $(this),
			parent = checked.closest('.cf-table-viewer'),
			checks = parent.find('.cf-entrycheck'),
			action = $('#cf_bulk_action');

		checks.prop('checked', checked.prop('checked'));

	});
	
	$('body').on('change', '.cf-entrycheck', function(){

		var checkall	= $('.cf-bulkcheck'),
			allchecks	= $('.cf-entrycheck'),
			checked 	= $('.cf-entrycheck:checked');

		if(allchecks.length != checked.length){
			checkall.prop('checked', false);
		}else{
			checkall.prop('checked', true);
		}

	});
	$('body').on('click', '.cf-bulk-action', function(){

		var action 		= $('#cf_bulk_action'),
			bulkCheck 	= $('.cf-bulkcheck');

		if( !action.val().length){
			return;
		}

		action.prop('disabled', true);
		var checks 	= $('#form-entries-viewer .cf-entrycheck:checked'),
			form	= $('#form-entries-viewer .list.form-panel').data('form');

		if(checks.length){

			var list = [],
				rows = [];
			for( var i = 0; i<checks.length; i++){
				list.push(checks[i].value);
				rows.push('#entry_row_' + checks[i].value);
			}
			var row_items = $(rows.join(','));

			var data = {
				'action'	:	'cf_bulk_action',
				'do'		:	action.val(),
				'items'		:	list,
				'form'		:	form
			}

			row_items.animate({"opacity": .4}, 500);
			$.post(ajaxurl, data, function(res){
				if(res.status && res.entries && res.total){
					row_items.remove();
					$('.entry_count_' + form).html(res.total);
					$('input.current-page').trigger('change');
				}else if(res.url){
					row_items.animate({"opacity": 1}, 500);
					window.location = res.url;
				}else if(res.status === 'reload'){
					$('input.current-page').trigger('change');
				}
				action.val('').prop('disabled', false);
				bulkCheck.prop('checked', false);				
			});
		}else{
			action.prop('disabled', false);
		}

	});

});

</script>


















