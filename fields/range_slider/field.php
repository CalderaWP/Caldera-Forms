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
				<input id="<?php echo esc_attr( $field_id ); ?>" type="range" data-handle="<?php echo esc_attr( $field['config']['handle'] ); ?>" data-trackcolor="<?php echo esc_attr( $field['config']['trackcolor'] ); ?>" data-handleborder="<?php echo esc_attr( $field['config']['handleborder'] ); ?>" data-color="<?php echo esc_attr( $field['config']['color'] ); ?>" data-field="<?php echo esc_attr( $field_base_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_value ); ?>" min="<?php echo esc_attr( $field['config']['min'] ); ?>" max="<?php echo esc_attr( $field['config']['max'] ); ?>" step="<?php echo esc_attr( $field['config']['step'] ); ?>" <?php echo $field_required; ?>>
			</div>
			<?php if(!empty($field['config']['showval'])){ ?><div class="col-xs-4"><?php if(!empty($field['config']['prefix'])){echo $field['config']['prefix']; } ?><span id="<?php echo esc_attr( $field_id ); ?>_value"><?php echo $field_value; ?></span><?php if(!empty($field['config']['suffix'])){echo $field['config']['suffix']; } ?></div><?php } ?>
		</div>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
<?php ob_start(); ?>
<script type="text/javascript">
	window.addEventListener("load", function(){

		function init_rangeslider_<?php echo $field_id; ?>(){
			var el = jQuery('#<?php echo $field_id; ?>'),
				rangeslider;
			<?php if(empty($field['config']['pollyfill'])){ ?>
			if (el.is(':visible')) {
				if( !el.data( 'plugin_rangeslider' ) ){
					rangeslider = el.rangeslider({
						onSlide: function(position, value) {
							<?php
								if( false !== strpos( $field['config']['step'], '.' ) ){
									$part = explode('.', $field['config']['step'] );								
							?>
							value = value.toFixed( <?php echo strlen( $part[1] ); ?> );
							<?php } ?>
							jQuery('#<?php echo $field_id; ?>_value').html(value);
						},
						polyfill: <?php echo $polyfill; ?>
					});
					rangeslider.parent().find('.rangeslider').css('backgroundColor', rangeslider.data('trackcolor'));
					rangeslider.parent().find('.rangeslider__fill').css('backgroundColor', rangeslider.data('color'));
					rangeslider.parent().find('.rangeslider__handle').css('backgroundColor', rangeslider.data('handle')).css('borderColor', rangeslider.data('handleborder'));
				}
			}else{
				el.rangeslider('destroy');
			}
			<?php }else{ ?>
			// pollyfill support
			el.on('change', function(){
				jQuery('#<?php echo $field_id; ?>_value').html(this.value);
			}).css("width", "100%");
			<?php } ?>
		}
		<?php if(empty($field['config']['pollyfill'])){ ?>
		// setup tabs
		jQuery(document).on('cf.pagenav cf.add cf.disable cf.modal', function(){
			init_rangeslider_<?php echo $field_id; ?>();
		});
		<?php } ?>
		// init slider
		init_rangeslider_<?php echo $field_id; ?>();

	});
</script>
<?php
	$script_template = ob_get_clean();
	if( ! empty( $form[ 'grid_object' ] ) && is_object( $form[ 'grid_object' ] ) ){
		$form[ 'grid_object' ]->append( $script_template, $field[ 'grid_location' ] );
	}else{
		echo $script_template;
	}
?>
