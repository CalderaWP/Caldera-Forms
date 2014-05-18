<?php

global $field_type_list, $field_type_templates;

// Load element
$element = get_option( $_GET['edit'] );
//dump($element);

// place nonce field
wp_nonce_field( 'cf_edit_element', 'cf_edit_nonce' );

// Init check
echo "<input name=\"config[_last_updated]\" value=\"" . date('r') . "\" type=\"hidden\">";
echo "<input name=\"config[ID]\" value=\"" . $_GET['edit'] . "\" type=\"hidden\">";

// Get Fieldtpyes
$field_types = apply_filters('caldera_forms_get_field_types', array() );
// sort fields
ksort($field_types);

// Get Elements
$panel_extensions = apply_filters('caldera_forms_get_panel_extensions', array() );


$field_type_list = array();
$field_type_templates = array();
$field_type_defaults = array(
	"var fieldtype_defaults = {};"
);

// Build Field Types List
foreach($field_types as $field=>$config){

	if(!file_exists($config['file'])){
		continue;
	}
	// type list
	$field_type_list[$field] = $config;

	if(!empty($config['setup']['template'])){
		if(file_exists( $config['setup']['template'] )){
			// create config template block
			
				ob_start();
					include $config['setup']['template'];
				$field_type_templates[sanitize_key( $field ) . "_tmpl"] = ob_get_clean();

				
		}
	}
	
	if(!empty($config['setup']['default'])){
		$field_type_defaults[] = "fieldtype_defaults." . sanitize_key( $field ) . "_cfg = " . json_encode($config['setup']['default']) .";";
	}
	if(!empty($config['setup']['not_supported'])){
		$field_type_defaults[] = "fieldtype_defaults." . sanitize_key( $field ) . "_nosupport = " . json_encode($config['setup']['not_supported']) .";";
	}

}




function field_wrapper_template($id = '{{id}}', $label = '{{label}}', $slug = '{{slug}}', $caption = '{{caption}}', $hide_label = '{{hide_label}}', $type = null, $config_str = '{"default":"default value"}'){

	if(is_array($config_str)){
		$config 	= $config_str;
		$config_str = json_encode( $config_str );

	}else{
		$config = json_decode($config_str, true);
	}

	?>
	<div class="caldera-editor-field-config-wrapper" id="<?php echo $id; ?>" style="display:none;">
		<button class="button button-small pull-right delete-field" type="button"><i class="icn-delete"></i></button>
		<h3 class="caldera-editor-field-title"><?php echo $label; ?>&nbsp;</h3>
		<div class="caldera-config-group">
			<label for="<?php echo $id; ?>_type"><?php echo __('Field Type', 'caldera-forms'); ?></label>
			<div class="caldera-config-field">
				<select class="block-input caldera-select-field-type required" id="<?php echo $id; ?>_type" name="config[fields][<?php echo $id; ?>][type]" data-type="<?php echo $type; ?>">					
					<?php
					echo build_field_types($type);
					?>
				</select>
			</div>
		</div>		
		<div class="caldera-config-group">
			<label for="<?php echo $id; ?>_lable"><?php echo __('Label', 'caldera-forms'); ?></label>
			<div class="caldera-config-field">
				<input type="text" class="block-input field-config field-label required" id="<?php echo $id; ?>_lable" name="config[fields][<?php echo $id; ?>][label]" value="<?php echo sanitize_text_field( $label ); ?>">
			</div>
		</div>

		<div class="caldera-config-group hide-label-field">
			<label for="<?php echo $id; ?>_hide_label"><?php echo __('Hide Label', 'caldera-forms'); ?></label>
			<div class="caldera-config-field">
				<input type="checkbox" class="field-config field-checkbox" id="<?php echo $id; ?>_hide_label" name="config[fields][<?php echo $id; ?>][hide_label]" value="1" <?php if($hide_label === 1){ echo 'checked="checked"'; }; ?>>
			</div>
		</div>

		<div class="caldera-config-group">
			<label for="<?php echo $id; ?>_slug"><?php echo __('Slug', 'caldera-forms'); ?></label>
			<div class="caldera-config-field">
				<input type="text" class="block-input field-config field-slug required" id="<?php echo $id; ?>_slug" name="config[fields][<?php echo $id; ?>][slug]" value="<?php echo $slug; ?>">
			</div>
		</div>

		<div class="caldera-config-group caption-field">
			<label for="<?php echo $id; ?>_caption"><?php echo __('Caption', 'caldera-forms'); ?></label>
			<div class="caldera-config-field">
				<input type="text" class="block-input field-config" id="<?php echo $id; ?>_caption" name="config[fields][<?php echo $id; ?>][caption]" value="<?php echo sanitize_text_field( $caption ); ?>">
			</div>
		</div>
		<div class="caldera-config-field-setup">
		</div>
		<input type="hidden" class="field_config_string block-input" value="<?php echo htmlentities( $config_str ); ?>">
	</div>
	<?php
}

function build_field_types($default = null){
	global $field_type_list;
	
	$out = '';
	if(null === $default){
		$out .= '<option></option>';
	}
	foreach($field_type_list as $field=>$config){
		$sel = "";
		if($default == $field){
			$sel = 'selected="selected"';
		}
		$out .= "<option value=\"". $field . "\" ". $sel .">" . $config['field'] . "</option>\r\n";

	}

	return $out;

}

function group_line_template($id = '{{id}}', $name = '{{name}}', $repeat = '0', $admin = '0', $desc = null){
	$icon = 'icn-folder';
	if(!empty($repeat)){
		$icon = 'icn-repeat';
	}
	$adminclass= null;
	if(!empty($admin)){
		$adminclass = 'is-admin';
	}
	ob_start();
	?>
	<li data-group="<?php echo $id; ?>" class="caldera-group-nav <?php echo $adminclass; ?>">
		<a href="#<?php echo $id; ?>">
		<i class="icn-right pull-right"></i>
		<i class="group-type <?php echo $icon; ?>"></i> <span><?php echo $name; ?></span></a>
		<input type="hidden" class="caldera-config-group-name" value="<?php echo $name; ?>" name="config[groups][<?php echo $id; ?>][name]" autocomplete="off">
		<input type="hidden" class="caldera-config-group-slug" value="<?php echo $id; ?>" name="config[groups][<?php echo $id; ?>][slug]" autocomplete="off">
		<input type="hidden" class="caldera-config-group-repeat" value="<?php echo $repeat; ?>" name="config[groups][<?php echo $id; ?>][repeat]" autocomplete="off">
		<input type="hidden" class="caldera-config-group-admin" value="<?php echo $admin; ?>" name="config[groups][<?php echo $id; ?>][admin]" autocomplete="off">
		<input type="hidden" class="caldera-config-group-desc" value="<?php echo $desc; ?>" name="config[groups][<?php echo $id; ?>][desc]" autocomplete="off">

	</li>
	<?php

	return ob_get_clean();
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
			<?php echo __('Caldera Forms', 'caldera-forms'); ?>
		</li>
		<li class="caldera-element-type-label">
			<?php echo $element['name']; ?>
		</li>
		<li>
			<a href="#settings-panel"><?php echo __("General Settings", "caldera-forms"); ?></a>
		</li>
	</ul>
	<button class="button caldera-header-save-button" type="submit"><?php echo __('Save & Close', 'caldera-forms'); ?></button>

</div>

<div style="display: none;" class="caldera-editor-body caldera-config-editor-panel " id="settings-panel">
	<h3><?php echo __("General Settings", "caldera-forms"); ?></h3>
	<div class="caldera-config-group">
		<label><?php echo __('Form Name', 'caldera-forms'); ?> </label>
		<div class="caldera-config-field">
			<input type="text" class="field-config required" name="config[name]" value="<?php echo $element['name']; ?>" style="width:300px;" required="required">
		</div>
	</div>
	<div class="caldera-config-group">
		<label><?php echo __('Form Description', 'caldera-forms'); ?> </label>
		<div class="caldera-config-field">
			<textarea name="config[description]" class="field-config" style="width:300px;" rows="5"><?php echo htmlentities( $element['description'] ); ?></textarea>
		</div>
	</div>
</div>

<?php
// PANELS LOWER NAV

foreach($panel_extensions as $panel_slug=>$panel){
	if(empty($panel['tabs'])){
		continue;
	}

	?>
	<div class="caldera-editor-header caldera-editor-subnav">
		<ul class="caldera-editor-header-nav">
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
					echo "<li".$active."><a href=\"#" . $group_slug . "-config-panel\">" . $tab_setup['name'] . "</a></li>\r\n";
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
		</ul>
	</div>
	<?php
}


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

?>


<script type="text/html" id="caldera_field_config_wrapper_templ">
<?php
	echo field_wrapper_template();
?>
</script>
<script type="text/html" id="noconfig_field_templ">
<div class="caldera-config-group">
	<label>Default</label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config" name="{{_name}}[default]" value="{{default}}">
	</div>
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





































































