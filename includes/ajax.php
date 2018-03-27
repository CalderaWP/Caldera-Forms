<?php
/*
 * Ajax Submissions 
 */


//add_filter('caldera_forms_render_grid_structure', 'cf_ajax_structures', 10, 2);
add_action('caldera_forms_redirect', 'cf_ajax_redirect', 10, 4 );
add_filter('caldera_forms_render_form_classes', 'cf_ajax_register_scripts', 10, 2);
add_action('caldera_forms_general_settings_panel', 'cf_form_ajaxsetup');
// add ajax actions
add_action( 'wp_ajax_cf_process_ajax_submit', 'cf_form_process_ajax' );
add_action( 'wp_ajax_nopriv_cf_process_ajax_submit', 'cf_form_process_ajax' );


function cf_form_process_ajax(){
	// hook into submision
	//_cf_cr_pst
	global $post;
	if( !empty( $_POST['_cf_cr_pst'] ) ){
		$post = get_post( (int) $_POST['_cf_cr_pst'] );
	}

	Caldera_Forms::process_form_via_post();
}


function cf_form_ajaxsetup($form){
	if( !isset( $form['custom_callback'] ) ){
		$form['custom_callback'] = null;
	}
?>
<div class="caldera-config-group">
	<fieldset>
		<legend><?php echo esc_html__( 'Ajax Submissions', 'caldera-forms'); ?></legend>
		<div class="caldera-config-field">
			<input type="checkbox" id="caldera-forms-enable_ajax" value="1" name="config[form_ajax]" class="field-config"<?php if(isset($form['form_ajax'])){ echo ' checked="checked"'; } ?>>
			<label for="caldera-forms-enable_ajax"><?php echo esc_html__( 'Enable Ajax Submissions. (No page reloads)', 'caldera-forms'); ?></label>
		</div>
	</fieldset>
</div>


<div class="caldera-config-group">
	<fieldset>
		<legend><?php esc_html_e( 'Custom Callback', 'caldera-forms'); ?></legend>
		<div class="caldera-config-field">
			<input id="caldera-forms-custom_callback" type="checkbox" onclick="jQuery('#custom_callback_panel').toggle();" value="1" name="config[has_ajax_callback]" class="field-config"<?php if(isset($form['has_ajax_callback'])){ echo ' checked="checked"'; } ?>><label for="caldera-forms-custom_callback"><?php echo esc_html__( 'Add a custom Javascript callback handlers on submission.', 'caldera-forms'); ?></label>
		</div>
	</fieldset>

</div>

<div id="custom_callback_panel" <?php if(empty($form['has_ajax_callback'])){ echo 'style="display:none;"'; } ?>>
	
	<div class="caldera-config-group">
		<fieldset>
			<legend>
				<?php esc_html_e( 'Inhibit Notices', 'caldera-forms'); ?>
			</legend>
			<div class="caldera-config-field">
				<input id="caldera-forms-inhbit_notices" type="checkbox" value="1" name="config[inhibit_notice]" class="field-config"<?php if(isset($form['inhibit_notice'])){ echo ' checked="checked"'; } ?>><label for="caldera-forms-inhbit_notices"><?php esc_html_e("Don't show default alerts (success etc.)", 'caldera-forms'); ?></label>
			</div>
		</fieldset>
	</div>


	<div class="caldera-config-group" style="width:500px;">
		<fieldset>
			<legend>
				<?php esc_html_e( 'Callback Function', 'caldera-forms'); ?>
			</legend>
			<div class="caldera-config-field">
				<input id="caldera-forms-custom_callback" type="text" value="<?php echo $form['custom_callback']; ?>" name="config[custom_callback]" class="field-config block-input" aria-describedby="caldera-forms-custom_callback-desc">
				<p class="description" id="caldera-forms-custom_callback-desc">
					<?php esc_html_e('Javascript function to call on submission. Passed an object containing form submission result.'); ?> <a href="#" onclick="jQuery('#json_callback_example').toggle();return false;"><?php esc_html_e( 'See Example', 'caldera-forms' ); ?></a>
				</p>
					<pre id="json_callback_example" style="display:none;"><?php echo htmlentities('
{    
    "data": {
        "cf_id"		: "200", // Form Entry ID
        "my_var" 	: "custom passback variable defined in variables tab",
        "my_other" 	: "another custom passback variable",
        "get_var"	: "GET variable from embedded page",
        "more_var"	: "another GET variable",
    },
    "url"		: "redirect url. only included if redirection is needed. e.g redirect processor",
    "result"	: "Sent. Thank you, David.", // result text after magic tag render.
    "html"		: "<div class=\"alert alert-success\">Sent. Thank you, David.</div>",
    "type"		: "complete",
    "form_id"	: "CF551d804e0d72e",
    "form_name"	: "Example Form",
    "status"	: "complete"
}'); ?>
				</pre>
			</div>
		</fieldset>
	</div>




</div>

<div class="caldera-config-group">
	<fieldset>
		<legend>
			<?php echo esc_html__( 'Multiple Ajax Submissions', 'caldera-forms'); ?>
		</legend>
		<div class="caldera-config-field">
			<input for="caldera-forms-multi-ajax" type="checkbox" value="1" name="config[form_ajax_post_submission_disable]" class="field-config"<?php if(isset($form['form_ajax_post_submission_disable'])){ echo ' checked="checked"'; } ?>><label for="caldera-forms-multi-ajax"><?php esc_html_e( 'If set, form can be submitted multiple times with out a new page load.', 'caldera-forms'); ?></label>
		</div>
	</fieldset>
</div>
<?php	
}



function cf_ajax_redirect($type, $url, $form){

	if(empty($form['form_ajax'])){
		return;
	}

	if( empty( $_POST['cfajax'] ) || empty( $_POST['action'] ) || $_POST['action'] != 'cf_process_ajax_submit' ){
		return;
	}

	$data = Caldera_Forms::get_submission_data($form);
	
	// setup notcies
	$urlparts = parse_url($url);
	$query = array();

	if(!empty($urlparts['query'])){
		parse_str($urlparts['query'], $query);
	}

	$notices = array();
	$note_general_classes = Caldera_Forms_Render_Notices::get_note_general_classes( $form );

	// base id
	$form_id = 'caldera_form_1';

	if($type == 'complete'){
		if(isset($query['cf_su'])){
			$notices['success']['note'] = $form['success'];
			$form_id = 'caldera_form_' . $query['cf_su'];
		}else{
			$out['url'] = $url;
			$notices['success']['note'] = esc_html__( 'Redirecting', 'caldera-forms');
		}
	}elseif($type == 'preprocess'){
		if(isset($query['cf_er'])){
			$data = Caldera_Forms_Transient::get_transient( $query['cf_er'] );
			if(!empty($data['note'])){
				$notices[$data['type']]['note'] = $data['note'];
			}

		}else{
			$out['url'] = $url;
			$notices['success']['note'] = esc_html__( 'Redirecting', 'caldera-forms');
		}

	}elseif($type == 'error'){
		$data = Caldera_Forms_Transient::get_transient( $query['cf_er'] );
		if(!empty($data['note'])){
			$notices['error']['note'] = $data['note'];
		}

	}
	// check for field erors
	if(!empty($data['fields'])){
		foreach($form['fields'] as $fieldid=>$field){
			if( isset( $data['fields'][$fieldid] ) ){

				if($urlparts['path'] == 'api'){
					$out['fields'][$field['slug']] = Caldera_Forms_Sanitize::remove_scripts($data['fields'][$fieldid]);
				}else{
					$out['fields'][$fieldid] = Caldera_Forms_Sanitize::remove_scripts($data['fields'][$fieldid]);
				}
			}
		}
	}

	$notices = Caldera_Forms_Render_Notices::prepare_notices( $notices, $form );
	$note_classes = Caldera_Forms_Render_Notices::get_note_classes( $note_general_classes, $form );

	$html = Caldera_Forms_Render_Notices::html_from_notices( $notices, $note_classes );

	if(!empty($result)){
		$out['result'] = $result;
	}

	if(!empty($query)){
		if(!empty($query['cf_su'])){
			unset($query['cf_su']);
		}
		if(!empty($query['cf_ee'])){
			$out['entry'] = $query['cf_ee'];
		}
		$out['data'] = $query;
	}
	$out['html'] = Caldera_Forms_Sanitize::remove_scripts($html);
	$out['type'] = ( isset($data['type']) ? $data['type'] : $type );
	$out['form_id'] = $form['ID'];
	$out['form_name'] = $form['name'];	
	$out['status'] = $type;

	if( ! empty( $form[ 'scroll_top' ] ) ){
		$out[ 'scroll' ] = Caldera_Forms_Render_Util::notice_element_id( $form, absint( $_POST[ '_cf_frm_ct' ]  ) );
	}

	$out = apply_filters( 'caldera_forms_ajax_return', $out, $form);

	wp_send_json( $out );
	exit;

}

function cf_ajax_register_scripts($classes, $form){
	if(empty($form['form_ajax'])){
		return $classes;
	}


	// enqueue scripts
	wp_enqueue_script( 'cf-baldrick' );
	wp_enqueue_script( 'cf-ajax' );

	$classes[] = 'cfajax-trigger';

	return $classes;
}

function cf_ajax_setatts($atts, $form){
	global $current_form_count;

	$post_disable = 0;
	if ( isset( $form[ 'form_ajax_post_submission_disable' ] ) ) {
		$post_disable = $form[ 'form_ajax_post_submission_disable' ];
	}
	
	$resatts = array(
		'data-target'		=>	'#caldera_notices_'.$current_form_count,
		'data-template'		=>	'#cfajax_'.$form['ID'].'-tmpl',
		'data-cfajax'		=>	$form['ID'],
		'data-load-element' =>	'_parent',
		'data-load-class' 	=>	'cf_processing',
		'data-post-disable' =>	$post_disable,
		'data-action'		=>	'cf_process_ajax_submit',
		'data-request'		=>	cf_ajax_api_url( $form[ 'ID' ] ),
	);
	
	if( !empty( $form['custom_callback'] ) ){
		$resatts['data-custom-callback'] = $form['custom_callback'];
	}
	if( !empty( $form['has_ajax_callback']) && !empty( $form['inhibit_notice'] ) ){
		$resatts['data-inhibitnotice'] = true;
	}

	if(!empty($form['hide_form'])){
		$resatts['data-hiderows'] = "true";
	}

	return array_merge($atts, $resatts);

}

/**
 * Get URL for API for processing a form
 *
 * @since 1.3.2
 *
 * @param string $form_id Form ID
 *
 * @return string
 */
function cf_ajax_api_url( $form_id ) {
	
	return Caldera_Forms::get_submit_url( $form_id );

}


/**
 * Perform a redirect using the best means possible
 *
 * This is copypasted from Pods. Thanks Pods! Very GPL.
 *
 * @param string $location The path to redirect to.
 * @param int $status Optional. Status code to use. Default is 302
 *
 * @return void
 *
 * @since 1.3.4
 */
function cf_redirect( $location, $status = 302 ) {
	if ( !headers_sent() ) {
		wp_redirect( $location, $status );
		die();
	}else {
		die( '<script type="text/javascript">'
		     . 'document.location = "' . str_replace( '&amp;', '&', esc_js( $location ) ) . '";'
		     . '</script>' );
	}

}
