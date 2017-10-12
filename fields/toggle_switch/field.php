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

			$field_value = Caldera_Forms_Field_Util::find_select_field_value( $field, $field_value );
			if(empty($field['config']['option'])){ ?>
					
					<a id="<?php echo esc_attr( $field_id ); ?>_1" class="button" data-value="true" <?php echo $field_structure['aria']; ?>><?php  esc_html_e('Enable', 'caldera-forms'); ?></a>

			<?php }else{
				foreach($field['config']['option'] as $option_key=>$option){
				if(!isset($option['value'])){
					$option['value'] = $option['label'];
				}
				$selclass = $defaultClassName;
				if($field_value === $option['value']){
					$selclass = $selectedClassName;
				}

					?><a id="<?php echo esc_attr(Caldera_Forms_Field_Util::opt_id_attr( $field_id, $option_key ) ); ?>" data-label="<?php echo esc_attr( $option['label'] );?>" data-field="<?php echo esc_attr( $field_base_id ); ?>" data-active="<?php echo $selectedClassName; ?>" data-default="<?php echo $defaultClassName; ?>" class="btn <?php echo $selclass; ?>" data-value="<?php echo esc_attr( $option['value'] ); ?>" <?php echo $field_structure['aria']; ?> title="<?php echo esc_attr( __( 'Choose Option', 'caldera-forms' ) .  $option['label']  ); ?>"><?php echo $option['label']; ?></a><?php
				}
			} ?>		
		</div>
		<div style="display:none;" aria-hidden="true">
		<?php
		if(!empty($field['config']['option'])){
			foreach($field['config']['option'] as $option_key=>$option){
				if(!isset($option['value'])){
					$option['value'] = $option['label'];
				}
				$sel = '';
				if($field_value === $option['value']){
					$sel = 'checked="checked"';
				}
				?>
				<input <?php if( !empty( $field['required'] ) ){ ?>required="required"<?php } ?> type="radio" id="<?php echo esc_attr(Caldera_Forms_Field_Util::opt_id_attr( $field_id, $option_key ) ); ?>" data-label="<?php echo esc_attr( $option['label'] );?>" data-field="<?php echo esc_attr( $field_base_id ); ?>" data-ref="<?php echo $field_id.'_'.$option_key; ?>" class="cf-toggle-group-radio <?php echo $field_id; ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $option['value'] ); ?>" <?php echo $sel; ?> data-radio-field="<?php echo esc_attr( $field_id ); ?>" data-calc-value="<?php echo esc_attr( Caldera_Forms_Field_Util::get_option_calculation_value( $option, $field, $form ) ); ?>" >
				<?php
			}
		}
		?></div>
		<?php echo $field_caption; ?>
	</div>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
