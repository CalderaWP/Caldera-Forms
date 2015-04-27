<script type="text/html" id="bulk-actions-active-tmpl">
	<option selected="selected" value=""><?php echo __('Bulk Actions'); ?></option>
	<option value="export"><?php echo __('Export Selected'); ?></option>
	<?php if( current_user_can( 'manage_options' ) ){ ?><option value="trash"><?php echo __('Move to Trash'); ?></option><?php } ?>
</script>
<script type="text/html" id="bulk-actions-trash-tmpl">
	<option selected="selected" value=""><?php echo __('Bulk Actions'); ?></option>
	<option value="export"><?php echo __('Export Selected'); ?></option>
	<?php if( current_user_can( 'delete_others_posts' ) ){ ?><option value="active"><?php echo __('Restore'); ?></option>
	<option value="delete"><?php echo __('Delete Permanently'); ?></option><?php } ?>
</script>
<script type="text/html" id="import-form-tmpl">
	<form class="new-form-form" action="admin.php?page=caldera-forms&import=true" enctype="multipart/form-data" method="POST">
		<?php
		wp_nonce_field( 'cf-import', 'cfimporter' );
		do_action('caldera_forms_import_form_template_start');
		?>
		<div class="caldera-config-group">
			<label for=""><?php echo __('Form Name', 'caldera-forms'); ?></label>
			<div class="caldera-config-field">
				<input type="text" class="new-form-name block-input field-config" name="name" value="" required="required">
			</div>
		</div>
		<div class="caldera-config-group">
			<label for=""><?php echo __('Form File', 'caldera-forms'); ?></label>
			<div class="caldera-config-field">
				<input type="file" class="new-form-name" name="import_file" required="required" style="width: 230px;">
			</div>
		</div>
		
		<div class="baldrick-modal-footer" style="display: block; clear: both; position: relative; height: 27px; width: 100%; margin: 0px -12px;">

			<button type="submit" class="button button-primary" style="float:right;"><?php echo __('Import Form', 'caldera-forms'); ?></button>	

		</div>

		
		<?php
		do_action('caldera_forms_import_form_template_end');
		?>
	</form>
</script>
<script type="text/html" id="new-form-tmpl">
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
		{{#if clone}}<input type="hidden" name="clone" value="{{clone}}">{{/if}}
		<?php
		do_action('caldera_forms_new_form_template_end');
		?>
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
						<td>{{#if label}}{{value}}{{else}}{{{this}}}{{/if}}</td>
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
	<div class="user-avatar user-avatar-{{user/ID}}"{{#if user/name}} title="{{user/name}}"{{/if}} style="margin-top:-1px;">
	{{{user/avatar}}}
	</div>
{{/if}}

	<div id="main-entry-panel" class="tab-detail-panel" data-tab="<?php _e('Entry', 'caldera-forms'); ?>">
		<h4><?php echo __('Submitted', 'caldera-forms'); ?> <small class="description">{{date}}</small></h4>
		<hr>
		{{#each data}}
			<div class="entry-line">
				<label>{{label}}</label>
				<div>{{#if view/label}}{{view/value}}{{else}}{{{view}}}{{/if}}&nbsp;</div>
			</div>
		{{/each}}
	</div>


	{{#if meta}}
	{{#each meta}}
	<div id="meta-{{@key}}" data-tab="{{name}}" class="tab-detail-panel">
	<h4>{{name}}</h4>
	<hr>
	{{#unless template}}
		{{#each data}}
			{{#if title}}
				<h4>{{title}}</h4>
			{{/if}}
			{{#each entry}}
				<div class="entry-line">
					<label>{{meta_key}}</label>
					<div>{{{meta_value}}}&nbsp;</div>
				</div>
			{{/each}}
		{{/each}}
	{{/unless}}
	<?php do_action('caldera_forms_entry_meta_templates'); ?>
	</div>
	{{/each}}
	{{/if}}

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


















