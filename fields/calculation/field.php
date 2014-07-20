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

if(!empty($field['config']['manual'])){
	$formula = $field['config']['manual_formula'];
	preg_match_all("/%(.+?)%/", $formula, $hastags);
	if(!empty($hastags[1])){
		$binds = array();

		foreach($hastags[1] as $tag_key=>$tag){

			foreach($form['fields'] as $key_id=>$fcfg){
				if($fcfg['slug'] === $tag){
					$binds[] = '#'.$key_id;
					$bindfields[] = '"'.$key_id.'"';
					$formula = str_replace($hastags[0][$tag_key], $key_id, $formula);
				}
			}
		}
	}
	// fix POW
	$formula = str_replace('pow(', 'Math.pow(', $formula);

}

$binds = array();
$binds_wrap = array();
$binds_vars = array();
foreach($form['fields'] as $fid=>$cfg){
	if(false !== strpos($formula, $fid)){
		$binds_vars[] = $fid." = parseFloat( $('[data-field=\"".$fid."\"]').is(':checkbox') ? checked_total_" . $field_base_id. "($('[data-field=\"".$fid."\"]:checked')) : $('[data-field=\"".$fid."\"]').is(':radio') ? $('[data-field=\"".$fid."\"]:checked').val() : $('[data-field=\"".$fid."\"]').val() ) || 0 ";
		$binds[] = "[data-field=\"".$fid."\"]";
		// include a conditional wrapper
		$binds_wrap[] = "#conditional_".$fid;
	}
}


if(!empty($binds)){
	$bindtriggers = array_merge($binds, $binds_wrap);

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
		$('body').on('change keyup cf.remove cf.add', '<?php echo implode(',', $bindtriggers); ?>', function(e){
			docalc_<?php echo $field_base_id; ?>();
		});
		docalc_<?php echo $field_base_id; ?>();
	});
	
</script>
<?php } ?>