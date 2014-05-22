<?php

// Just some basics.
$per_page_limit = 20;
// form tempalte
$template_style = 'form-card-tmpl';
// get all forms
$forms = get_option( '_caldera_forms' );

// Modal Height
$modal_height = '400';

// check there are groups
if(!empty($meta_groups['groups'])){
	if( count($meta_groups['groups']) > 7){
		$multiplier = count($meta_groups['groups']) - 7;
		$modal_height = $modal_height + ( 30 * $multiplier );
	}
}
// load fields
//$field_types = apply_filters('caldera_forms_get_field_types', array() );

// create user modal buttons
$modal_new_form = __('Create Form', 'caldera-forms').'|{"data-action" : "create_form", "data-active-class": "disabled", "data-load-class": "disabled", "data-callback": "new_form_redirect", "data-before" : "serialize_modal_form", "data-modal-autoclose" : "new_form" }';

?><div class="caldera-editor-header">
	<ul class="caldera-editor-header-nav">
		<li class="caldera-editor-logo">
			Caldera Forms
		</li>
		<li class="caldera-forms-version">
			v<?php echo CFCORE_VER; ?>
		</li>
		<li class="caldera-forms-search">
			<a class="button ajax-trigger" data-request="start_new_form" data-modal-buttons='<?php echo $modal_new_form; ?>' data-modal-width="600" data-modal-height="300" data-load-class="none" data-modal="new_form" data-modal-title="Create New Form" data-template="#new-form-tmpl"><?php echo __('New Form', 'caldera-forms'); ?></a>
		</li>		
		
	</ul>
</div>
<div class="form-admin-page-wrap">
	<div class="form-panel-wrap">
		<table class="widefat fixed">
			<thead>
				<tr>
					<th>Form</th>
					<th style="width:4em; text-align:center;">Entries</th>
				</tr>		
			</thead>
			<tbody>
		<?php

			if(!empty($forms)){
				global $wpdb;

				$class = "alternate";
				foreach($forms as $form_id=>$form){
					$total = $wpdb->get_var($wpdb->prepare("SELECT COUNT(`id`) AS `total` FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `form_id` = %s;", $form_id));
					
					/*
					?>

					<div class="form-panel postbox">
						<h4><?php echo $form['name']; ?></h4>
						<?php if(!empty($form['description'])){ ?><h5><?php echo $form['description']; ?></h5><?php } ?>

						<ul class="form-controls">
							<li><a class="form-control" href="admin.php?page=caldera-forms&edit=<?php echo $form_id; ?>"><?php echo __('Edit Form', 'caldera-forms'); ?></a></li>
							<li><a class="form-control ajax-trigger" href="#entres"

							data-action="browse_entries"
							data-target="#form-entries-viewer"
							data-form="<?php echo $form_id; ?>"



							><?php echo __('Entries: ' . $total, 'caldera-forms'); ?></a></li>
							<li class="form-delete"><a class="form-control" data-confirm="<?php echo __('This will delete this form permanently. Continue?', 'caldera-forms'); ?>" href="admin.php?page=caldera-forms&delete=<?php echo $form_id; ?>&cal_del=<?php echo wp_create_nonce( 'cf_del_frm' ); ?>"><?php echo __('Delete Form', 'caldera-forms'); ?></a></li>
						</ul>					
					</div>

					<?php
					*/
					?>

					<tr id="form_row_<?php echo $form_id; ?>" class="<?php echo $class; ?> form_entry_row">						
						<td>
							<?php echo $form['name']; ?>
							<div class="row-actions">
							<span class="edit"><a class="form-control" href="admin.php?page=caldera-forms&edit=<?php echo $form_id; ?>"><?php echo __('Edit Form', 'caldera-forms'); ?></a> | </span>
							<span class="edit"><a class="form-control form-entry-trigger ajax-trigger" href="#entres"

							data-action="browse_entries"
							data-target="#form-entries-viewer"
							data-form="<?php echo $form_id; ?>"
							data-template="#forms-list-alt-tmpl"
							data-active-element="#form_row_<?php echo $form_id; ?>"
							data-active-class="highlight"
							data-group="entry_nav"
							data-callback="setup_pagination"
							data-page="1"

							><?php echo __('View Entries', 'caldera-forms'); ?></a> | </span>
							<span class="trash form-delete"><a class="form-control" data-confirm="<?php echo __('This will delete this form permanently. Continue?', 'caldera-forms'); ?>" href="admin.php?page=caldera-forms&delete=<?php echo $form_id; ?>&cal_del=<?php echo wp_create_nonce( 'cf_del_frm' ); ?>"><?php echo __('Delete Form', 'caldera-forms'); ?></a></span>

							</div>
						</td>
						<td style="width:4em; text-align:center;"><?php echo $total; ?></td>
					</tr>


					<?php
					if($class == 'alternate'){
						$class = '';
					}else{
						$class = "alternate";
					}

				}
			}else{

				echo '<p>' . __('You don\'t have any forms.', 'caldera-forms');

			}
		?></tbody>
		</table>
	</div>
	<div class="form-entries-wrap">
		<div class="tablenav caldera-table-nav" style="display:none;">
			<div class="tablenav-pages">
				<span class="displaying-num"></span>
				<span class="pagination-links">
					<a href="#first" title="Go to the first page" data-page="first" class="first-page">«</a>
					<a href="#prev" title="Go to the previous page" data-page="prev" class="prev-page">‹</a>
					<span class="paging-input"><input type="text" size="1" name="paged" title="Current page" class="current-page"> of <span class="total-pages"></span></span>
					<a href="#next" title="Go to the next page" data-page="next" class="next-page">›</a>
					<a href="#last" title="Go to the last page" data-page="last" class="last-page">»</a>
				</span>
			</div>
		</div>
		<div id="form-entries-viewer"></div>	
	</div>
</div>

<script type="text/html" id="new-form-tmpl">
	<form class="new-form-form">
		<div class="caldera-config-group">
			<label for=""><?php echo __('Form Name', 'caldera-forms'); ?></label>
			<div class="caldera-config-field">
				<input type="text" class="new-form-name block-input field-config" name="name" value="">
			</div>
		</div>
		<div class="caldera-config-group">
			<label for=""><?php echo __('Description', 'caldera-forms'); ?></label>
			<div class="caldera-config-field">
				<textarea class="block-input field-config" name="description" value=""></textarea>
			</div>
		</div>
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
					</tr>
				</thead>
				<tbody>
				{{#each entries}}
					<tr>
						<td>{{_entry_id}}</td>
						<td>{{_date}}</td>
						{{#each data}}
						<td>{{this}}</td>
						{{/each}}
					</tr>
				{{/each}}
				</tbody>
			</table>
		</div>
	{{else}}
	<p class="description"><?php echo __('No entries yet.', 'caldera-forms'); ?></p>
	{{/if}}
</script>
<script type="text/html" id="forms-list-tmpl">
	{{#if entries}}
		{{#each entries}}
		<div class="form-panel postbox">
			{{#if suser}}
			<div class="avatar-link">
				<span class="user-avatar-{{user/ID}}">{{{user/avatar}}}</span>
			</div>
			{{/if}}
			<h4># {{entry_id}}</h4>
			<h5>{{date_submitted}}</h5>
			{{#if data}}
			<table class="table table-condensed">
				<tbody>
				{{#each data}}
					<tr>
						<th>{{@key}}</th>
						<td>{{this}}</td>
					</tr>
				{{/each}}
				</tbody>
			</table>

			{{/if}}
		</div>
		{{/each}}
	{{else}}
	<p class="description"><?php echo __('No entries yet.', 'caldera-forms'); ?></p>
	{{/if}}
</script>
<script type="text/javascript">

function new_form_redirect(obj){
	if(typeof obj.data === 'string'){
		window.location = 'admin.php?page=caldera-forms&edit=' + obj.data;
	}else{
		alert(obj.data.error);
	}
}

// profile form saver
function serialize_modal_form(el){
	
	var clicked	= jQuery(el),
		data 	= clicked.closest('.caldera-modal-wrap').find('.new-form-form'),
		name = data.find('.new-form-name');
	
	//verify name is set
	if(name.val().length < 1){
		alert("<?php echo __('An form name is required', 'caldera-forms'); ?>");
		name.focus().addClass('has-error');
		return false;
	}


	clicked.data('data', data.serialize());

	return true;
}

function setup_pagination(obj){

	var total			= obj.rawData.total,
		tense			= ( total === 1 ? ' <?php echo __('entry', 'caldera-pages'); ?>' : ' <?php echo __('entries', 'caldera-pages'); ?>' ),
		pages			= obj.rawData.pages,
		current			= obj.rawData.current_page,
		pagenav			= jQuery('.caldera-table-nav'),
		page_links		= pagenav.find('.pagination-links'),
		entries_total	= pagenav.find('.displaying-num'),
		pages_total		= pagenav.find('.total-pages'),
		current_display	= pagenav.find('.current-page'),
		first_page		= pagenav.find('.first-page'),
		prev_page		= pagenav.find('.prev-page'),
		next_page		= pagenav.find('.next-page'),
		last_page		= pagenav.find('.last-page');

	obj.params.trigger.data('page', current);

	pagenav.data('total', pages);

	if(total < 1){
		pagenav.hide();
		return;	
	}else if(pages <= 1){
		page_links.hide();
	}else{
		page_links.show();		
	}
	pagenav.show();
	page_links.find('a').removeClass('disabled');

	// setup values
	page_links.data('total', total);
	entries_total.html(total + tense);
	pages_total.html(pages);
	current_display.val(current);

	if(current === 1){
		first_page.addClass('disabled');
		prev_page.addClass('disabled');
	}else if(current === pages){
		last_page.addClass('disabled');
		next_page.addClass('disabled');		
	}


}

function start_new_form(){

	
	return {};

}
jQuery(function($){
	
	function do_page_navigate(el){
	
		var clicked 		= $(el);

		if(clicked.hasClass('disabled')){
			return;
		}

		var	form_trigger	= $('.form_entry_row.highlight').find('.form-entry-trigger'),
			current			= parseInt(form_trigger.data('page')),
			pagenav			= jQuery('.caldera-table-nav'),
			page_links		= pagenav.find('.pagination-links'),
			total			= parseInt(pagenav.data('total'));

		

		if(clicked.data('page') === 'first'){
			form_trigger.data('page', 1).trigger('click');
		}else if(clicked.data('page') === 'prev'){
			var next = current - 1;
			form_trigger.data('page', next).trigger('click');
		}else if(clicked.data('page') === 'next'){
			var next = current + 1;
			form_trigger.data('page', next).trigger('click');
		}else if(clicked.data('page') === 'last'){
			form_trigger.data('page', total).trigger('click');
		}else{
			form_trigger.data('page', clicked.val()).trigger('click');
		}
	}

	$('body').on('change','.current-page', function(e){
		do_page_navigate(this);
	});
	$('body').on('click','.pagination-links a', function(e){
		e.preventDefault();
		do_page_navigate(this);
	});

});
</script>