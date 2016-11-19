<?php
$polyfill = 'false';
if(!empty($field['config']['pollyfill'])){
	$polyfill = 'true';
}
if(!empty($field['config']['suffix'])){
	$field['config']['suffix'] = Caldera_Forms::do_magic_tags($field['config']['suffix']);
}
if(!empty($field['config']['prefix'])){
	$field['config']['prefix'] = Caldera_Forms::do_magic_tags($field['config']['prefix']);
}

if ( is_array( $field_value ) )  {
	if ( isset( $field_value[0] ) ) {
		$field_value = $field_value[0];
	}else{
		$field_value = 0;
	}
}

	echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<div style="position: relative;" <?php if(!empty($field['config']['showval'])){ ?>class="row"<?php } ?>>
			<?php if(!empty($field['config']['showval'])){ ?><div class="col-xs-8" style="margin: <?php if(!empty($field['config']['pollyfill'])){ echo '2px'; }else{ echo '8px'; } ?> 0px;"><?php }else{ ?><div style="margin: <?php if(!empty($field['config']['pollyfill'])){ echo '6px'; }else{ echo '12px'; } ?> 0px;"><?php } ?>
				<input id="<?php echo esc_attr( $field_id ); ?>" type="range" data-handle="<?php echo esc_attr( $field['config']['handle'] ); ?>" data-field="<?php echo esc_attr( $field_base_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_value ); ?>" min="<?php echo esc_attr( $field['config']['min'] ); ?>" max="<?php echo esc_attr( $field['config']['max'] ); ?>" step="<?php echo esc_attr( $field['config']['step'] ); ?>" <?php echo $field_required; ?> >
			</div>
			<?php if(!empty($field['config']['showval'])){ ?><div class="col-xs-4"><?php if(!empty($field['config']['prefix'])){echo $field['config']['prefix']; } ?><span id="<?php echo esc_attr( $field_id ); ?>_value"><?php echo $field_value; ?></span><?php if(!empty($field['config']['suffix'])){echo $field['config']['suffix']; } ?></div><?php } ?>
		</div>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
