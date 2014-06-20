<?php
/*
 * Ajax Submissions addon- included
 */

add_filter('caldera_forms_render_grid_structure', 'cf_ajax_structures', 10, 2);
add_action('caldera_forms_redirect', 'cf_ajax_redirect', 10, 5);
add_filter('caldera_forms_render_form_classes', 'cf_ajax_register_scripts', 10, 2);
add_action('caldera_forms_general_settings_panel', 'cf_form_ajaxsetup');

function cf_form_ajaxsetup($form){
?>
<div class="caldera-config-group">
	<label><?php echo __('Ajax Submissions', 'caldera-forms'); ?></label>
	<div class="caldera-config-field">
		<label><input type="checkbox" value="1" name="config[form_ajax]" class="field-config"<?php if(isset($form['form_ajax'])){ echo ' checked="checked"'; } ?>> <?php echo __('Enable Ajax Submissions. (No page reloads)', 'caldera-forms'); ?></label>
	</div>
</div>
<?php	
}

function cf_ajax_structures($grid, $form){

	if(empty($form['form_ajax'])){
		return $grid;
	}		
	global $current_form_count; 

	// add in notification area
	$grid->before('<div id="caldera_notices_'.$current_form_count.'" data-spinner="'. admin_url( 'images/spinner.gif' ).'"></div>', '1');

	return $grid;
}

function cf_ajax_redirect($type, $url, $form){	
	if(empty($form['form_ajax'])){
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
	$note_general_classes = array(
		'alert'
	);
	$note_general_classes = apply_filters('caldera_forms_render_note_general_classes', $note_general_classes, $form);

	// base id
	$form_id = 'caldera_form_1';

	if($type == 'complete'){
		if(isset($query['cf_su'])){
			$notices['success']['note'] = $form['success'];
			$form_id = 'caldera_form_' . $query['cf_su'];
		}else{
			$out['url'] = $url;
			$notices['success']['note'] = __('Redirecting');
		}
	}elseif($type == 'preprocess'){
		$data = get_transient( $query['cf_er'] );
		if(!empty($data['note'])){
			$notices[$data['type']]['note'] = $data['note'];
		}
	}elseif($type == 'error'){
		$data = get_transient( $query['cf_er'] );
		//dump($data);
		$field_slugs = array_keys($data['fields']);
		if(!empty($data['fields'])){
			foreach($form['fields'] as $fieldid=>$field){
				if( in_array( $field['slug'], $field_slugs ) ){
					$out['fields'][$fieldid] = $data['fields'][$field['slug']];
				}
			}
		}
		//$out['fields'] = 
		//$notices[$data['type']]['note'] = $data['note'];
	}

	$notices = apply_filters('caldera_forms_render_notices', $notices, $form);

	$note_classes = array(
		'success'	=> array_merge($note_general_classes, array(
			'alert-success'
		)),
		'error'	=> array_merge($note_general_classes, array(
			'alert-error'
		)),
		'info'	=> array_merge($note_general_classes, array(
			'alert-info'
		)),
		'warning'	=> array_merge($note_general_classes, array(
			'alert-warning'
		)),
		'danger'	=> array_merge($note_general_classes, array(
			'alert-danger'
		)),
	);
	
	$note_classes = apply_filters('caldera_forms_render_note_classes', $note_classes, $form);

	$html = '';

	if(!empty($notices)){
		// do notices
		foreach($notices as $note_type => $notice){
			if(!empty($notice['note'])){					
				$html .= '<div class=" '. implode(' ', $note_classes[$note_type]) . '">' . $notice['note'] .'</div>';
			}
		}

	}
	$out['html'] = $html;
	$out['status'] = $type;

	header('Content-Type: application/json');
	echo json_encode( $out );
	exit;

}

function cf_ajax_register_scripts($classes, $form){
	if(empty($form['form_ajax'])){
		return $classes;
	}	
	// setup attributes action
	add_filter('caldera_forms_render_form_attributes', 'cf_ajax_setatts', 10, 2);

	// enqueue scripts
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'cfajax-baldrick', CFCORE_URL . 'assets/js/jquery.baldrick.js', array('jquery'), '1.00', true );
	wp_enqueue_script( 'cfajax-core', plugin_dir_url(__FILE__) . 'js/ajax-core.js', array('cfajax-baldrick'), '1.00', true );

	$classes[] = 'cfajax-trigger';

	return $classes;
}

function cf_ajax_setatts($atts, $form){
	global $current_form_count;
	
	$resatts = array(
		'data-target'	=>	'#caldera_notices_'.$current_form_count,
		'data-template'	=>	'#cfajax_'.$form['ID'].'-tmpl',
		'data-action'	=>	'cfajax_setdate'
	);
	if(!empty($form['hide_form'])){
		$resatts['data-hiderows'] = "true";
	}

	return array_merge($atts, $resatts);

}
