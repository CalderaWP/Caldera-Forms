<script type="text/html" id="bulk-actions-active-tmpl">
	<option selected="selected" value=""><?php esc_html_e( 'Bulk Actions', 'caldera-forms' ); ?></option>
	<option value="export"><?php esc_html_e( 'Export Selected', 'caldera-forms' ); ?></option>
	<?php if( current_user_can( Caldera_Forms::get_manage_cap( 'manage' ) ) ){ ?><option value="trash"><?php esc_html_e( 'Move to Trash', 'caldera-forms' ); ?></option><?php } ?>
</script>

<script type="text/html" id="cf-export-template">
		<input type="hidden" name="export-form" value="{{formid}}">
		<input type="hidden" name="cal_del" value="{{nonce}}">

		<div class="caldera-config-group">
			<label><?php echo esc_html__( 'Export Type', 'caldera-forms' ); ?></label>
			<div class="caldera-config-field">
				<select class="form-export-type" name="format" style="width: 230px;">
					<option value="json"><?php esc_html_e('Backup / Importable (json)' , 'caldera-forms' ); ?></option>
					<option value="php"><?php esc_html_e('PHP include File' , 'caldera-forms' ); ?></option>
				</select>
			</div>
		</div>
		<p class="description" id="json_export_option"><?php esc_html_e( 'This gives you a .json file that can be imported into Caldera Forms.', 'caldera-forms' ); ?></p>
		<div style="display:none;" id="php_export_options">
			<div class="caldera-config-group">
				<label><?php echo esc_html__( 'Form ID', 'caldera-forms' ); ?></label>
				<div class="caldera-config-field">
					<input type="text" class="block-input field-config" data-format="slug" name="form_id" value="{{formslug}}" required="required">
				</div>
			</div>
			<div class="caldera-config-group">
				<label><?php echo esc_html__( 'Pin to Admin', 'caldera-forms' ); ?></label>
				<div class="caldera-config-field">
					<label><input type="checkbox" name="pin_menu" value="1"> <?php esc_html_e( 'Set form to be pinned to Admin Menu', 'caldera-forms' ); ?></label>
				</div>
			</div>
			<div class="caldera-config-group">
				<label><?php echo esc_html__( 'Field Slugs', 'caldera-forms' ); ?></label>
				<div class="caldera-config-field">
					<label><input type="checkbox" name="convert_slugs" value="1"> <?php esc_html_e( "Convert Field ID's to use field slugs", 'caldera-forms' ); ?></label>
				</div>
			</div>
			<hr>
			<p class="description"><?php esc_html_e('This gives you a hardcoded .php file that can be included in projects. It includes the correct filter for the ID specific form allowing you to easily use the form by simply including the file.', 'caldera-forms'); ?></p>
			<p class="description"><?php esc_html_e('This is not a backup and cannot be imported.', 'caldera-forms'); ?></p>

		</div>

		{{#script}}
		jQuery( function( $ ){
			$(document).on('change', '.form-export-type', function(){
				if( this.value === 'php' ){
					$('#json_export_option').slideUp(100);
					$('#php_export_options').slideDown(100);
				}else{
					$('#php_export_options').slideUp(100);
					$('#json_export_option').slideDown(100);
				}
			})
		});

		{{/script}}
</div>

</script>
<script type="text/html" id="bulk-actions-trash-tmpl">
	<option selected="selected" value=""><?php esc_html_e( 'Bulk Actions', 'caldera-forms' ); ?></option>
	<option value="export"><?php esc_html_e( 'Export Selected', 'caldera-forms' ); ?></option>
	<?php if( current_user_can( 'delete_others_posts' ) ){ ?><option value="active"><?php esc_html_e( 'Restore', 'caldera-forms' ); ?></option>
	<option value="delete"><?php esc_html_e( 'Delete Permanently', 'caldera-forms' ); ?></option><?php } ?>
</script>
<script type="text/html" id="import-form-tmpl">
	<form class="new-form-form" action="admin.php?page=caldera-forms&import=true" enctype="multipart/form-data" method="POST">
		<?php
		wp_nonce_field( 'cf-import', 'cfimporter' );
		do_action('caldera_forms_import_form_template_start');
		?>
		<div class="caldera-config-group">
			<label for=""><?php esc_html_e( 'Form Name', 'caldera-forms' ); ?></label>
			<div class="caldera-config-field">
				<input type="text" class="new-form-name block-input field-config" autocomplete="off" name="name" value="" required="required">
			</div>
		</div>
		<div class="caldera-config-group">
			<label for=""><?php esc_html_e( 'Form File', 'caldera-forms' ); ?></label>
			<div class="caldera-config-field">
				<input type="file" class="new-form-name" name="import_file" required="required" style="width: 230px;">
			</div>
		</div>
		
		<div class="baldrick-modal-footer" style="display: block; clear: both; position: relative; height: 24px; width: 100%; margin: 0px -12px;">

			<button type="submit" class="button" style="float:right;"><?php esc_html_e( 'Import Form', 'caldera-forms' ); ?></button>	

		</div>

		
		<?php
		do_action('caldera_forms_import_form_template_end');
		?>
	</form>
</script>
<script type="text/html" id="front-settings-tmpl">
	<?php

	$style_includes = get_option( '_caldera_forms_styleincludes' );
	if(empty($style_includes)){
		$style_includes = array(
			'alert'	=>	true,
			'form'	=>	true,
			'grid'	=>	true,
		);
		update_option( '_caldera_forms_styleincludes', $style_includes);
	}


	?>
	<div class="caldera-settings-group">
		<div class="caldera-settings">
			<strong><?php esc_html_e( 'Alert Styles' , 'caldera-forms' ); ?></strong>
			<p class="description"><?php esc_html_e( 'Includes Bootstrap 3 styles on the frontend for form alert notices', 'caldera-forms' ); ?></p>
			<div class="clear"></div>
		</div>
		<div class="caldera-setting">
			<div class="switch setting_toggle_alert <?php if(!empty($style_includes['alert'])){ ?>active<?php } ?>">
				<div data-action="save_cf_setting" data-load-element="_parent" data-load-class="load" data-set="alert" data-callback="update_setting_toggle" class="ajax-trigger box-wrapper"></div>
				<div class="box"><span class="spinner"></span></div>
			</div>
		</div>
		<div class="clear"></div>
	</div>	

	<div class="caldera-settings-group">
		<div class="caldera-settings">
			<strong><?php esc_html_e( 'Form Styles' , 'caldera-forms' ); ?></strong>
			<p class="description"><?php esc_html_e( 'Includes Bootstrap 3 styles on the frontend for form fields and buttons', 'caldera-forms' ); ?></p>
			<div class="clear"></div>
		</div>
		<div class="caldera-setting">
			<div class="switch setting_toggle_form <?php if(!empty($style_includes['form'])){ ?>active<?php } ?>">
				<div data-action="save_cf_setting" data-load-element="_parent" data-load-class="load" data-set="form" data-callback="update_setting_toggle" class="ajax-trigger box-wrapper"></div>
				<div class="box"><span class="spinner"></span></div>
			</div>
		</div>
		<div class="clear"></div>
	</div>	

	<div class="caldera-settings-group">
		<div class="caldera-settings">
			<strong><?php esc_html_e( 'Grid Structures' , 'caldera-forms' ); ?></strong>
			<p class="description"><?php esc_html_e( 'Includes Bootstrap 3 styles on the frontend for form grid layouts', 'caldera-forms' ); ?></p>
			<div class="clear"></div>
		</div>
		<div class="caldera-setting">
			<div class="switch setting_toggle_grid <?php if(!empty($style_includes['grid'])){ ?>active<?php } ?>">
				<div data-action="save_cf_setting" data-load-element="_parent" data-load-class="load" data-set="grid" data-callback="update_setting_toggle" class="ajax-trigger box-wrapper"></div>
				<div class="box"><span class="spinner"></span></div>
			</div>

		</div>
		<div class="clear"></div>
	</div>	


</script>
<script type="text/html" id="new-form-tmpl">
		{{#if clone}}
		<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'cf_create_form' ) ); ?>">
		<?php
		do_action('caldera_forms_clone_form_template_start');
		?>
		{{else}}
		<?php
		do_action('caldera_forms_new_form_template_start');
		?>
		{{/if}}
		{{#if clone}}
		<div class="caldera-config-group">
			<label for=""><?php esc_html_e( 'Form Name', 'caldera-forms' ); ?></label>
			<div class="caldera-config-field">
				<input type="text" class="new-form-name block-input field-config" name="name" value="" required="required">
			</div>
		</div>
		<input type="hidden" name="clone" value="{{clone}}">
		<?php
		do_action('caldera_forms_clone_form_template_end');
		?>
		{{else}}
		<?php
		do_action('caldera_forms_new_form_template_end');
		?>
		{{/if}}
		{{#script}}
		jQuery('.new-form-name').focus();
		{{/script}}
</script>
<script type="text/html" id="forms-list-alt-tmpl">

	{{#if entries}}
		<div class="list form-panel postbox" data-form="{{form}}">
			<table class="table table-condensed cf-table-viewer">
				<thead>
					<tr>
						<th style="width:16px;"><input type="checkbox" class="cf-bulkcheck"></th>
						<th><?php esc_html_e('ID', 'caldera-forms' ); ?></th>
						<th><?php esc_html_e('Submitted', 'caldera-forms' ); ?></th>
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
						<td style="text-align: right; width: 100px;white-space: nowrap;">
							<?php do_action('caldera_forms_entry_actions'); ?>
						</td>
					</tr>
				{{/each}}
				{{else}}
					<tr><td colspan="100"><?php esc_html_e('No entries found', 'caldera-forms' ); ?></td></tr>
				{{/if}}
				</tbody>
			</table>
		</div>
	{{else}}
	<p class="description"><?php esc_html_e('No entries yet.', 'caldera-forms' ); ?></p>
	{{/if}}
</script>
<script type="text/html" id="view-entry-tmpl">
{{#if user}}
	<div class="user-avatar user-avatar-{{user/ID}}"{{#if user/name}} title="{{user/name}}"{{/if}} style="margin-top:-1px;">
	{{{user/avatar}}}
	</div>
{{/if}}

	<div id="main-entry-panel" class="tab-detail-panel" data-tab="<?php esc_html_e('Entry', 'caldera-forms' ); ?>">
		<h4><?php echo esc_html_e('Submitted', 'caldera-forms' ); ?> <small class="description">{{date}}</small></h4>
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
			bulkCheck 	= $('.cf-bulkcheck'),
			nonce 		= $('#cf_toolbar_actions'),
			referer		= nonce.parent().find('[name="_wp_http_referer"]');

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
				'form'		:	form,
				'cf_toolbar_actions' : nonce.val(),
				'_wp_http_referer' : referer.val()
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


<?php 
$forms = Caldera_Forms_Forms::get_forms( true );
foreach( $forms as $form_id=>$form_conf ){ ?>

<script type="text/html" id="cfajax_<?php echo esc_attr( $form_id ); ?>-tmpl">
	{{#script}}
		var view = jQuery('.current-view'),
			toggles = jQuery('.status_toggles.button-primary');

		
		if( view.length ){
			view.trigger('click');
			setTimeout( function(){
				toggles.trigger('click');
			}, 500 );
		}else{
			jQuery('#view_entry_baldrickModalCloser,#edit_entry_baldrickModalCloser').trigger('click');	
			toggles.trigger('click');			
		}
	{{/script}}
</script>

<?php } ?>















