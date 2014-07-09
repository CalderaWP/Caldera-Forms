<?php

global $field_type_list, $field_type_templates, $wpdb;

// GET ENTRY DETAILS
$entry = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `id` = %d;", $_GET['edit-entry']));

$element = get_option( $entry->form_id );
if(empty($element)){
	wp_die( __('Invalid Entry', 'caldera-forms') );
}
// Navigation
?>
<div class="caldera-editor-header">
	<ul class="caldera-editor-header-nav">
		<li class="caldera-editor-logo">
			<?php echo __('Caldera Forms', 'caldera-forms'); ?>
		</li>
		<li class="caldera-element-type-label">
			<?php echo $element['name']; ?>
		</li>
		<li class="caldera-element-type-label">
			<?php echo __('Entry', 'caldera-forms') .' : '. $entry->id; ?>
		</li>

	</ul>
	<button class="button button-primary caldera-header-save-button" type="submit"><?php echo __('Update Form', 'caldera-forms'); ?><span id="save_indicator" class="spinner" style="position: absolute; right: -30px;"></span></button>	
</div>

<div class="caldera-editor-header caldera-editor-subnav">
	<ul class="caldera-editor-header-nav">
		<li class="sub-meta-line"><?php echo __('Submitted', 'caldera-forms'); ?>: <?php echo date_i18n( get_option('date_format') . ' @ ' . get_option( 'time_format', $entry->datestamp) ); ?></li>
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
				if( !empty( $tab_setup['repeat'] ) ){
					// add a repeater button
					echo " <a href=\"#" . $panel_slug . "_tag\" class=\"add-new-h2 caldera-add-group\" data-group=\"" . $panel_slug . "\">" . __('Add New', 'pods-caldera') . "</a>\r\n";
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
						if(count($field_vars) > $depth){
							$depth = count($field_vars);
						}
					}
				}
				for($group_index = 0; $group_index < $depth; $group_index++){
					
					if( !empty( $tab_setup['repeat'] ) ){
						echo "<div class=\"caldera-config-editor-panel-group\">\r\n";
					}
					foreach($tab_setup['fields'] as $field_slug=>&$field){
						
						$field_name = 'config[settings][' . $panel_slug . '][' . $field_slug . ']';
						$field_id = $panel_slug. '_' . $field_slug . '_' . $group_index;
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
						
						$field_wrapper_class = "caldera-config-group";
						$field_input_class = "caldera-config-field";
						$field_class = "field-config";
						if(!empty($field['required'])){
							$field_class .= " required";							
						}
						include $field_types[$field['type']]['file'];

					}
					if( !empty( $tab_setup['repeat'] ) ){
						echo "<a href=\"#remove_" . $panel_slug . "\" class=\"caldera-config-group-remove\">" . __('Remove', 'pods-caldera') . "</a>\r\n";
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
					$field_template .= "	<a href=\"#remove-group\" class=\"caldera-config-group-remove\">" . __('Remove', 'pods-caldera') . "</a>\r\n";
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

		
		$sorted_field_types = array();
		ksort($field_types);
		foreach($field_types as $field_slug=>$config){
			$cats[] = 'General';
			if(!empty($config['category'])){
				$cats = explode(',', $config['category']);
			}

			$icon = CFCORE_URL . "/assets/images/field.png";
			if(!empty($config['icon'])){
				$icon = $config['icon'];
			}
			foreach($cats as $cat){
				$cat = trim($cat);
				$template = '<div class="form-modal-add-line">';
					$template .= '<button type="button" class="button info-button set-current-field" data-field="{{id}}" data-type="' . $field_slug . '">' . __('Set Element', 'caldera-forms') . '</button>';
					$template .= '<img src="'. $icon .'" class="form-modal-lgo" width="45" height="45">';
					$template .= '<strong>' . $config['field'] . '</strong>';
					$template .= '<p class="description">' . (!empty($config['description']) ? $config['description'] : __('No description given', 'caldera-forms') ) . '</p>';
				$template .= '</div>';
				if(!isset($sorted_field_types[$cat])){
					$sorted_field_types[$cat] = null;
				}
				$sorted_field_types[$cat] .= $template;
			}
		}
		ksort($sorted_field_types);
		echo '<div class="modal-side-bar">';
			echo '<ul class="modal-side-tabs">';
			foreach($sorted_field_types as $cat=>$template){
				
				if(!isset($cat_class)){
					$cat_class = ' active';
				}
				echo "<li><a href=\"#modal-category-". sanitize_key( $cat ) ."\" class=\"modal-side-tab". $cat_class ."\">" . $cat . "</a></li>\r\n";
				$cat_class = '';
			}
		echo "</ul>\r\n";
		echo "</div>\r\n";
		$cat_show = false;
		foreach($sorted_field_types as $cat=>$template){
			if(!empty($cat_show)){
				$cat_show = 'style="display: none;"';
			}
			echo '<div id="modal-category-'. sanitize_key( $cat ) .'" class="tab-detail-panel" '.$cat_show.'>';
				echo $template;
			echo '</div>';
			$cat_show = true;
		}

	?>
	</div>
</script>
<script type="text/html" id="caldera_field_config_wrapper_templ">
<?php
	echo field_wrapper_template();
?>
</script>
<script type="text/html" id="field-option-row-tmpl">
	{{#each option}}
	<div class="toggle_option_row">
		<i class="dashicons dashicons-sort" style="padding: 4px 9px;"></i>
		<input type="radio" class="toggle_set_default field-config" name="{{../_name}}[default]" value="{{@key}}" {{#is ../default value="@key"}}checked="checked"{{/is}}>
		<input type="text" class="toggle_value_field field-config" name="{{../_name}}[option][{{@key}}][value]" value="{{value}}" placeholder="value">
		<input type="text" class="toggle_label_field field-config" name="{{../_name}}[option][{{@key}}][label]" value="{{label}}" placeholder="label">
		<button class="button button-small toggle-remove-option" type="button"><i class="icn-delete"></i></button>		
	</div>
	{{/each}}
</script>
<script type="text/html" id="noconfig_field_templ">
<div class="caldera-config-group">
	<label>Default</label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config" name="{{_name}}[default]" value="{{default}}">
	</div>
</div>
</script>
<script type="text/html" id="conditional-group-tmpl">	
	{{#each group}}
		<div class="caldera-condition-group">
			<div class="caldera-condition-group-label"><?php echo __('or', 'caldera-forms'); ?></div>			
			<div class="caldera-condition-lines" id="{{id}}_conditions_lines">
				{{#each lines}}
				<div class="caldera-condition-line">
					if 
					<select name="config[{{../type}}][{{../../id}}][conditions][group][{{../id}}][{{id}}][field]" data-condition="{{../type}}" class="caldera-processor-field-bind caldera-conditional-field-set" data-id="{{../../id}}" data-default="{{field}}" data-line="{{id}}" data-row="{{../id}}" data-all="true" style="max-width:120px;"></select>
					<select name="config[{{../type}}][{{../../id}}][conditions][group][{{../id}}][{{id}}][compare]" style="max-width:110px;">
						<option value="is" {{#is compare value="is"}}selected="selected"{{/is}}><?php echo __('is', 'caldera-forms'); ?></option>
						<option value="isnot" {{#is compare value="isnot"}}selected="selected"{{/is}}><?php echo __('is not', 'caldera-forms'); ?></option>
						<option value=">" {{#is compare value=">"}}selected="selected"{{/is}}><?php echo __('is greater than', 'caldera-forms'); ?></option>
						<option value="<" {{#is compare value="<"}}selected="selected"{{/is}}><?php echo __('is less than', 'caldera-forms'); ?></option>
						<option value="startswith" {{#is compare value="startswith"}}selected="selected"{{/is}}><?php echo __('starts with', 'caldera-forms'); ?></option>
						<option value="endswith" {{#is compare value="endswith"}}selected="selected"{{/is}}><?php echo __('ends with', 'caldera-forms'); ?></option>
						<option value="contains" {{#is compare value="contains"}}selected="selected"{{/is}}><?php echo __('contains', 'caldera-forms'); ?></option>
					</select>
					<span class="caldera-conditional-field-value" data-value="{{value}}" id="{{id}}_value"><input disabled type="text" value="" placeholder="<?php echo __('Select field first', 'caldera-forms'); ?>" style="max-width: 165px;"></span>
					<button type="button" class="button remove-conditional-line pull-right"><i class="icon-join"></i></button>
				</div>
				{{/each}}
			</div>
			<button type="button" class="button button-small ajax-trigger" data-id="{{../id}}" data-type="{{type}}" data-group="{{id}}" data-request="new_conditional_line" data-target="#{{id}}_conditions_lines" data-callback="rebuild_field_binding" data-template="#conditional-line-tmpl" data-target-insert="append"><?php echo __('Add Condition', 'caldera-forms'); ?></button>
		</div>
	{{/each}}
</script>
<script type="text/html" id="conditional-line-tmpl">
	<div class="caldera-condition-line">
		<div class="caldera-condition-line-label"><?php echo __('and', 'caldera-forms'); ?></div>
		if 
		<select name="{{name}}[field]" class="caldera-processor-field-bind caldera-conditional-field-set" data-condition="{{type}}" data-id="{{id}}" data-line="{{lineid}}" data-row="{{rowid}}" data-all="true" style="max-width:120px;"></select>
		<select name="{{name}}[compare]" style="max-width:110px;">
			<option value="is"><?php echo __('is', 'caldera-forms'); ?></option>
			<option value="isnot"><?php echo __('is not', 'caldera-forms'); ?></option>
			<option value=">"><?php echo __('is greater than', 'caldera-forms'); ?></option>
			<option value="<"><?php echo __('is less than', 'caldera-forms'); ?></option>
			<option value="startswith"><?php echo __('starts with', 'caldera-forms'); ?></option>
			<option value="endswith"><?php echo __('ends with', 'caldera-forms'); ?></option>
			<option value="contains"><?php echo __('contains', 'caldera-forms'); ?></option>
		</select>
		<span class="caldera-conditional-field-value" id="{{lineid}}_value"><input disabled type="text" value="" placeholder="<?php echo __('Select field first', 'caldera-forms'); ?>" style="max-width: 165px;"></span>
		<button type="button" class="button remove-conditional-line pull-right"><i class="icon-join"></i></button>
	</div>
</script>
<?php

/// Output the field templates
foreach($field_type_templates as $key=>$template){
	echo "<script type=\"text/html\" id=\"" . $key . "\">\r\n";
		echo $template;
	echo "\r\n</script>\r\n";
}
?>
<script type="text/javascript">

<?php
// output fieldtype defaults
echo implode("\r\n", $field_type_defaults);

?>
</script>





































































