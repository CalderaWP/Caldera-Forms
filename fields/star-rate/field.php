<?php
if(!isset($field['config']['track_color'])){
	 $field['config']['track_color'] = '#AFAFAF';
}
if(!isset($field['config']['type'])){
	 $field['config']['type'] = 'star';
}
?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<div style="position: relative;">
			<div id="<?php echo $field_id; ?>_stars" style="color:<?php echo $field['config']['track_color']; ?>;font-size:<?php echo floatval( $field['config']['size'] ); ?>px;"></div>
			<input id="<?php echo $field_id; ?>" type="text" data-field="<?php echo $field_base_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo $field_value; ?>" <?php echo $field_required; ?> style="position: absolute; width: 0px; height: 0px; padding: 0px; bottom: 0px; left: 12px; opacity: 0; z-index: -1000;">
		</div>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
<?php ob_start(); ?>
<script type="text/javascript">
	jQuery(function($){		
		$('#<?php echo $field_id; ?>_stars').raty({
			starOff	: 'raty-<?php echo $field['config']['type']; ?>-off',
			starOn : 'raty-<?php echo $field['config']['type']; ?>-on',	
			target: '#<?php echo $field_id; ?>',
			spaceWidth: <?php echo $field['config']['space']; ?>, 
			targetKeep: true, targetType: 'score',
			<?php if(!empty($field_value)){ echo "score: ".$field_value.","; }; ?> 
			hints: [1,2,3,4,5], 
			number: <?php echo $field['config']['number']; ?>, 
			starType: 'f',
			starColor: '<?php echo $field['config']['color']; ?>',
			numberMax: 100,
			click :function(e){
				$('#<?php echo $field_id; ?>').trigger('change');
			}
			<?php if(!empty($field['config']['cancel'])){ echo ",cancel: true"; }; ?>
			<?php if(!empty($field['config']['single'])){ echo ",single: true"; }; ?>
		});
	});
</script>
<?php
	$script_template = ob_get_clean();
	$grid->append( $script_template, $location );