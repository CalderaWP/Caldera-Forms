<?php

global $field_config_panels;

// TAKE IN THE GRID

if(!empty($element['layout_grid']['structure'])){
	$rows = explode("|", $element['layout_grid']['structure']);
}else{
	$rows = array('12');
}

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


?>
<span id="row-remove-fields-message" class="hidden"><?php echo __('This will remove all the fields in this row. Are you sure?', 'caldera-forms'); ?></span>
<div class="layout-grid-panel layout-grid">
	<?php
	if(!empty($rows)){
	foreach($rows as $row=>$columns){ ?>
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
</div>
<input type="hidden" name="config[layout_grid][structure]" class="layout-structure" value="">