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
			// build config
			ob_start();
			field_wrapper_template( $field_id, $field['label'], $field['slug'], $field['caption'], ( isset($field['hide_label']) ? 1 : 0 ), $field['type'], json_encode($field['config']) );
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
					<div class="button layout-form-field" data-config="<?php echo $field['ID']; ?>">
						<i style="float: right; padding: 7px 0px 0px; display:none;" class="icon-edit"></i>
						<div class="drag-handle">
							<i class="icon-forms"></i>&nbsp;&nbsp;<span class="layout_field_name"><?php echo $field['label']; ?></span>
						</div><input type="hidden" class="field-location" value="<?php echo $row.':'.$column; ?>" name="config[layout_grid][fields][<?php echo $field['ID']; ?>]">
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