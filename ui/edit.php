<?php
//php_cs::disable

global $field_type_list, $field_type_templates;

if( ! isset( $_GET[  Caldera_Forms_Admin::EDIT_KEY  ] ) || ! is_string(  Caldera_Forms_Admin::EDIT_KEY  ) ){
	wp_die( esc_html__( 'Invalid form ID', 'caldera-forms'  ) );
}

if( Caldera_Forms_Admin::is_revision_edit() ){
	$element = $form = Caldera_Forms_Forms::get_revision( $_GET[ Caldera_Forms_Admin::REVISION_KEY ]  );
} else{
	$element = $form = Caldera_Forms_Forms::get_form( $_GET[ Caldera_Forms_Admin::EDIT_KEY ] );

}

if( empty( $element ) || ! is_array( $element ) ){
	wp_die( esc_html__( 'Invalid Form.', 'caldera-forms'  ) );
}
/**
 * Runs before form editor is rendered, after form is gotten from DB.
 *
 * @since 1.4.3
 *
 * @param array $element Form config
 */
do_action( 'caldera_forms_prerender_edit', $element );

/**
 * Filter which Magic Tags are available in the form editor
 *
 *
 * @since 1.3.2
 *
 * @param array $tags Array of magic registered tags 
 * @param array $form_id for which this applies.
 */
$magic_tags = apply_filters( 'caldera_forms_get_magic_tags', array(), $element['ID'] );

if(empty($element['success'])){
	$element['success'] = esc_html__( 'Form has been successfully submitted. Thank you.', 'caldera-forms' );
}

if(!isset($element['db_support'])){
	$element['db_support'] = 1;
}


/**
 * Convert existing field conditions if old method used
 *
 * @since 1.3.0
 */
if( empty( $element['conditional_groups'] ) ){
	
	$element['conditional_groups'] = array();
	if( !empty( $element['fields'] ) ){
		foreach( $element['fields'] as $field_id=>$field ){

			if( !empty( $field['conditions'] ) && !empty( $field['conditions']['type'] ) ){

				if( empty( $field['conditions']['group'] ) ){
					continue;
				}
				$element['conditional_groups']['conditions'][ 'con_' . $field['ID'] ] = array(
					'id' => 'con_' . $field['ID'],
					'name'	=> $field['label'],
					'type'	=> $field['conditions']['type'],
					'fields'=> array(),
					'group' => array()
				);

				foreach( $field['conditions']['group'] as $groups_id=>$groups ){
					foreach( $groups as $group_id => $group ){
						$element['conditional_groups']['conditions'][ 'con_' . $field['ID'] ]['fields'][ $group_id ] = $group['field'];
						$element['conditional_groups']['conditions'][ 'con_' . $field['ID'] ]['group'][ $groups_id ][ $group_id ] = array(
							'parent'	=>	$groups_id,
							'field'		=>	$group['field'],
							'compare'	=>	$group['compare'],
							'value'		=>	$group['value']
						);
					}
				}
				$element['fields'][ $field_id ]['conditions'] = array(
					'type' => 'con_' . $field['ID']
				);
			}
		}
	}
}

if ( ! isset( $element['fields'] ) ) {
	$element['fields'] = array();
}

$element['conditional_groups']['fields'] = $element['fields'];

// place nonce field
wp_nonce_field( 'cf_edit_element', 'cf_edit_nonce' );

// Init check
echo "<input id=\"last_updated_field\" name=\"config[_last_updated]\" value=\"" . esc_attr( date( 'r' ) ) . "\" type=\"hidden\">";
echo "<input id=\"form_id_field\" name=\"config[ID]\" value=\"" . esc_attr( $_GET[ 'edit' ] ) . "\" type=\"hidden\">";
echo "<input id=\"form_db_id_field\" name=\"config[db_id]\" value=\"" . esc_attr( $element[ 'db_id' ] ) . "\" type=\"hidden\">";

do_action('caldera_forms_edit_start', $element);

// Get Fieldtpyes
$field_types = Caldera_Forms_Fields::get_all();

// Get Elements
$panel_extensions = Caldera_Forms_Admin_Panel::get_panels();


$field_type_list = array();
$field_type_templates = array();
$field_type_defaults = array(
	"var fieldtype_defaults = {};"
);

// options based template
$field_options_template = "
<div class=\"caldera-config-group caldera-config-group-full\">
	<div class=\"caldera-config-group\">
		<div class=\"caldera-config-field\">
			<label><input id=\"{{_id}}_auto\" type=\"checkbox\" class=\"auto-populate-options field-config\" name=\"{{_name}}[auto]\" value=\"1\" {{#if auto}}checked=\"checked\"{{/if}}> ".esc_html__( 'Auto Populate', 'caldera-forms' )."</label>
		</div>
	</div>
</div>
{{#if auto}}{{#script}}jQuery('#{{_id}}_auto').trigger('change');{{/script}}{{/if}}
<div class=\"caldera-config-group-auto-options\" style=\"display:none;\">
	<div class=\"caldera-config-group\">
		<label>". esc_html__( 'Source', 'caldera-forms' ) . "</label>
		<div class=\"caldera-config-field\">
			<select class=\"block-input field-config auto-populate-type\" name=\"{{_name}}[auto_type]\">
				<option value=\"\">" . esc_html__( 'Select a source', 'caldera-forms' ) . "</option>
				<option value=\"post_type\"{{#is auto_type value=\"post_type\"}} selected=\"selected\"{{/is}}>" . esc_html__( 'Post Type', 'caldera-forms' ) . "</option>
				<option value=\"taxonomy\"{{#is auto_type value=\"taxonomy\"}} selected=\"selected\"{{/is}}>" . esc_html__( 'Taxonomy', 'caldera-forms' ) . "</option>";
				ob_start();

				/**
				 * Runs after default field auto-population types options are outputted, inside of the select element.
				 *
				 * Use this to add new options in UI for auto-population sources
				 *
				 * @since unknown
				 */
				do_action( 'caldera_forms_autopopulate_types' );
				$field_options_template .= ob_get_clean() . "
			</select>
		</div>
	</div>
	
	<div class=\"caldera-config-group caldera-config-group-auto-taxonomy auto-populate-type-panel\" style=\"display:none;\">
		<label>". esc_html__( 'Taxonomy', 'caldera-forms' )."</label>
		<div class=\"caldera-config-field\">
			<select class=\"block-input field-config\" name=\"{{_name}}[taxonomy]\">";

			$taxonomies = get_taxonomies();

	    	foreach($taxonomies as $tax_type=>$tax_name){
	    		$field_options_template .= "<option value=\"" . $tax_type . "\" {{#is taxonomy value=\"" . $tax_type . "\"}}selected=\"selected\"{{/is}}>" . $tax_name . "</option>\r\n";
	    	}
	    	
			$field_options_template .= "</select>

		</div>
	</div>

	<div class=\"caldera-config-group caldera-config-group-auto-post_type auto-populate-type-panel\" style=\"display:none;\">
		<label>".esc_html__( 'Post Type', 'caldera-forms' ) ."</label>
		<div class=\"caldera-config-field\">
			<select class=\"block-input field-config\" name=\"{{_name}}[post_type]\">";

			$post_types = get_post_types(array(), 'objects');

	    	foreach($post_types as $type){
	    		$field_options_template .= "<option value=\"" . $type->name . "\" {{#is post_type value=\"" . $type->name . "\"}}selected=\"selected\"{{/is}}>" . $type->labels->name . "</option>\r\n";
	    	}

			$field_options_template .= "</select>

		</div>
	</div>

	<div class=\"caldera-config-group caldera-config-group-auto-taxonomy caldera-config-group-auto-post_type auto-populate-type-panel\" style=\"display:none;\">
		<label>". esc_html__( 'Value', 'caldera-forms' )."</label>
		<div class=\"caldera-config-field\">
			<select class=\"block-input field-config\" name=\"{{_name}}[value_field]\">
				<option value=\"name\" {{#is value_field value=\"name\"}}selected=\"selected\"{{/is}}>Name</option>\r\n
				<option value=\"id\" {{#is value_field value=\"id\"}}selected=\"selected\"{{/is}}>ID</option>\r\n
	    	</select>
		</div>
	</div>
	<div class=\"caldera-config-group caldera-config-group-auto-taxonomy auto-populate-type-panel\" style=\"display:none;\">
		<label>". esc_html__( 'Orderby', 'caldera-forms' )."</label>
		<div class=\"caldera-config-field\">
			<select class=\"block-input field-config\" name=\"{{_name}}[orderby_tax]\">
				<option value=\"count\" {{#is value_field value=\"count\"}}selected=\"selected\"{{/is}}>
					" . __( 'Count', 'caldera-forms'  ) ."
				</option>\r\n
				<option value=\"id\" {{#is value_field value=\"id\"}}selected=\"selected\"{{/is}}>
					" . __( 'ID', 'caldera-forms'  ) ."
				</option>\r\n
				<option value=\"name\" {{#is value_field value=\"name\"}}selected=\"selected\"{{/is}}>
					" . __( 'Name', 'caldera-forms'  ) ."
				</option>\r\n
				<option value=\"slug\" {{#is value_field value=\"slug\"}}selected=\"selected\"{{/is}}>
					" . __( 'Slug', 'caldera-forms'  ) ."
				</option>\r\n
	    	</select>
		</div>
	</div>
	<div class=\"caldera-config-group caldera-config-group-auto-post_type auto-populate-type-panel\" style=\"display:none;\">
		<label>". esc_html__( 'Orderby', 'caldera-forms' )."</label>
		<div class=\"caldera-config-field\">
			<select class=\"block-input field-config\" name=\"{{_name}}[orderby_post]\">
				<option value=\"ID\" {{#is value_field value=\"ID\"}}selected=\"selected\"{{/is}}>
					" . __( 'ID', 'caldera-forms'  ) ."
				</option>\r\n
				<option value=\"name\" {{#is value_field value=\"name\"}}selected=\"selected\"{{/is}}>
					" . __( 'Name (post slug)', 'caldera-forms'  ) ."
				</option>\r\n
				<option value=\"author\" {{#is value_field value=\"author\"}}selected=\"selected\"{{/is}}>
					" . __( 'Author', 'caldera-forms'  ) ."
				</option>\r\n
				<option value=\"title\" {{#is value_field value=\"title\"}}selected=\"selected\"{{/is}}>
					" . __( 'Title', 'caldera-forms'  ) ."
				</option>\r\n
				<option value=\"date\" {{#is value_field value=\"date\"}}selected=\"selected\"{{/is}}>
					" . __( 'Publish Date', 'caldera-forms'  ) ."
				</option>\r\n
				<option value=\"modified\" {{#is value_field value=\"modified\"}}selected=\"selected\"{{/is}}>
					" . __( 'Modified Date', 'caldera-forms'  ) ."
				</option>\r\n
				<option value=\"parent\" {{#is value_field value=\"parent\"}}selected=\"selected\"{{/is}}>
					" . __( 'Parent ID', 'caldera-forms'  ) ."
				</option>\r\n
				<option value=\"comment_count\" {{#is value_field value=\"comment_count\"}}selected=\"selected\"{{/is}}>
					" . __( 'Comment Count', 'caldera-forms'  ) ."
				</option>\r\n
				<option value=\"menu_order\" {{#is value_field value=\"menu_order\"}}selected=\"selected\"{{/is}}>
					" . __( 'Menu Order', 'caldera-forms'  ) ."
				</option>\r\n
	    	</select>
		</div>
	</div>
	<div class=\"caldera-config-group caldera-config-group-auto-taxonomy caldera-config-group-auto-post_type auto-populate-type-panel\" style=\"display:none;\">
		<label>". esc_html__( 'Order', 'caldera-forms' )."</label>
		<div class=\"caldera-config-field\">
			<select class=\"block-input field-config\" name=\"{{_name}}[order]\">
				<option value=\"ASC\" {{#is value_field value=\"ASC\"}}selected=\"selected\"{{/is}}>
					" . __( 'Ascending', 'caldera-forms'  ) ."
				</option>\r\n
				<option value=\"DESC\" {{#is value_field value=\"DESC\"}}selected=\"selected\"{{/is}}>
					" . __( 'Descending', 'caldera-forms'  ) ."
				</option>\r\n
	    	</select>
		</div>
	</div>


	";
	ob_start();

	/**
	 * Runs after default options for auto-populate fields
	 *
	 * Use this to add new options in UI when making custom aut-population types
	 *
	 * @since unknown
	 */
	do_action( 'caldera_forms_autopopulate_type_config' );

	/**
	 * Filter to setup presets for option fields
	 *
	 * Use this to add new option presets for option based fields like Checkboxes, radios and selects
	 *
	 * @since 1.4.0
	 * @param array $presets Array of current presets 
	 * @param array $element current structure of form
	 */
	$option_presets = apply_filters( 'caldera_forms_field_option_presets', array(), $element );
	$preset_options = array();
	if( !empty( $option_presets ) && is_array( $option_presets ) ){
		foreach ($option_presets as $preset_name => $preset ) {
			if( empty( $preset['name'] ) ){ continue; }
			$preset_options[] = '<option value="' . esc_attr( $preset_name ) . '">' . esc_html( $preset['name'] ) . '</option>';
		}
	}
	$preset_options = implode(' ', $preset_options );

	$field_options_template .= ob_get_clean() . "

</div>
<div class=\"caldera-config-group-toggle-options\" {{#if auto}}style=\"display:none;\"{{/if}}>
	<div class=\"caldera-config-group caldera-config-group-full\">
		<button type=\"button\" class=\"button add-toggle-option add-option\" style=\"width: 180px;\">" . esc_html__( 'Add Option', 'caldera-forms' ) . "</button>
		<button type=\"button\" data-bulk=\"#{{_id}}_bulkwrap\" class=\"button add-toggle-option\" style=\"width: 190px;\">" . esc_html__( 'Bulk Insert / Preset', 'caldera-forms' ) . "</button>
		<div id=\"{{_id}}_bulkwrap\" style=\"display:none; margin-top:10px;\" class=\"bulk-preset-panel\">
		<select data-bulk=\"#{{_id}}_batch\" class=\"preset_options block-input\" style=\"margin-bottom:6px;\">
		<option value=\"\">" . esc_html__( 'Select a preset', 'caldera-forms' ) . "</option>
		" . $preset_options . "
		</select>		
		<textarea style=\"resize:vertical; height:200px;\" class=\"block-input\" id=\"{{_id}}_batch\"></textarea>
		<p class=\"description\">" . esc_html__( 'Single option per line. These replace the current list.', 'caldera-forms' ) . "</p>
		<button type=\"button\" data-options=\"#{{_id}}_batch\" class=\"button block-button add-toggle-option\" style=\"margin: 10px 0;\">" . esc_html__( 'Insert Options', 'caldera-forms' ) . "</button>
		</div>
	</div>
	<div class=\"caldera-config-group caldera-config-group-full\">
	<label style=\"padding: 10px;\"><input type=\"radio\" class=\"toggle_set_default field-config\" name=\"{{_name}}[default]\" value=\"\" {{#unless default}}checked=\"checked\"{{/unless}}> " . esc_html__( 'No Default', 'caldera-forms' ) . "</label>
	<label class=\"pull-right\" style=\"padding: 10px;\"><input type=\"checkbox\" class=\"toggle_show_values field-config\" name=\"{{_name}}[show_values]\" value=\"1\" {{#if show_values}}checked=\"checked\"{{/if}}> " . esc_html__( 'Show Values', 'caldera-forms' ) . "</label>
	</div>
	
	<div class=\"caldera-config-group caldera-config-group-full toggle-options field-options caldera-config-field\" data-field=\"{{_id}}\">
		{{#each option}}
			<div class=\"toggle_option_row 315\">
					<i class=\"dashicons dashicons-sort option-group-control\" style=\"padding: 4px 9px;\"></i>
					
					<input type=\"radio\" class=\"toggle_set_default field-config option-group-control\" name=\"{{../_name}}[default]\" value=\"{{@key}}\" {{#is ../default value=\"@key\"}}checked=\"checked\"{{/is}}>
					
					<a href=\"https://calderaforms.com/doc/select-options/?utm_source=wp-admin&utm_medium=form-editor&utm_content=select-options\" target=\"_blank\" class=\"dashicons dashicons-editor-help\" style=\"float:right;\" data-toggle=\"tooltip\" data-placement=\"bottom\"  title=\"" . esc_attr( __( 'Learn more about using select field options', 'caldera-forms' ) ) . "\"></a>
		
					<div class=\"caldera-config-group\">
						<label class=\"option-setting-label option-setting-label-for-value\" for=\"opt-calc-val-{{@key}}\">
							". esc_html__( 'Calculation Value', 'caldera-forms' ) . "
						</label>
						<input {{#unless ../show_values}} style=\"display:none;\"{{/unless}} type=\"text\" class=\"toggle_calc_value_field field-config option-setting \" name=\"{{../_name}}[option][{{@key}}][calc_value]\" value=\"{{#if ../show_values}}{{calc_value}}{{else}}{{label}}{{/if}}\" placeholder=\"" . esc_attr( __( 'Calculation Value', 'caldera-forms' ) ) . "\" id=\"opt-calc-val-{{@key}}\" data-opt=\"{{@key}}\" />
					</div>
					
					<div class=\"caldera-config-group\">
						<label class=\"option-setting-label option-setting-label-for-value\" for=\"opt-val-{{@key}}\">
							". esc_html__( 'Value', 'caldera-forms' ) . " 
						</label>
						<input  {{#unless ../show_values}} style=\"display:none;\"{{/unless}} type=\"text\" class=\"toggle_value_field option-setting field-config  required \" name=\"{{../_name}}[option][{{@key}}][value]\" value=\"{{#if ../show_values}}{{value}}{{else}}{{label}}{{/if}}\"" . esc_attr( __( 'Value', 'caldera-forms' ) ) . "\" id=\"opt-val-{{@key}}\" data-opt=\"{{@key}}\" />
						
					</div>
					
					<div class=\"caldera-config-group\">
						<label class=\"option-setting-label option-setting-label-for-label\" for=\"opt-label-{{@key}}\">
							". esc_html__( 'Label', 'caldera-forms' ) . "
						</label>
						<input {{#unless ../show_values}} style=\"width:245px;\"{{/unless}} type=\"text\" data-option=\"{{@key}}\" class=\"toggle_label_field option-setting field-config required\" name=\"{{../_name}}[option][{{@key}}][label]\" value=\"{{label}}\" placeholder=\"" . esc_attr( __( 'Label', 'caldera-forms' ) ) . "\" for=\"opt-label-{{@key}}\" data-opt=\"{{@key}}\" />
					</div>	
				<button class=\"button button-small toggle-remove-option\" type=\"button\">
					<i class=\"icn-delete\"></i>
				</button>		
			</div>
		{{/each}}
		
	</div>
	
	<div style=\"display:none;\" class=\"notice error\">
		<p>" . esc_html__( 'Option values must be unique.', 'caldera-forms' ) . "</p>
	</div>
</div>
";

$default_template = "
<div class=\"caldera-config-group\">
	<label>" . esc_html__( 'Default', 'caldera-forms' ) . "</label>
	<div class=\"caldera-config-field\">
		<input type=\"text\" class=\"block-input field-config\" name=\"{{_name}}[default]\" value=\"{{default}}\">
	</div>
</div>
";


// type list
$field_type_list = array(
	esc_html__( 'Basic', 'caldera-forms' )       => array(),
	esc_html__( 'Select', 'caldera-forms' )         => array(),
	esc_html__( 'eCommerce', 'caldera-forms' )         => array(),
	esc_html__( 'File', 'caldera-forms' )      => array(),
	esc_html__( 'Content', 'caldera-forms' )      => array(),
	esc_html__( 'Special', 'caldera-forms' ) => array(),
	
);

// Build Field Types List
foreach($field_types as $field_slug=>$config){

    if ( ! empty( $field['cf2'])) {
        if (!file_exists($config['file'])) {
            if (!function_exists($config['file'])) {
                continue;
            }
        }
    }

    $categories = array();
	if(!empty($config['category'])){
		$categories = explode(',', $config['category']);
	}
	foreach((array) $categories as $category){
		if( !isset( $field_type_list[trim($category)] ) ){
			$category = esc_html__( 'Special', 'caldera-forms' );
		}
		$field_type_list[trim($category)][$field_slug] = $config;
	}

	ob_start();
	do_action('caldera_forms_field_settings_template', $config, $field_slug );
	if(!empty($config['setup']['template'])){
		if(file_exists( $config['setup']['template'] )){
			// create config template block							
			include $config['setup']['template'];
		}
	}
	$field_type_templates[sanitize_key( $field_slug ) . "_tmpl"] = ob_get_clean();

	if(isset($config['options'])){
		if(!isset($field_type_templates[sanitize_key( $field_slug ) . "_tmpl"])){
			$field_type_templates[sanitize_key( $field_slug ) . "_tmpl"] = null;
		}

		// has configurable options - include template
		$field_type_templates[sanitize_key( $field_slug ) . "_tmpl"] .= $field_options_template;
	}

	
	if(!empty($config['setup']['default'])){
		$field_type_defaults[] = "fieldtype_defaults." . sanitize_key( $field_slug ) . "_cfg = " . json_encode($config['setup']['default']) .";";
	}
	if(!empty($config['setup']['not_supported'])){
		$field_type_defaults[] = "fieldtype_defaults." . sanitize_key( $field_slug ) . "_nosupport = " . json_encode($config['setup']['not_supported']) .";";
	}

	if(empty($config['setup']['preview']) || !file_exists( $config['setup']['preview'] )){

		// if preview is a function
		if(!empty($config['setup']['preview']) && function_exists($config['setup']['preview'])){
			$func = $config['setup']['preview'];
			$field_type_templates['preview-' . sanitize_key( $field_slug ) . "_tmpl"] = $func($config);
		}else{
			// simulate a preview with actual field file
			$field = array(
				'label'	=>	'{{label}}',
				'slug'	=>	'{{slug}}',
				'type'	=>	'{{type}}',
				'caption' => '{{caption}}',
				'config' => (!empty($config['setup']['default']) ? $config['setup']['default'] : array() )
			);

			$field_name = $field['slug'];
			$field_id = 'preview_fld_' . $field['slug'];
			$wrapper_before = "<div class=\"preview-caldera-config-group\">";
			$field_before = "<div class=\"preview-caldera-config-field\">";
			$field_after = '</div>';
			$wrapper_after = '</div>';
			$field_label = "<label for=\"" . $field_id . "\" class=\"control-label\">" . $field['label'] . "</label>\r\n";
			$field_required = "";
			$field_placeholder = 'placeholder="' . $field['label'] .'"';
			$field_caption = "<span class=\"help-block\">" . $field['caption'] . "</span>\r\n";
			
			// blank default
			$field_value = null;
			$field_class = "preview-field-config";
			if( file_exists( $config[ 'file' ] ) ){
				$file = $config[ 'file' ];
			}else{
				$file = CFCORE_PATH . 'fields/generic-input';
			}
			ob_start();
			include $file;
			$field_type_templates['preview-' . sanitize_key( $field_slug ) . "_tmpl"] = ob_get_clean();
		}
	}else{
		ob_start();
		include $config['setup']['preview'];
		$field_type_templates['preview-' . sanitize_key( $field_slug ) . "_tmpl"] = ob_get_clean();
	}


}



function caldera_forms_field_wrapper_template($field_id = '{{field_id}}', $label = '{{label}}', $slug = '{{slug}}', $caption = '{{caption}}', $hide_label = '{{hide_label}}', $required = '{{required}}', $entry_list = '{{entry_list}}', $type = null, $config_str = '{{json config}}', $conditions_str = '{"type" : ""}'){

	if(is_array($config_str)){
		$config 	= $config_str;
		$config_str = json_encode( $config_str );

	}else{
		$config = json_decode($config_str, true);
	}
$form = [];

	$condition_type = '';
	if(!empty($conditions_str)){
		$conditions = json_decode($conditions_str, true);
		if(!empty($conditions['type'])){
			$condition_type = $conditions['type'];
		}
		if(!empty($conditions['group'])){
			$groups = array();
			foreach ($conditions['group'] as $groupid => $group) {
				$group_tmp = array(
					'field_id' => $groupid,
					'type'	=> 'fields',
					'lines' => array()
				);
				if(!empty($group)){
					foreach($group as $line_id => $line){
						$group_line = $line;
						$group_line['field_id'] = $line_id;
						$group_tmp['lines'][] = $group_line;
					}
				}
				$groups[] = $group_tmp;
			}
			$conditions['group'] = $groups;
			$conditions_str = json_encode($conditions);
		}
	}	
	
	?>
	<div class="caldera-editor-field-config-wrapper caldera-editor-config-wrapper ajax-trigger" 
	
        data-request="setup_field_type"
        data-event="field.drop"
        data-load-class="none"
        data-modal="field_setup"
        data-modal-title="<?php echo esc_html__( 'Fields', 'caldera-forms' ); ?>"
        data-template="#form-fields-selector-tmpl"
        data-modal-width="700"
        data-modal-height="680"
	    id="<?php echo esc_attr($field_id); ?>"
         style="display:none;"
    >
		

		<h3 class="caldera-editor-field-title">
            <?php echo esc_html( $label ); ?>&nbsp;
        </h3>
		<input type="hidden" class="field-config" name="config[fields][<?php echo esc_attr($field_id); ?>][ID]" value="<?php echo esc_attr($field_id); ?>">

		<div id="<?php echo esc_attr($field_id); ?>_settings_pane" class="wrapper-instance-pane">

            <div class="caldera-config-group">
				<label for="<?php echo esc_attr($field_id); ?>_type"><?php echo esc_html__( 'Field Type', 'caldera-forms' ); ?></label>
				<div class="caldera-config-field">
					<select class="block-input caldera-select-field-type" data-field="<?php echo esc_attr($field_id); ?>" id="<?php echo esc_attr($field_id); ?>_type" name="config[fields][<?php echo esc_attr($field_id); ?>][type]" data-type="<?php echo $type; ?>">
						<?php
						echo build_field_types($type);
						?>
					</select>
				</div>
			</div>

			<div class="caldera-config-group">
				<label for="<?php echo esc_attr($field_id); ?>_fid"><?php echo esc_html__( 'ID', 'caldera-forms' ); ?></label>
				<div class="caldera-config-field">
					<input type="text" class="block-input field-id" id="<?php echo esc_attr($field_id); ?>_fid" value="<?php echo esc_attr($field_id); ?>" readonly="readonly">
				</div>
			</div>


			<div class="caldera-config-group">
				<label for="<?php echo esc_attr($field_id); ?>_lable"><?php echo esc_html__( 'Name', 'caldera-forms' ); ?></label>
				<div class="caldera-config-field">
					<input type="text" class="block-input field-config field-label required" id="<?php echo esc_attr($field_id); ?>_lable" name="config[fields][<?php echo esc_attr($field_id); ?>][label]" value="<?php echo sanitize_text_field( $label ); ?>">
				</div>
			</div>

			<div class="caldera-config-group hide-label-field">
				<label for="<?php echo esc_attr($field_id); ?>_hide_label"><?php echo esc_html__( 'Hide Label', 'caldera-forms' ); ?></label>
				<div class="caldera-config-field">
					<input type="checkbox" class="field-config field-checkbox" id="<?php echo esc_attr($field_id); ?>_hide_label" name="config[fields][<?php echo esc_attr($field_id); ?>][hide_label]" value="1" <?php if($hide_label === 1){ echo 'checked="checked"'; }else{?>{{#if hide_label}}checked="checked"{{/if}}<?php } ?>>
				</div>
			</div>

			<div class="caldera-config-group">
				<label for="<?php echo esc_attr($field_id); ?>_slug"><?php echo esc_html__( 'Slug', 'caldera-forms' ); ?></label>
				<div class="caldera-config-field">
					<input type="text" class="block-input field-config field-slug required" id="<?php echo esc_attr($field_id); ?>_slug" name="config[fields][<?php echo esc_attr($field_id); ?>][slug]" value="<?php echo $slug; ?>">
				</div>
			</div>
			<div id="field-condition-type-<?php echo esc_attr($field_id); ?>"></div>

			<div class="caldera-config-group required-field">
				<label for="<?php echo esc_attr($field_id); ?>_required"><?php echo esc_html__( 'Required', 'caldera-forms' ); ?></label>
				<div class="caldera-config-field">
					<input type="checkbox" class="field-config field-required field-checkbox" id="<?php echo esc_attr($field_id); ?>_required" name="config[fields][<?php echo esc_attr($field_id); ?>][required]" value="1" <?php if($required === 1){ echo 'checked="checked"'; }else{?>{{#if required}}checked="checked"{{/if}}<?php } ?>>
				</div>
			</div>

			<div class="caldera-config-group caption-field">
				<label for="<?php echo esc_attr($field_id); ?>_caption"><?php echo esc_html__( 'Description', 'caldera-forms' ); ?></label>
				<div class="caldera-config-field">
					<input type="text" class="block-input field-config" id="<?php echo esc_attr($field_id); ?>_caption" name="config[fields][<?php echo esc_attr($field_id); ?>][caption]" value="<?php echo esc_html( $caption ); ?>">
				</div>
			</div>
			
			<div class="caldera-config-group entrylist-field">
				<label for="<?php echo esc_attr($field_id); ?>_entry_list"><?php echo esc_html__( 'Show in Entry List', 'caldera-forms' ); ?></label>
				<div class="caldera-config-field">
					<input type="checkbox" class="field-config field-checkbox" id="<?php echo esc_attr($field_id); ?>_entry_list" name="config[fields][<?php echo esc_attr($field_id); ?>][entry_list]" value="1" <?php if($entry_list === 1){ echo 'checked="checked"'; }else{?>{{#if entry_list}}checked="checked"{{/if}}<?php } ?>>
				</div>
			</div>

			<?php
            /**
             * Runs in field wrapper template before field specific settings fields
             *
             * @since 1.6.1
             *
             * @param array $config Field config
             * @param string $type Field type
             * @param string $field_id Template representation of Field ID. Probably {{ID}}
             */
            do_action( 'caldera_forms_field_wrapper_before_field_setup', $config, $type, $field_id );
			?>

			<div class="caldera-config-field-setup">
			</div>

			<?php
			/**
			 * Runs in field wrapper template after field specific settings fields
			 *
			 * @since 1.6.1
			 *
			 * @param array $config Field config
			 * @param string $type Field type
             * @param string $field_id Template representation of Field ID. Probably {{ID}}
			 */
			do_action( 'caldera_forms_field_wrapper_after_field_setup', $config, $type, $field_id );
			?>

			<input type="hidden" class="field_config_string block-input" value="<?php echo htmlentities( $config_str ); ?>">
            <br>
			<?php
			/**
			 * Runs in field wrapper template before delete field button
			 *
			 * @since 1.6.2
			 *
			 * @param array $config Field config
			 * @param string $type Field type
			 * @param string $field_id Template representation of Field ID. Probably {{ID}}
			 */
			do_action( 'caldera_forms_field_wrapper_before_delete', $config, $type, $field_id );
			?>

			<button class="button delete-field block-button" data-confirm="<?php echo esc_html__( 'Are you sure you want to remove this field?. \'Cancel\' to stop. \'OK\' to delete', 'caldera-forms' ); ?>" type="button"><?php echo esc_html__( 'Delete Field', 'caldera-forms' ); ?></button>

            <?php
			/**
			 * Runs in field wrapper template after delete field button
			 *
			 * @since 1.6.2
			 *
			 * @param array $config Field config
			 * @param string $type Field type
			 * @param string $field_id Template representation of Field ID. Probably {{ID}}
			 */
			do_action( 'caldera_forms_field_wrapper_after_delete', $config, $type, $field_id );
			?>
        </div>

	</div>
	<?php
}

function build_field_types($default = null){
	global $field_type_list;
	

	$out = '';
	if(null === $default){
		$out .= '<option></option>';
	}

	foreach($field_type_list as $category=>$fields){

		$out .= "<optgroup label=\" ". $category . "\">\r\n";
		foreach ($fields as $field => $config) {

			$sel = "";
			if( $default === null ){
				$sel = "{{#is type value=\"" . $field . "\"}}selected=\"selected\"{{/is}}";
			}
			if($default == $field){
				$sel = 'selected="selected"';
			}

			$out .= "<option value=\"". $field . "\" ". $sel .">" . $config['field'] . "</option>\r\n";
		}
		$out .= "</optgroup>";
	}

	return $out;

}


function field_line_template($id = '{{id}}', $label = '{{label}}', $group = '{{group}}'){
	
	ob_start();

	?>
	<li data-field="<?php echo $id; ?>" class="caldera-field-line">
		<a href="#<?php echo $id; ?>">
			<i class="icn-right pull-right"></i>
			<i class="icn-field"></i>
			<?php echo htmlentities( $label ); ?>
		</a>
		<input type="hidden" class="caldera-config-field-group" value="<?php echo $group; ?>" name="config[fields][<?php echo $id; ?>][group]" autocomplete="off">
	</li>
	<?php

	return ob_get_clean();
}


// Navigation
?>
<div class="caldera-editor-header">
	<ul class="caldera-editor-header-nav">
		<li class="caldera-editor-logo">
			<span class="caldera-forms-name">Caldera Forms</span>
		</li>
		<li class="caldera-element-type-label">
			<?php echo $element['name']; ?>
		</li>
		<li>
			<a href="#settings-panel">
				<?php esc_html_e( 'Form Settings', 'caldera-forms'  ); ?>
			</a>
		</li>

	</ul>

	<div class="updated_notice_box">
		<?php esc_html_e( 'Updated Successfully', 'caldera-forms'  ); ?>
	</div>


	<?php if( ! Caldera_Forms_Admin::is_revision_edit() ){ ?>

		<div id="caldera-header-save-button"></div>

		<a class="button caldera-header-preview-button" target="_blank" href="<?php echo esc_url( Caldera_Forms_Admin::preview_link( $element[ 'ID' ] ) ); ?>">
			<?php esc_html_e( 'Preview Form', 'caldera-forms' ); ?>
		</a>
	<?php
	}else{ ?>

		<a
			href="<?php echo esc_url( Caldera_Forms_Admin::form_edit_link($element[ 'ID' ] )); ?>"
		    class="button caldera-header-return-button"
			id="caldera-forms-return-from-revision"
		>
			<?php esc_html_e( 'Exit Revision', 'caldera-forms' ); ?>
			
		</a>

		<a
			href="#"
		    class="button button-primary caldera-header-restore-button"
			id="caldera-forms-restore-revision"
			data-form="<?php echo esc_attr( $element[ 'ID' ] ); ?>"
			data-revision="<?php echo esc_attr( $element[ 'db_id' ] ); ?>"
		    data-edit-link="<?php echo esc_url( Caldera_Forms_Admin::form_edit_link($element[ 'ID' ] )); ?>"
		>
			<?php esc_html_e( 'Restore Form Revision', 'caldera-forms' ); ?>
			<span id="save_indicator" class="spinner" style="position: absolute; right: -33px;"></span>

		</a>

		<a class="button caldera-header-preview-button" target="_blank" href="<?php echo esc_url( Caldera_Forms_Admin::preview_link( $element[ 'ID' ], $element[ 'db_id' ] ) ); ?>">
			<?php esc_html_e( 'Preview Form Revision', 'caldera-forms' ); ?>
		</a>

	<?php } ?>



	<?php
	if ( !empty( $element['mailer']['preview_email'] ) ){
		$has_email_preview = 'aria-hidden="false" ';
	}else{
		$has_email_preview = 'aria-hidden="true" style="display:none;visibility:hidden;"';
	}
	?>
	<a class="button caldera-header-email-preview-button" target="_blank" href="<?php echo esc_url( add_query_arg( array(
			'cf-email-preview' => wp_create_nonce( $element[ 'ID' ] ),
			'cf-email-preview-form' => $element[ 'ID' ]
	),  get_home_url() ) ); ?>" <?php echo $has_email_preview; ?>>
		<?php esc_html_e( 'Preview Last Email', 'caldera-forms' ); ?>
	</a>
</div>

<?php include CFCORE_PATH  . 'ui/panels/form-settings.php'; ?>
	<div class="caldera-editor-header caldera-editor-subnav">
		<ul class="caldera-editor-header-nav">

		<?php
		// PANELS LOWER NAV

		foreach($panel_extensions as $panel_slug=>$panel){
			if(empty($panel['tabs'])){
				continue;
			}

			?>
					<?php
					// BUILD ELEMENT SETUP TABS
					if(!empty($panel['tabs'])){
						// PANEL BASED TABS
						foreach($panel['tabs'] as $group_slug=>$tab_setup){
							if($tab_setup['location'] !== 'lower'){
								continue;
							}

							$active = null;
							if(!empty($tab_setup['active'])){
								$active = " class=\"active\"";
							}
							echo "<li".$active." id=\"tab_".$group_slug."\"><a href=\"#" . $group_slug . "-config-panel\">" . $tab_setup['name'] . "</a></li>\r\n";
						}

						// CODE BASED TABS
						if(!empty($panel['tabs']['code'])){
							foreach($panel['tabs']['code'] as $code_slug=>$tab_setup){
								$active = null;
								if(!empty($tab_setup['active'])){
									$active = " class=\"active\"";
								}
								echo "<li".$active."><a href=\"#" . $code_slug . "-code-panel\" data-editor=\"" . $code_slug . "-editor\">" . $tab_setup['name'] . "</a></li>\r\n";
							}
						}

					}

					?>
			<?php
		}
		?>
		</ul>
	</div>
<?php

// PANEL WRAPPERS & RENDER
$repeatable_templates = array();
foreach($panel_extensions as $panel){
	if(empty($panel['tabs'])){
		continue;
	}

	foreach($panel['tabs'] as $panel_slug=>$tab_setup){
		$active = "  style=\"display:none;\"";
		if(!empty($tab_setup['active'])){
			$active = null;
		}
		echo "<div id=\"" . $panel_slug . "-config-panel\" class=\"caldera-editor-body caldera-config-editor-panel " . ( !empty($tab_setup['side_panel']) ? "caldera-config-has-side" : "" ) . "\"".$active.">\r\n";
			if( !empty($tab_setup['side_panel']) ){
				echo "<div id=\"" . $panel_slug . "-config-panel-main\" class=\"caldera-config-editor-main-panel\">\r\n";
			}
			echo '<h3>'.$tab_setup['label'];
				if( isset( $tab_setup[ 'tip' ] ) && is_array( $tab_setup[ 'tip' ] ) ) {
					printf( '<a href="%s" target="_blank" class="dashicons dashicons-editor-help caldera-forms-tab-help-bubble" data-toggle="tooltip" data-placement="top"  title="%s"><span class="screen-reader-text">%s</span></a>',
						esc_url( $tab_setup[ 'tip' ][ 'link' ] ),
						esc_attr( $tab_setup[ 'tip' ][ 'text'] ),
						esc_html__( 'Click to view help doc on CalderaForms.com' )
					);
				}
				if( !empty( $tab_setup['repeat'] ) ){
					// add a repeater button
					echo " <a href=\"#" . $panel_slug . "_tag\" class=\"add-new-h2 caldera-add-group\" data-group=\"" . $panel_slug . "\">" . esc_html__( 'Add New', 'caldera-forms' ) . "</a>\r\n";
				}
				// ADD ACTIONS
				if(!empty($tab_setup['actions'])){
					foreach($tab_setup['actions'] as $action){
						include $action;
					}
				}
			echo '</h3>';
			// BUILD CONFIG FIELDS
			if(!empty($tab_setup['fields'])){
				// group index for loops
				$depth = 1;
				if(isset($element['settings'][$panel_slug])){
					// find max depth
					foreach($element['settings'][$panel_slug] as &$field_vars){
						if(is_countable($field_vars) && count($field_vars) > $depth){
							$depth = count($field_vars);
						}
					}
				}
				for($group_index = 0; $group_index < $depth; $group_index++){
					
					if( !empty( $tab_setup['repeat'] ) ){
						echo "<div class=\"caldera-config-editor-panel-group\">\r\n";
					}
					foreach($tab_setup['fields'] as $field_slug=>&$field){
						$wrapper_before = "<div class=\"caldera-config-group\">";
						$field_before = "<div class=\"caldera-config-field\">";
						$field_after = '</div>';
						$wrapper_after = '</div>';
						$field_name = 'config[settings][' . $panel_slug . '][' . $field_slug . ']';
						$field_base_id = $field_id = $panel_slug. '_' . $field_slug . '_' . $group_index;						
						$field_label = "<label for=\"" . $field_id . "\">" . $field['label'] . "</label>\r\n";
						$field_placeholder = "";
						$field_required = "";
						if(!empty($field['hide_label'])){
							$field_label = "";
							$field_placeholder = 'placeholder="' . htmlentities( $field['label'] ) .'"';
						}


						$field_caption = null;
						if(!empty($field['caption'])){
							$field_caption = "<p class=\"description\">" . $field['caption'] . "</p>\r\n";
						}

						// blank default
						$field_value = null;

						if(isset($field['config']['default'])){
							$field_value = $field['config']['default'];
						}
						if(isset($element['settings'][$panel_slug][$field_slug])){
							$field_value = $element['settings'][$panel_slug][$field_slug];
						}

						$field_class = "field-config";
						if(!empty($field['required'])){
							$field_class .= " required";							
						}
						include $field_types[$field['type']]['file'];

					}
					if( !empty( $tab_setup['repeat'] ) ){
						echo "<a href=\"#remove_" . $panel_slug . "\" class=\"caldera-config-group-remove\">" . esc_html__( 'Remove', 'caldera-forms' ) . "</a>\r\n";
						echo "</div>\r\n";
					}
				}


				/// CHECK GROUP IS REPEATABLE ADN ADD A TEMPLATE IF IT IS
				if( !empty( $tab_setup['repeat'] ) ){

					$field_template = "<script type=\"text/html\" id=\"" . $panel_slug . "_panel_tmpl\">\r\n";
					$field_template .= "	<div class=\"caldera-config-editor-panel-group\">\r\n";

					foreach($tab_setup['fields'] as $field_slug=>&$field){
						
						$field_name = 'config[settings][' . $panel_slug . '][' . $field_slug . '][]';
						$field_id = $panel_slug. '_' . $field_slug;

						// blank default
						$field_value = null;

						if(isset($field['config']['default'])){
							$field_value = $field['config']['default'];
						}

						$field_template .= "	<div class=\"caldera-config-group\">\r\n";
							$field_template .= "		<label for=\"" . $field_id . "\">" . $field['label'] . "</label>\r\n";
							$field_template .= "		<div class=\"caldera-config-field\">\r\n";
								ob_start();
								include $field_types[$field['type']]['file'];
								$field_template .= ob_get_clean();
							$field_template .= "		</div>\r\n";
						$field_template .= "	</div>\r\n";

					}
					$field_template .= "	<a href=\"#remove-group\" class=\"caldera-config-group-remove\">" . esc_html__( 'Remove', 'caldera-forms' ) . "</a>\r\n";
					$field_template .= "	</div>\r\n";
					$field_template .= "</script>\r\n";

					$repeatable_templates[] = $field_template;

				}


			}elseif(!empty($tab_setup['canvas'])){
				include $tab_setup['canvas'];
			}

			if(!empty($tab_setup['side_panel'])){
				echo "</div>\r\n";
				echo "<div id=\"" . $panel_slug . "-config-panel-side\" class=\"caldera-config-editor-side-panel\">\r\n";

					include $tab_setup['side_panel'];

				echo "</div>\r\n";
			}

		echo "</div>\r\n";
	}
	echo "<a name=\"" . $panel_slug . "_tag\"></a>";
}

// PROCESSORS
do_action('caldera_forms_edit_end', $element);
?>
<script type="text/html" id="field-options-cofnig-tmpl">
<?php
	echo $field_options_template;
?>
</script>

<script type="text/html" id="form-fields-selector-tmpl">
	<div class="modal-tab-panel">
	<?php

		
		$sorted_field_types = array(
			__( 'Basic', 'caldera-forms' ) => '',
			__( 'Select', 'caldera-forms' ) => '',
			__( 'File', 'caldera-forms' ) => '',
			__( 'Content', 'caldera-forms' ) => '',
			__( 'eCommerce', 'caldera-forms' )  => '',
			__( 'Special', 'caldera-forms' ) => '',
			
		);

		if( defined( 'CFCORE_SHOW_DISCONTINUED_FIELDS' ) && CFCORE_SHOW_DISCONTINUED_FIELDS  ){
			$sorted_field_types[ __( 'Discontinued', 'caldera-forms' ) ] = '';
		}

		foreach($field_types as $field_slug=>$config){
			$cats[] = 'General';
			if(!empty($config['category'])){
				$cats = explode(',', $config['category']);
			}

			$svg = false;
			$icon = CFCORE_URL . "assets/images/field.png";
			if(!empty($config['icon'])){
				$icon = $config['icon'];
				if( false !== strpos( $icon, '.svg' ) ){
					$svg = true;
				}

			}
			foreach($cats as $cat){
				$cat = trim($cat);
				if(  __( 'Discontinued', 'caldera-forms' ) == $cat ){
					continue;
				}
				$template = '<div class="form-modal-add-line">';
					$template .= '<button type="button" class="button info-button set-current-field" data-field="{{id}}" data-type="' . $field_slug . '">' . esc_html__( 'Set Field', 'caldera-forms' ) . '</button>';
					$class = 'form-modal-lgo';
					if( $svg ){
						$class .= ' form-modal-lgo-svg';
					}
					$template .= '<img src="'. $icon .'" class="' . $class . '" width="45" height="45">';
					$template .= '<strong>' . $config['field'] . '</strong>';
					$template .= '<p class="description">' . (!empty($config['description']) ? esc_html__( $config[ 'description' ] ) : esc_html__( 'No description given', 'caldera-forms' ) ) . '</p>';
				$template .= '</div>';
				if(!isset($sorted_field_types[$cat])){
					$cat = __( 'Special', 'caldera-forms' );
				}
				$sorted_field_types[$cat] .= $template;
			}
		}

		$cat_show = false;

		foreach($sorted_field_types as $cat=>$template){
			if(!empty($cat_show)){
				$cat_show = 'style="display: none;"';
			}
			echo '<div id="modal-category-'. sanitize_key( $cat ) .'" data-tab="' . esc_attr( $cat ) . '" class="tab-detail-panel" '.$cat_show.'>';
				echo $template;
			echo '</div>';
			$cat_show = true;
		}

	?>
	</div>
</script>
<script type="text/html" id="caldera_field_config_wrapper_templ">
<?php
    caldera_forms_field_wrapper_template('{{id}}' );
?>
</script>
<script type="text/html" id="field-option-row-tmpl">
	{{#each option}}
	<div class="toggle_option_row 962">
		<i class="dashicons dashicons-sort option-group-control" style="padding: 4px 9px;"></i>

		<input type="radio" class="toggle_set_default field-config option-group-control" name="{{../_name}}[default]" value="{{@key}}" {{#is ../default value="@key"}}checked="checked"{{/is}}>

		<a href="https://calderaforms.com/doc/select-options/?utm_source=wp-admin&utm_medium=form-editor&utm_content=discount" target="_blank" class="dashicons dashicons-editor-help" style="float:right;" data-toggle="tooltip" data-placement="bottom"  title="<?php  esc_attr_e( 'Learn more about using select field options', 'caldera-forms'  ); ?>"></a>

		<div class="caldera-config-group">
			<label class="option-setting-label option-setting-label-for-value" for="opt-calc-val-{{@key}}">
				<?php esc_html_e( 'Calculation Value', 'caldera-forms' ); ?>
			</label>
			<input
				type="text"
				class="toggle_calc_value_field field-config option-setting "
				name="{{../_name}}[option][{{@key}}][calc_value]"
                data-field="{{../_name}}"
				value="{{calc_value}}"
				placeholder="<?php esc_attr_e( 'Calculation Value', 'caldera-forms'  ); ?>"
				id="opt-calc-val-{{@key}}"
				{{#unless ../show_values}} style="display:none;"{{/unless}}
			/>
		</div>

		<div class="caldera-config-group">
			<label class="option-setting-label option-setting-label-for-value" for="opt-val-{{@key}}">
				<?php esc_html_e( 'Value', 'caldera-forms' ); ?>
			</label>
			<input
				type="text"
				class="toggle_value_field option-setting field-config  required "
				name="{{../_name}}[option][{{@key}}][value]"
				value="{{value}}"
				placeholder="<?php esc_attr_e( 'Value', 'caldera-forms' ); ?>"
				id="opt-val-{{@key}}"
				data-opt="{{@key}}"
				{{#unless ../show_values}} style="display:none;"{{/unless}}
			/>
		</div>

		<div class="caldera-config-group">
			<label class="option-setting-label option-setting-label-for-label" for="opt-label-{{@key}}">
				<?php esc_html_e( 'Label', 'caldera-forms' ); ?>
			</label>
			<input
				type="text"
				data-option="{{@key}}"
				class="toggle_label_field option-setting field-config required"
				name="{{../_name}}[option][{{@key}}][label]" value="{{label}}"
				placeholder="<?php esc_attr_e( 'Label', 'caldera-forms' ); ?>"
				for="opt-label-{{@key}}"
				data-opt="{{@key}}"
				{{#unless ../show_values}} style="width:245px;"{{/unless}}
			/>
		</div>

		<button class="button button-small toggle-remove-option" type="button">
			<i class="icn-delete"></i>
		</button>
	</div>
	{{/each}}
</script>
<script type="text/html" id="noconfig_field_templ" class="cf-editor-template">
<div class="caldera-config-group">
	<label><?php _e( 'Default', 'caldera-forms' ); ?></label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config" name="{{_name}}[default]" value="{{default}}">
	</div>
</div>
</script>

<?php

/// Output the field templates
foreach($field_type_templates as $key=>$template){
	echo "<script type=\"text/html\" class=\"cf-editor-template\" id=\"" . $key . "\">\r\n";
		echo $template;
	echo "\r\n</script>\r\n";
}
?>

<?php


$magic_script = array(
	'field' => array()
);

foreach($magic_tags as $magic_set_key=>$magic_tags_set){

	$magic_script[$magic_set_key] = array(
		'type'	=>	$magic_tags_set['type'],
		'tags'	=>	array(),
		'wrap'	=>  $magic_tags_set['wrap']
	);

	foreach($magic_tags_set['tags'] as $tag_key=>$tag_value){

		if(is_array($tag_value)){
			foreach($tag_value as $compatibility){
				$magic_script[$magic_set_key]['tags'][$compatibility][] = $tag_key;
			}
		}else{
			$magic_script[$magic_set_key]['tags']['text'][] = $tag_value;
		}
	}

}

?>
<script type="text/javascript">

<?php
// output fieldtype defaults
echo implode("\r\n", $field_type_defaults);
?>
var system_values = <?php echo json_encode( $magic_script ); ?>;
var preset_options = <?php echo json_encode( $option_presets ); ?>;

</script>

<script type="text/javascript">
	jQuery('.error,.notice,.notice-error').remove();
</script>


<?php
/**
 * Runs at the bottom of the Caldera Forms form editor page
 *
 * @since 1.6.0
 */
do_action('caldera_forms_editor_footer');
