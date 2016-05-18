<?php

// Just some basics.
$per_page_limit = 20;


// get all forms
$forms = Caldera_Forms_Forms::get_forms( true );
$forms = apply_filters( 'caldera_forms_admin_forms', $forms );

$style_includes = get_option( '_caldera_forms_styleincludes' );
$entry_perpage = get_option( '_caldera_forms_entry_perpage', 20 );
if(empty($style_includes)){
	$style_includes = array(
		'alert'	=>	true,
		'form'	=>	true,
		'grid'	=>	true,
	);
	update_option( '_caldera_forms_styleincludes', $style_includes);
}


// load fields
//$field_types = apply_filters( 'caldera_forms_get_field_types', array() );

// create user modal buttons
$modal_new_form = __('Create Form', 'caldera-forms').'|{"data-action" : "create_form", "data-active-class": "disabled", "data-load-class": "disabled", "data-callback": "new_form_redirect", "data-before" : "serialize_modal_form", "data-modal-autoclose" : "new_form" }';

?><div class="caldera-editor-header">
	<ul class="caldera-editor-header-nav">
		<li class="caldera-editor-logo">
			<span class="dashicons-cf-logo"></span>
			<?php _e('Caldera Forms', 'caldera-forms'); ?>
		</li>
		<li class="caldera-forms-version">
			v<?php echo CFCORE_VER; ?>
		</li>
		<li class="caldera-forms-toolbar-item">
			<a class="button ajax-trigger" data-request="start_new_form" data-modal-buttons='<?php echo $modal_new_form; ?>' data-modal-width="600" data-modal-height="400" data-load-class="none" data-modal="new_form" data-modal-title="<?php echo __('Create New Form', 'caldera-forms'); ?>" data-template="#new-form-tmpl"><?php echo __('New Form', 'caldera-forms'); ?></a>
		</li>
		<li class="caldera-forms-toolbar-item">
			<a class="button ajax-trigger" data-request="start_new_form" data-modal-width="400" data-modal-height="192" data-modal-element="div" data-load-class="none" data-modal="import_form" data-template="#import-form-tmpl" data-modal-title="<?php echo __('Import Form', 'caldera-forms'); ?>" ><?php echo __('Import', 'caldera-forms'); ?></a>
		</li>
		<li class="caldera-forms-toolbar-item">
		&nbsp;&nbsp;
		</li>
		<li class="caldera-forms-headtext">
			<?php echo __('Render forms with:', 'caldera-forms'); ?>
		</li>
		<li class="caldera-forms-toolbar-item">
			<div class="toggle_option_preview">
				<button type="button" title="<?php echo __('Includes Bootstrap 3 styles on the frontend for form alert notices', 'caldera-forms'); ?>" data-action="save_cf_setting" data-active-class="none" data-set="alert" data-callback="update_setting_toggle" class="ajax-trigger setting_toggle_alert button <?php if(!empty($style_includes['alert'])){ ?>button-primary<?php } ?>"><?php echo __('Alert Styles' , 'caldera-forms'); ?></button>
				<button type="button" title="<?php echo __('Includes Bootstrap 3 styles on the frontend for form fields and buttons', 'caldera-forms'); ?>" data-action="save_cf_setting" data-active-class="none" data-set="form" data-callback="update_setting_toggle" class="ajax-trigger setting_toggle_form button <?php if(!empty($style_includes['form'])){ ?>button-primary<?php } ?>"><?php echo __('Form Styles' , 'caldera-forms'); ?></button>
				<button type="button" title="<?php echo __('Includes Bootstrap 3 styles on the frontend for form grid layouts', 'caldera-forms'); ?>" data-action="save_cf_setting" data-active-class="none" data-set="grid" data-callback="update_setting_toggle" class="ajax-trigger setting_toggle_grid button <?php if(!empty($style_includes['grid'])){ ?>button-primary<?php } ?>"><?php echo __('Grid Structures' , 'caldera-forms'); ?></button>
			</div>
		</li>
		<li class="caldera-forms-toolbar-item">
		&nbsp;
		</li>

	</ul>
</div>
<div class="form-admin-page-wrap">
	<div class="form-panel-wrap">
	<?php

	// admin notices


	?>
	<div class="cf-notification" style="display:none;">
		<span class="dashicons dashicons-arrow-down cf-notice-toggle"></span>
		<span class="dashicons dashicons-arrow-up cf-notice-toggle" style="display:none;"></span>
		<div class="cf-notification-notice">
			<span class="dashicons dashicons-warning"></span>
			<span class="cf-notice-info-line"></span>
		</div>
		<div class="cf-notification-count"></div>
		<div class="cf-notification-panel"></div>
	</div>
	<?php if(! empty( $forms ) ){ ?>
		<table class="widefat fixed">
			<thead>
				<tr>
					<th><?php _e('Form', 'caldera-forms'); ?></th>
					<th style="width:5em; text-align:center;"><?php _e('Entries', 'caldera-forms'); ?></th>
				</tr>		
			</thead>
			<tbody>
		<?php

			global $wpdb;

			$class = "alternate";
			foreach($forms as $form_id=>$form){
				if( !empty( $form['hidden'] ) ){
					continue;
				}

				if(!empty($form['db_support'])){
					$total = $wpdb->get_var($wpdb->prepare("SELECT COUNT(`id`) AS `total` FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `form_id` = %s && `status` = 'active';", $form_id));
				}else{
					$total = __('Disabled', 'caldera-forms');
				}
			
				?>

				<tr id="form_row_<?php echo $form_id; ?>" class="<?php echo $class; ?> form_entry_row">						
					<td class="<?php if( !empty( $form['form_draft'] ) ) { echo 'draft-form'; }else{ echo 'active-form'; } ?>">
						<?php echo $form['name']; ?>
						
						<?php if( !empty( $form['debug_mailer'] ) ) { ?>
						<span style="color: rgb(207, 0, 0);" class="description"><?php _e('Mailer Debug enabled.', 'caldera-forms') ;?></span>
						<?php } ?>
												
						<div class="row-actions">
						<?php if( empty( $form['_external_form'] ) ){ ?><span class="edit"><a class="form-control" href="admin.php?page=caldera-forms&edit=<?php echo $form_id; ?>"><?php echo __('Edit'); ?></a> | </span>
						<span class="edit"><a class="form-control ajax-trigger" href="#entres"
						data-load-element="#form_row_<?php echo $form_id; ?>"
						data-action="toggle_form_state"
						data-active-element="#form_row_<?php echo $form_id; ?>"
						data-callback="set_form_state"
						data-form="<?php echo $form_id; ?>"

						><?php if( !empty( $form['form_draft'] ) ) { echo __('Activate', 'caldera-forms'); }else{ echo __('Deactivate', 'caldera-forms'); } ?></a> | </span><?php } ?>

						<?php if(!empty($form['db_support'])){ ?><span class="edit"><a class="form-control form-entry-trigger ajax-trigger" href="#entres"

						data-action="browse_entries"
						data-target="#form-entries-viewer"
						data-form="<?php echo $form_id; ?>"
						data-template="#forms-list-alt-tmpl"
						data-active-element="#form_row_<?php echo $form_id; ?>"
						data-load-class="spinner"
						data-active-class="highlight"
						data-group="entry_nav"
						data-callback="setup_pagination"
						data-status="active"
						data-page="1"

						><?php echo __('Entries', 'caldera-forms'); ?></a> | </span><?php } ?>
						<input type="hidden" id="form-export-<?php echo $form_id; ?>" value='{ "formslug" : "<?php echo sanitize_title( $form['name'] ); ?>", "formid" : "<?php echo $form_id; ?>", "nonce" : "<?php echo wp_create_nonce( 'cf_del_frm' ); ?>" }'>
						<?php if( empty( $form['_external_form'] ) ){ ?><span class="export"><a class="form-control ajax-trigger" 
							<?php 
								// build exporter buttons
								$buttons = array(
									'data-request' => 'cf_build_export',
									'data-modal-autoclose' => 'export',
								);
							?>
							data-modal="export"
							data-modal-height="400"
							data-modal-title="<?php echo esc_attr( __('Export Form', 'caldera-forms') ); ?>" 
							data-request="#form-export-<?php echo $form_id; ?>" 
							data-type="json"
							data-modal-buttons="<?php echo esc_attr( __( 'Export Form', 'caldera-forms' ) ); ?>|<?php echo esc_attr( json_encode( $buttons ) ); ?>"
							data-template="#cf-export-template"
							href="#export"><?php echo __('Export', 'caldera-forms'); ?></a> | </span><?php } ?>
						<span><a class="ajax-trigger" href="#clone" data-request="start_new_form" data-modal-buttons='<?php echo $modal_new_form; ?>' data-clone="<?php echo $form_id; ?>" data-modal-width="600" data-modal-height="400" data-load-class="none" data-modal="new_form" data-modal-title="<?php echo __('Clone Form', 'caldera-forms'); ?>" data-template="#new-form-tmpl"><?php echo __('Clone', 'caldera-forms'); ?></a><?php if( empty( $form['_external_form'] ) ){ ?> | </span>
						<span class="trash form-delete"><a class="form-control" data-confirm="<?php echo __('This will delete this form permanently. Continue?', 'caldera-forms'); ?>" href="admin.php?page=caldera-forms&delete=<?php echo $form_id; ?>&cal_del=<?php echo wp_create_nonce( 'cf_del_frm' ); ?>"><?php echo __('Delete'); ?></a></span><?php } ?>


						</div>
					</td>
					<td style="width:4em; text-align:center;" class="entry_count_<?php echo $form_id; ?>"><?php echo $total; ?></td>
				</tr>


				<?php
				if($class == 'alternate'){
					$class = '';
				}else{
					$class = "alternate";
				}

			}
		?></tbody>
		</table>
		<?php }else{ ?>
		<p>
			<?php esc_html_e( 'You don\'t have any forms.', 'caldera-forms'); ?>
		</p>
		<?php
			// check db and only show the upgrade if db version is not there since its confusing
			$db_version = get_option( 'CF_DB', 0 );
			if( CF_DB > $db_version ) {
			?>
			<div id="cf-upgrade-maybe-fail">
				<p>
					<?php
					echo __( sprintf( 'If you recently updated Caldera Forms and can no longer see saved forms, %s',
						sprintf( '<a href="https://calderawp.com/doc/caldera-forms-form-config-changes/" target="_blank"><strong>%s</strong></a>.', esc_html__( 'no data is lost. Click here for more information', 'caldera-forms' ),  'caldera-forms' ) ) ); ?>
				</p>
				<p>
					<?php printf( '<a href="%s" class="button">%s</a>', esc_url( add_query_arg( array( 'page' => 'caldera-forms', 'cal_db_update' => wp_create_nonce() ) ) ), esc_html__( 'Run The Updater', 'caldera-forms' ) ); ?>
				</p>
			</div>
			<?php } ?>
		<?php } ?>
	</div>
	<div class="form-entries-wrap">
	<?php include CFCORE_PATH . 'ui/entries_toolbar.php'; ?>
	<div id="form-entries-viewer"></div>
	</div>
</div>

<?php
do_action('caldera_forms_admin_templates');
?>
<script type="text/javascript">
function cf_set_limits( el ){
	jQuery( el ).data('perpage', jQuery('#cf-entries-list-items').val() );
}
function set_form_state( obj ){
	if( true === obj.data.success ){

		var row = jQuery('#form_row_' + obj.data.data.ID + '>td');
		row.first().attr('class', obj.data.data.state );
		obj.params.trigger.text( obj.data.data.label );
		
	}
}

function new_form_redirect(obj){
	if(typeof obj.data === 'string'){
		window.location = 'admin.php?page=caldera-forms&edit=' + obj.data;
	}else{
		alert(obj.data.error);
	}
}

function serialize_modal_form(el){
	
	var clicked	= jQuery(el),
		data 	= jQuery('#new_form_baldrickModal'),
		name 	= data.find('.new-form-name');
	
	//verify name is set
	if(name.val().length < 1){
		alert("<?php echo __('A form name is required', 'caldera-forms'); ?>");
		name.focus().addClass('has-error');
		return false;
	}


	clicked.data('data', data.serialize());

	return true;
}


function update_setting_toggle(obj){

	for( var k in obj.data){
		if(obj.data[k] === true){
			jQuery('.setting_toggle_' + k).addClass('button-primary');
		}else{
			jQuery('.setting_toggle_' + k).removeClass('button-primary');
		}
	}
	
	//for()

}

function extend_fail_notice(el){
	jQuery("#extend_cf_baldrickModalBody").html('<div class="alert error"><p><?php echo __('Looks like something is not working. Please try again a little later or post to the <a href="http://wordpress.org/support/plugin/caldera-forms" target="_blank">support forum</a>.', 'caldera-forms'); ?></p></div>');
}

function start_new_form(obj){
	if( obj.trigger.data('clone') ){
		return {clone: obj.trigger.data('clone') };
	}
	return {};
}

var cf_build_export;
jQuery( function( $ ){

	cf_build_export = function( el ){
		var export_object = $('#export_baldrickModal').serialize();
		window.location = "<?php echo esc_attr( admin_url('admin.php?page=caldera-forms' ) ); ?>&" + export_object;
	}

	var notices = $('.error,.notice,.notice-error');

	if( notices.length ){
		$('.cf-notice-info-line').html( notices.first().text() );
		$('.cf-notification-panel').hide();
		$('.cf-notification').fadeIn();
		notices.appendTo( $('.cf-notification-panel') );
		$( '.cf-notice-toggle').click( function(){
			$( '.cf-notice-toggle').toggle();
			$('.cf-notification-panel').slideToggle();
		});
	}
	var ready_limit_change;
	$(document).on('change', '#cf-entries-list-items', function(){
		if( ready_limit_change ){
			clearTimeout( ready_limit_change );
		}
		ready_limit_change = setTimeout( function(){
			$('.status_toggles.button-primary').trigger('click');	
		}, 280 );
		
	});

	$( document ).on('submit', '#new_form_baldrickModal', function(e){
		e.preventDefault();
		var trigger = $(this).find('button.ajax-trigger');
		trigger.trigger('click');
	});

});
</script>
<?php

include CFCORE_PATH . 'ui/entry_navigation.php';

do_action('caldera_forms_admin_footer');
?>
