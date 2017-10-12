<?php

global $field_config_panels;

// TAKE IN THE GRID

if(!isset($element['layout_grid']['structure'])){
	$element['layout_grid']['structure'] = '12';
}
//dump($element);
$element['layout_grid']['structure'] = explode("#", $element['layout_grid']['structure']);

echo "<span id=\"row-remove-fields-message\" class=\"hidden\">" . __('This will remove all the fields in this row. Are you sure?', 'caldera-forms') . "</span>";
echo '<div class="toggle_option_tab" data-title="'.__('Page', 'caldera-forms').'" style="float:none;' . ( count($element['layout_grid']['structure']) === 1 ? ' display:none;' : '' ) . '" id="page-toggles">';
$pgid = uniqid('pg');
if(count($element['layout_grid']['structure']) >= 1){
	for($i = 1; $i<=count($element['layout_grid']['structure']) ; $i++){
		$name = __('Page', 'caldera-forms') . ' ' . $i;
		if(isset($element['page_names'][($i-1)])){
			$name = htmlspecialchars($element['page_names'][($i-1)]);
		}
		echo '<button type="button" data-page="' . $pgid . $i .'" data-name="' . $name . '" class="page-toggle button' . ( $i === 1 ? ' button-primary' : '' ) . '">' . __('Page', 'caldera-forms') . ' ' . $i . '</button> ';
	
	}	
}
echo '</div>';
echo "<div id=\"grid-pages-panel\">\r\n";

// BUILD TEMPLATE LOCATIONS
$fields = array();
if(!empty($element['layout_grid']['fields'])){
	foreach($element['layout_grid']['fields'] as $field_id=>$location){

		if(isset($element['fields'][$field_id])){
			$field = $element['fields'][$field_id];
			$field['ID'] = $field_id;

			$location = explode(':', $location);
			$fields[$location[0]][$location[1]][] = $field;
			$config_str = (!empty($field['config']) ? json_encode($field['config']) : null);
			$conditions = '{}';
			if(!empty($field['conditions'])){
				$conditions = json_encode($field['conditions']);
			}			
			// build config
			ob_start();

			field_wrapper_template( $field_id, $field['label'], $field['slug'], $field['caption'], ( isset($field['hide_label']) ? 1 : 0 ), ( isset($field['required']) ? 1 : 0 ),( isset($field['entry_list']) ? 1 : 0 ), $field['type'], $config_str, $conditions);
			$field_config_panels[] = ob_get_clean();
		}

	}
}

$row = 0;
foreach( (array) $element['layout_grid']['structure'] as $page_key=>$page_struct){
	if(!empty($page_struct)){
		$rows = explode("|", $page_struct);
	}else{
		$rows = array('12');
	}
	?>
	<div class="layout-grid-panel layout-grid <?php echo ( $page_key === 0 ? 'page-active' : null ); ?>" id="<?php echo $pgid.($page_key+1); ?>" data-page="<?php echo $page_key; ?>" <?php echo ( $page_key > 0 ? ' style="display:none;"' : null ); ?>>
		<?php
		if(!empty($rows)){
		foreach($rows as $row_in=>$columns){ ?>
		<div class="first-row-level row">
			<?php

			$row += 1;

			$columns = explode(':', $columns);
			foreach($columns as $column=>$span){
				$column += 1;
			?>
			<div class="col-xs-<?php echo $span; ?>">
				<div class="layout-column column-container">
				<?php

					//render fields here
					if(isset($fields[$row][$column])){
						foreach($fields[$row][$column] as $field){
						?>
						<div class="layout-form-field" data-config="<?php echo $field['ID']; ?>">
							<i style="display:none;" class="icon-edit"></i>
							<i style="display:none;" class="dashicons dashicons-admin-page"></i>
							<div class="drag-handle">
								<div class="field_preview"><span class="spinner" style="display: block; float: left;"></span></div>
							</div>
							<input type="hidden" class="field-location" value="<?php echo $row.':'.$column; ?>" name="config[layout_grid][fields][<?php echo $field['ID']; ?>]">
						</div>
						<?php
						}
					}

				?>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php }} ?>
		<!-- Build the grid -->
		<input type="hidden" id="page_structure_<?php echo $page_key; ?>" name="config[layout_grid][structure][]" class="layout-structure" value="">
	</div>	
<?php } ?>
</div>
<div class="add-new-item caldera-add-group caldera-add-row"><span class="dashicons dashicons-plus" data-toggle="tooltip" data-placement="bottom" title="<?php esc_attr_e( 'Click to add a new row to layout.', 'caldera-forms' ); ?>"></span></div>
<?php do_action( 'caldera_forms_layout_config', $element ); ?>
<script type="text/html" id="grid-page-tmpl">
	<div class="layout-grid-panel layout-grid" data-page="{{page_no}}" data-name="<?php echo __('Page', 'caldera-forms'); ?> {{count}}" style="display:none;" id="{{page_no}}">
		<div class="first-row-level row">
			<div class="col-xs-12">
				<div class="layout-column column-container">
				</div>
			</div>
		</div>
		<input type="hidden" id="page_structure_{{page_no}}" name="config[layout_grid][structure][]" class="layout-structure" value="12">
	</div>
</script>
