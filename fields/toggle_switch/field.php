<?php

$selectedClassName = 'btn-success';
if(!empty($field['config']['selected_class'])){
	$selectedClassName = $field['config']['selected_class'];
}

$defaultClassName = 'btn-default';
if(!empty($field['config']['default_class'])){
	$defaultClassName = $field['config']['default_class'];
}

$groupOrientation = 'btn-group';
if(!empty($field['config']['orientation']) && $field['config']['orientation'] == 'justified'){
	$groupOrientation = 'btn-group btn-group-justified';
}elseif(!empty($field['config']['orientation']) && $field['config']['orientation'] == 'vertical'){
	$groupOrientation = 'btn-group-vertical';
}


?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
	<div class="caldera-config-field init_field_type" data-type="toggle_button">
		<div class="cf-toggle-group-buttons <?php echo $groupOrientation; ?>">
			<?php

			if(isset( $field['config'] ) && isset($field['config']['default']) && isset($field['config']['option'][$field['config']['default']])){
				//if( $field['config']['option'][$field['config']['default']]['value'] )
				if( $field['config']['default'] === $field_value ){
					$field_value = $field['config']['option'][$field['config']['default']]['value'];
				}

			}


			if(empty($field['config']['option'])){ ?>
					
					<a id="<?php echo $field_id; ?>_1" class="button" data-value="true"><?php echo __('Enable', 'caldera-forms'); ?></a>

			<?php }else{
				foreach($field['config']['option'] as $option_key=>$option){
				if(!isset($option['value'])){
					$option['value'] = htmlspecialchars( $option['label'] );
				}
				$selclass = $defaultClassName;
				if($field_value == $option['value']){
					$selclass = $selectedClassName;
				}

					?><a id="<?php echo $field_id.'_'.$option_key; ?>" data-label="<?php echo esc_attr( $option['label'] );?>" data-field="<?php echo $field_base_id; ?>" data-active="<?php echo $selectedClassName; ?>" data-default="<?php echo $defaultClassName; ?>" class="btn <?php echo $selclass; ?>" data-value="<?php echo $option['value']; ?>"><?php echo $option['label']; ?></a><?php
				}
			} ?>		
		</div>
		<div style="display:none;">
		<?php
		if(!empty($field['config']['option'])){
			foreach($field['config']['option'] as $option_key=>$option){
				if(!isset($option['value'])){
					$option['value'] = htmlspecialchars( $option['label'] );
				}
				$sel = '';
				if($field_value == $option['value']){
					$sel = 'checked="checked"';
				}
				?>
				<input type="radio" id="<?php echo $field_id . '_' . $option_key; ?>" data-label="<?php echo esc_attr( $option['label'] );?>" data-field="<?php echo $field_base_id; ?>" data-ref="<?php echo $field_id.'_'.$option_key; ?>" class="cf-toggle-group-radio <?php echo $field_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo $option['value'] ?>" <?php echo $sel; ?>>
				<?php
			}
		}
		?></div>
		<?php echo $field_caption; ?>
	</div>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>

<script>
jQuery( function( $ ){ 
	$(document).on('reset', '.<?php echo $form['ID']; ?>', function(e){
		$('a[data-field="<?php echo $field_base_id; ?>"]').removeClass('<?php echo $selectedClassName; ?>').addClass('<?php echo $defaultClassName; ?>');
		$('input[data-field="<?php echo $field_base_id; ?>"]').prop('checked','');
	});
});
</script>