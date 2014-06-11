<?php

$elementType = $field['config']['element'];
if(empty($elementType)){
	$elementType = 'div';
}

?><div class="<?php echo $field_wrapper_class; ?>">
	<?php echo $field_label; ?>
	<div class="<?php echo $field_input_class; ?>">
		<<?php echo $elementType; ?> class="<?php echo $field['config']['classes']; ?>"><?php echo $field['config']['before']; ?><span id="<?php echo $field_id; ?>"><?php echo $field_value; ?></span><?php echo $field['config']['after']; ?></<?php echo $elementType; ?>>
		<input type="hidden" name="<?php echo $field_name; ?>" value="0" data-field="<?php echo $field_base_id; ?>" >
		<?php echo $field_caption; ?>
	</div>
</div>
<?php
$formula = $field['config']['formular'];
$binds = array();
$binds_vars = array();
foreach($form['fields'] as $fid=>$cfg){
	if(false !== strpos($formula, $fid)){
		$binds_vars[] = $fid." = parseFloat( $('[data-field=\"".$fid."\"]').is(':checkbox') ? checked_total_" . $field_base_id. "($('[data-field=\"".$fid."\"]:checked')) : $('[data-field=\"".$fid."\"]').is(':radio') ? $('[data-field=\"".$fid."\"]:checked').val() : $('[data-field=\"".$fid."\"]').val() ) || 0 ";
		$binds[] = "[data-field=\"".$fid."\"]";
	}
}

if(!empty($binds)){
?>
<script type="text/javascript">
	jQuery(function($){
		function checked_total_<?php echo $field_base_id; ?>(items){
			var sum = 0;
			items.each(function(k,v){
				sum += parseFloat($(v).val());
			})
			return sum;
		}
		function docalc_<?php echo $field_base_id; ?>(){
			var <?php echo implode(', ',$binds_vars); ?>,
				total = <?php echo $formula; ?>;

			<?php if(!empty($field['config']['fixed'])){ ?>
			total = total.toFixed(2);
			<?php } ?>

			$('#<?php echo $field_id; ?>').html( total );
			$('[data-field="<?php echo $field_base_id; ?>"]').val( total ).trigger('change');

		}
		$('body').on('change keyup cf.remove', '<?php echo implode(',', $binds); ?>', function(e){
			docalc_<?php echo $field_base_id; ?>();
		});
		docalc_<?php echo $field_base_id; ?>();
	});
	
</script>
<?php } ?>