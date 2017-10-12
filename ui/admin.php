<?php

// Just some basics.
$per_page_limit = 20;

Caldera_Forms::check_tables();
// get all forms
$orderby = isset( $_GET[ Caldera_Forms_Admin::ORDERBY_KEY ] ) && 'name' == $_GET[ Caldera_Forms_Admin::ORDERBY_KEY ] ? 'name' : false;
$forms = Caldera_Forms_Forms::get_forms( true, false, $orderby );
$forms = apply_filters( 'caldera_forms_admin_forms', $forms );


$style_includes = get_option( '_caldera_forms_styleincludes' );
if(empty($style_includes)){
	$style_includes = array(
		'alert'	=>	true,
		'form'	=>	true,
		'grid'	=>	true,
	);
	update_option( '_caldera_forms_styleincludes', $style_includes);
}



// create user modal buttons
$modal_new_form = esc_html__('Create Form', 'caldera-forms').'|{"data-action" : "create_form", "data-active-class": "disabled", "data-load-class": "disabled", "data-callback": "new_form_redirect", "data-before" : "serialize_modal_form", "data-modal-autoclose" : "new_form" }|right';

?><div class="caldera-editor-header">
	<ul class="caldera-editor-header-nav">
		<li class="caldera-editor-logo">
			<span class="caldera-forms-name">Caldera Forms</span>
		</li>
		<li class="caldera-forms-version">
			<?php echo CFCORE_VER; ?>
		</li>
		<li class="caldera-forms-toolbar-item">
			<a class="button button-primary ajax-trigger cf-new-form-button" data-request="start_new_form" data-modal-no-buttons='<?php echo $modal_new_form; ?>' data-modal-width="70%" data-modal-height="80%" data-load-class="none" data-modal="new_form" data-nonce="<?php echo wp_create_nonce( 'cf_create_form' ); ?>" data-modal-title="<?php echo __('Create New Form', 'caldera-forms'); ?>" data-template="#new-form-tmpl">
		    	<?php echo __('New Form', 'caldera-forms'); ?>
			</a>
		</li>
		<li class="caldera-forms-toolbar-item">
			<a class="button ajax-trigger" data-request="start_new_form" data-modal-width="400" data-modal-height="192" data-modal-element="div" data-load-class="none" data-modal="import_form" data-template="#import-form-tmpl" data-modal-title="<?php echo __('Import Form', 'caldera-forms'); ?>" ><?php echo __('Import', 'caldera-forms'); ?></a>
		</li>
		<li class="caldera-forms-toolbar-item separator">&nbsp;&nbsp;</li>
		<li class="caldera-forms-toolbar-item" id="cf-email-settings-item">
			<?php
				printf( '<button class="button" id="cf-email-settings" title="%s">%s</button>',
					esc_attr__( 'Click to modify Caldera Forms email settings', 'caldera-forms'  ),
					esc_html__( 'Email Settings', 'caldera-forms' )
				);
			?>
		</li>
		<li class="caldera-forms-toolbar-item separator">&nbsp;&nbsp;</li>
		<li class="caldera-forms-toolbar-item">
			<a class="button ajax-trigger cf-general-settings" data-request="toggle_front_end_settings" data-modal-width="400" data-modal-height="405" data-modal-element="div" data-load-class="none" data-modal="front_settings" data-template="#front-settings-tmpl" data-callback="toggle_front_end_settings" data-modal-title="<?php esc_attr_e('General Settings', 'caldera-forms'); ?>" title="<?php  esc_attr_e( 'General Settings', 'caldera-forms' ); ?>" >
			<?php
				printf( '<span title="%s">%s</span>',
					esc_attr__( 'Click to modify Caldera Forms general settings', 'caldera-forms'  ),
					esc_html__( 'General Settings', 'caldera-forms' )
				);
			?>
			</a>
		</li>
		<li class="caldera-forms-toolbar-item separator">&nbsp;&nbsp;</li>
		<li class="caldera-forms-toolbar-item" id="cf-form-order-item">
			<?php
			if( 'name' === $orderby ){
				$text = __( 'Order Forms By ID', 'caldera-forms' );
				$url = Caldera_Forms_Admin::main_admin_page_url();
			}else {
				$text = __( 'Order Forms By Name', 'caldera-forms' );
				$url = Caldera_Forms_Admin::main_admin_page_url( 'name' );
			}
			printf( '<a  class="button" id="cf-form-order" title="%s" href="%s">%s</a>',
				esc_attr__( 'Click to change order of the forms', 'caldera-forms'  ),
				esc_url( $url ),
				esc_html__( $text )
			);
			?>
		</li>
		<?php if ( isset( $_GET['message_resent' ] ) ){?>
		<li class="caldera-forms-toolbar-item separator" >&nbsp;&nbsp;</li>
			<li class="caldera-forms-toolbar-item success">
				<?php esc_html_e( 'Message Resent', 'caldera-forms' ); ?>
			</li>
		<?php } ?>

	</ul>
</div>

<div class="form-admin-page-wrap">
	<div id="caldera-forms-admin-page-left">
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

				<tr id="<?php  echo esc_attr( trim( 'form_row_' . $form_id ) ); ?>" class="<?php echo $class; ?> form_entry_row">
					<td class="<?php if( !empty( $form['form_draft'] ) ) { echo 'draft-form'; }else{ echo 'active-form'; } ?>">
						<span class="cf-form-name-preview"><?php esc_html_e( $form[ 'name' ] ); ?></span> <input readonly type="text" class="cf-shortcode-preview" value="<?php echo esc_attr( '[caldera_form id="' . trim( $form[ 'ID' ] ) . '"]'); ?>"> <span class="cf-form-shortcode-preview"><?php echo esc_html__( 'Get Shortcode', 'caldera-forms' ); ?></span>

						<?php if( !empty( $form['debug_mailer'] ) ) { ?>
						<span style="color: rgb(207, 0, 0);" class="description"><?php _e('Mailer Debug enabled.', 'caldera-forms') ;?></span>
						<?php } ?>

						<div class="row-actions">
						<?php if( empty( $form['_external_form'] ) ){ ?><span class="edit"><a class="form-control" href="<?php echo esc_url( Caldera_Forms_Admin::form_edit_link( $form_id ) ); ?>"><?php echo __('Edit'); ?></a> | </span>
						<span class="edit"><a class="form-control ajax-trigger" href="#entres"
						data-load-element="<?php echo esc_attr( '#form_row_' . trim( $form_id ) ); ?>"
						data-action="toggle_form_state"
                        data-nonce="<?php echo esc_attr( wp_create_nonce( 'toggle_form_state') ); ?>"
						data-active-element="<?php echo esc_attr( '#form_row_' . trim( $form_id ) ); ?>"
						data-callback="set_form_state"
						data-form="<?php echo esc_attr( trim( $form_id ) ); ?>"

						><?php if( !empty( $form['form_draft'] ) ) { echo __('Enable', 'caldera-forms'); }else{ echo __('Disable', 'caldera-forms'); } ?></a> | </span><?php } ?>

						<?php if(!empty($form['db_support'])) { ?>
							<span class="edit">
								<?php if (  ! version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
									echo '<span title="' . esc_attr( sprintf( __( 'Entry viewer is disabled due to your PHP version, see %s', 'caldera-forms' ), 'https://calderaforms.com/php' ) ) .'" data-toggle="tooltip" data-placement="bottom" class="disabled">' . esc_html__( 'Entries', 'caldera-forms' ) . '</span> |';
								} else { ?>
									<a class="form-control form-entry-trigger ajax-trigger cf-entry-viewer-link"
									   href="#entres"
									   data-nonce="<?php echo wp_create_nonce( 'view_entries' ); ?>"
									   data-action="browse_entries"
									   data-target="#form-entries-viewer"
									   data-form="<?php echo esc_attr( trim( $form_id ) ); ?>"
									   data-template="#forms-list-alt-tmpl"
									   data-active-element="<?php echo esc_attr( '#form_row_' . trim( $form_id ) ); ?>"
									   data-load-class="spinner"
									   data-active-class="highlight"
									   data-group="entry_nav"
									   data-callback="setup_pagination"
									   data-status="active"
									   data-page="1"
									><?php esc_html_e( 'Entries', 'caldera-forms' ); ?></a> | </span><?php }
						}?>
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
							data-request="<?php echo esc_attr( '#form-export-' . trim( $form_id ) ); ?>"
							data-type="json"
							data-modal-buttons="<?php echo esc_attr( __( 'Export Form', 'caldera-forms' ) ); ?>|<?php echo esc_attr( json_encode( $buttons ) ); ?>"
							data-template="#cf-export-template"
							href="#export"><?php esc_html_e('Export', 'caldera-forms'); ?></a> | </span><?php } ?>
						<span><a class="ajax-trigger" href="#clone" data-request="start_new_form" data-modal-buttons='<?php echo $modal_new_form; ?>' data-clone="<?php echo $form_id; ?>" data-modal-width="600" data-modal-height="160" data-load-class="none" data-modal="new_clone" data-nonce="<?php echo esc_attr( wp_create_nonce( 'cf_create_form' ) ); ?>" data-modal-title="<?php echo __('Clone Form', 'caldera-forms'); ?>" data-template="#new-form-tmpl"><?php echo __('Clone', 'caldera-forms'); ?></a><?php if( empty( $form['_external_form'] ) ){ ?> | </span>
						<span class="trash form-delete"><a class="form-control" data-confirm="<?php echo __('This will delete this form permanently. Continue?', 'caldera-forms'); ?>" href="admin.php?page=caldera-forms&delete=<?php echo trim( $form_id ); ?>&cal_del=<?php echo wp_create_nonce( 'cf_del_frm' ); ?>"><?php echo __('Delete'); ?></a></span><?php } ?>


						</div>
					</td>
					<td style="width:4em; text-align:center;" class="<?php echo esc_attr( 'entry_count_' . trim( $form_id ) ); ?><?php echo $form_id; ?>"><?php echo $total; ?></td>
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
		<div id="cf-you-have-no-forms">
			<p style="margin: 24px;">
				<?php esc_html_e( 'You don\'t have any forms yet.', 'caldera-forms'); ?>
			</p>

		</div>
		<div class="caldera-forms-clippy-zone-inner-wrap">
			<div class="caldera-forms-clippy">
				<h2>
					<?php esc_html_e( 'New To Caldera Forms?', 'caldera-forms' ); ?>
				</h2>
				<p>
					<?php esc_html_e( 'We have a complete getting started guide for new users.', 'caldera-forms' ); ?>
				</p>

				<a href="https://calderaforms.com/getting-started?utm-source=wp-admin&utm_campaign=clippy&utm_term=no-forms" target="_blank" class="bt-btn btn btn-orange">
					<?php esc_html_e( 'Read Now', 'caldera-forms' ); ?>
				</a

			</div>
		</div>

		<?php } ?>
	</div>
	</div>
	<div id="caldera-forms-admin-page-right">
		<div id="caldera-forms-clippy">
			<?php if ( ! is_ssl() ): ?>
				<div class="caldera-forms-clippy-zone warn-clippy">
					<div class="caldera-forms-clippy-zone-inner-wrap" style="background: white">
						<div class="caldera-forms-clippy"
						     style="background-color:white;border-left: 4px solid #dc3232;">
							<h2>
								<?php esc_html_e( 'Your Forms Might Be Marked Insecure', 'caldera-forms' ); ?>
							</h2>
							<p>
								<?php esc_html_e( 'WordPress reports that you are not using SSL. Your forms may be marked insecure by browsers if not loaded using HTTPS.', 'caldera-forms' ); ?>
							</p>
							<a href="https://calderaforms.com/docs/ssl?utm-source=wp-admin&utm_campaign=clippy&utm_term=support"
							   target="_blank" class="bt-btn btn btn-green" style="width: 80%;margin-left:5%;">
								<?php esc_html_e( 'Learn More', 'caldera-forms' ); ?>
							</a>
						</div>
					</div>
				</div>
			<?php endif ?>
			<docs :important="importantDocs"></docs>
			<extend :product="product" :title="extendTitle"></extend>
			<div class="caldera-forms-clippy-zone" style="background-image: url( '<?php echo esc_url( CFCORE_URL . 'assets/images/caldera-globe-logo-sm.png' ); ?>' );">
				<div class="caldera-forms-clippy-zone-inner-wrap">
					<div class="caldera-forms-clippy">
						<h2>
							<?php esc_html_e( 'Need Support? Or Found A Bug?', 'caldera-forms' ); ?>
						</h2>
						<p>
							<?php esc_html_e( 'In addition to our documentation, we have support licenses to get you extra help.', 'caldera-forms' ); ?>
						</p>
						<a href="https://calderaforms.com/support?utm-source=wp-admin&utm_campaign=clippy&utm_term=support" target="_blank" class="bt-btn btn btn-green">
							<?php esc_html_e( 'Learn About Our Support Options', 'caldera-forms' ); ?>
						</a>


						<a href="https://github.com/calderawp/caldera-forms/issues" target="_blank" class="bt-btn btn btn-green">
							<?php esc_html_e( 'Report A Bug', 'caldera-forms' ); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
		<?php echo Caldera_Forms_Entry_Viewer::full_viewer(); ?>

	</div>
</div>

<?php
do_action('caldera_forms_admin_templates');
?>
<script type="text/javascript">

function set_form_state( obj ){
	if( true === obj.data.success ){

		var row = jQuery('#form_row_' + obj.data.data.ID + '>td');
		row.first().attr('class', obj.data.data.state );
		obj.params.trigger.text( obj.data.data.label );

	}
}

function new_form_redirect(obj){
	if(typeof obj.data === 'string'){
		window.location = 'admin.php?page=caldera-forms&edit=' + obj.data.trim();
	}else{
		alert(obj.data.error);
	}
}

function serialize_modal_form(el){

	var clicked	= jQuery(el),
		data 	= clicked.closest('.baldrick-modal-wrap'),
		name 	= data.find('.new-form-name');

	if( clicked.hasClass( 'cf-loading-form' ) ){
		return false;
	}
	//verify name is set
	if(name.val().length < 1){
		name.focus().addClass('has-error');
		return false;
	}


	clicked.data('data', data.serialize()).addClass('cf-loading-form').animate({width: 348}, 200);

	jQuery('.cf-change-template-button').animate({ marginLeft: -175, opacity: 0 }, 200);

	return true;
}

var cf_front_end_settings = {};
function update_setting_toggle(obj){
	cf_front_end_settings = obj.data;
	toggle_front_end_settings();
}
function toggle_front_end_settings(){

	for( var k in cf_front_end_settings){
		if(cf_front_end_settings[k] === true){
			jQuery('.setting_toggle_' + k).addClass('active');
		}else{
			jQuery('.setting_toggle_' + k).removeClass('active');
		}
	}
}

function get_front_end_settings( obj ){
	//cf_front_end_settings
	return cf_front_end_settings;
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
	};

	var $notices = $('.error,.notice,.notice-error');
	$notices.remove();

	$( document ).on('submit', '#new_form_baldrickModal', function(e){
		e.preventDefault();
		var trigger = $(this).find('button.ajax-trigger');
		trigger.trigger('click');
	});
	var form_toggle_state = false;
	$( document ).on( 'click', '.hide-forms', function(){
		var clicked = $(this),
			panel = $('.form-admin-page-wrap'),
			forms = $('.form-panel-wrap'),
			size = -35;

		if( true === form_toggle_state ){
			size = 430;
			clicked.find('span').css({transform: ''});
			form_toggle_state = false;
			forms.attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' ).show();
		}else{
			form_toggle_state = true;
			clicked.find('span').css({transform: 'rotate(180deg)'});
			forms.attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' ).hide();
		}
		panel.animate( {marginLeft: size }, 220);


	});

	$( document ).on('change', '.cf-template-select', function(){
		var template = $(this).parent(),
			create = $('.cf-form-create'),
			name = $('.new-form-name');

		if( create.find('.cf-loading-form').length ){
			return;
		}
		$('.cf-template-title').html( template.find('small').html() );
		$('.cf-form-template.selected').removeClass('selected');
		template.addClass('selected');
		$('.cf-form-template.selected').animate( {opacity: 1}, 100 );
		//$('.cf-form-template:not(.selected)').animate( {opacity: 0.6}, 200 );
		// shift siding
		var box = $('.cf-templates-wrapper');
		var relativeX = box.offset().left - template.offset().left;
		var boxwid = box.offset().left + box.innerWidth();
		var diffwid = template.offset().left + template.innerWidth();
		$('.cf-form-template').css('overflow', 'hidden').find('.row,small').show();
		template.css('overflow', 'visible').find('.row,small').hide();
		if( boxwid - diffwid > template.outerWidth() ){
			create.css( { left : -2, right: '' } );
		}else{
			create.css( { right : -2, left: '' } );
		}

		create.appendTo(template).attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' ).fadeIn( 100 );

		name.focus();
	});
	$( document ).on('click', '.cf-change-template-button', function(){
		$('.cf-template-select:checked').prop('checked', false);
		$('.cf-form-template').removeClass('selected');
		//$('.cf-form-template').animate( {opacity: 1}, 200 );
		$('.cf-form-create').fadeOut(100, function(){
			$('.cf-form-template').css('overflow', 'hidden').find('div,small').fadeIn(100);
			$(this).attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
		})
	});



	//switch in and out of email settings
	var inEmailSettings = false;
	$( '#cf-email-settings' ).on( 'click', function(e){
		e.preventDefault();
		var $mainUI = $( '.form-panel-wrap, .form-entries-wrap' ),
			$emailSettingsUI = $('#cf-email-settings-ui'),
			$otherButtons = $('.caldera-forms-toolbar-item a'),
			$toggles = $('.toggle_option_preview, #render-with-label'),
			$clippy = $('#caldera-forms-clippy');

		if( inEmailSettings ){
			$( this ).html( '<?php esc_html_e( 'Email Settings', 'caldera-forms' ); ?>' );
			inEmailSettings = false;
			$otherButtons.removeClass( 'disabled' );
			$emailSettingsUI.hide().attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
			$mainUI.show().attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' );
			$toggles.show().attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' );
			$clippy.show().attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' );
		}else{
			inEmailSettings = true;
			$( this ).html( '<?php esc_html_e( 'Close Email Settings', 'caldera-forms' ); ?>' );
			$otherButtons.addClass( 'disabled' );
			$mainUI.hide().attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
			$emailSettingsUI.show().attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' );
			$( this ).html = "<?php esc_html__( 'Email Settings', 'caldera-forms' ); ?>";
			$clippy.hide().attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
			$toggles.hide().attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
		}



	});

	//handle save of email settings
	$( '#cf-email-settings-save' ).on( 'click', function( e ) {
		e.preventDefault( e );
		var data = {
			nonce: $('#cfemail').val(),
			action: 'cf_email_save',
			method: $('#cf-emails-api').val(),
			sendgrid: $('#cf-emails-sendgrid-key').val()
		};
		var $spinner = $( '#cf-email-spinner' );
		$spinner.attr( 'aria-hidden', false ).css( 'visibility', 'visible' ).show();

		$.post( ajaxurl, data ).done( function( r ) {
			$spinner.attr( 'aria-hidden', true ).css( 'visibility', 'hidden' ).hide(
				500, function(){
					document.getElementById( 'cf-email-settings' ).click();
				}
			);
		});

	});




	$(document).on('click', '.cf-form-shortcode-preview', function(){
		var clicked = $( this ),
			shortcode = clicked.prev(),
			name = shortcode.prev();
		name.hide();
		clicked.hide();
		shortcode.show().focus().select();
	});
	$(document).on('blur', '.cf-shortcode-preview', function(){
		var clicked = $( this ),
			form = clicked.prev(),
			name = clicked.next();
		clicked.hide();
		form.show();
		name.show();
	})

});
</script>
<?php


/**
 * Runs at the bottom of the main Caldera Forms admi page
 *
 * @since unknown
 */
do_action('caldera_forms_admin_footer');
?>
