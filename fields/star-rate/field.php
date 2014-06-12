<div class="<?php echo $field_wrapper_class; ?>">
	<?php echo $field_label; ?>
	<div class="<?php echo $field_input_class; ?>">
		<div style="position: relative;">
			<div id="<?php echo $field_id; ?>_stars" style="color:<?php echo $field['config']['color']; ?>;font-size:<?php echo floatval( $field['config']['size'] ); ?>px;"></div>
			<input id="<?php echo $field_id; ?>" type="text" data-field="<?php echo $field_base_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo $field_value; ?>" <?php echo $field_required; ?> style="position: absolute; width: 0px; height: 0px; padding: 0px; bottom: 0px; left: 12px; opacity: 0; z-index: -1000;">
		</div>
		<?php echo $field_caption; ?>
	</div>
</div>
<script type="text/javascript">
	jQuery(function($){		
		$('#<?php echo $field_id; ?>_stars').raty({
			target: '#<?php echo $field_id; ?>',
			spaceWidth: <?php echo $field['config']['space']; ?>, 
			targetKeep: true, targetType: 'score',
			<?php if(!empty($field_value)){ echo "score: ".$field_value.","; }; ?> 
			hints: [1,2,3,4,5], 
			number: <?php echo $field['config']['number']; ?>, 
			starType: 'f', 
			numberMax: 100,
			click :function(e){
				$('#<?php echo $field_id; ?>').trigger('change');
			}
			<?php if(!empty($field['config']['cancel'])){ echo ",cancel: true"; }; ?>
			<?php if(!empty($field['config']['single'])){ echo ",single: true"; }; ?>
		});
	});
</script>