<?php

$elementType = $field['config']['element'];
if(empty($elementType)){
	$elementType = 'div';
}

if(!empty($field['config']['before'])){
	$field['config']['before'] = Caldera_Forms::do_magic_tags($field['config']['before']);
}
if(!empty($field['config']['after'])){
	$field['config']['after'] = Caldera_Forms::do_magic_tags($field['config']['after']);
}


if( !isset( $field['config']['thousand_separator'] ) ){
	$field['config']['thousand_separator'] = ',';
}

$thousand_separator = $field['config']['thousand_separator'];

?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<<?php echo $elementType; ?> class="<?php echo $field['config']['classes']; ?>"><?php echo $field['config']['before']; ?><span id="<?php echo $field_id; ?>"><?php echo $field_value; ?></span><?php echo $field['config']['after']; ?></<?php echo $elementType; ?>>
		<input type="hidden" name="<?php echo $field_name; ?>" value="0" data-field="<?php echo $field_base_id; ?>" >
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
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
					$binds[] = '[data-field="'.$key_id.'"]';
					$bindfields[] = '"'.$key_id.'_'.$current_form_count.'"';
					$formula = str_replace($hastags[0][$tag_key], $key_id, $formula);
				}
			}
		}
	}
	// fix POW
	$formula = str_replace('pow(', 'Math.pow(', $formula);
	$formula = str_replace('ceil(', 'Math.ceil(', $formula);
}
$formula = str_replace("\r",'', str_replace("\n",'', str_replace(' ','', trim( Caldera_Forms::do_magic_tags( $formula ) ) ) ) );
$binds = array();
$binds_wrap = array();
$binds_vars = array();
foreach($form['fields'] as $fid=>$cfg){
	if(false !== strpos($formula, $fid)){
		//dump($cfg,0);
		$formula = str_replace($fid, $fid, $formula);
		if($cfg['type'] == 'date_picker') {
			$binds_vars[] = $fid." = ($('[data-field=\"".$fid."\"]').val() == '' ? 0 : ((new Date( $('[data-field=\"".$fid."\"]').val() )) / (1000 * 3600 * 24)))";
		} else {
			$binds_vars[] = $fid . " = parseFloat( $('[data-field=\"" . $fid . "\"]').is(':checkbox') ? checked_total_" . $field_base_id . "($('[data-field=\"" . $fid . "\"]:checked')) : $('[data-field=\"" . $fid . "\"]').is(':radio') ? $('[data-field=\"" . $fid . "\"]:checked').val() : $('[data-field=\"" . $fid . "\"]').val() ) || 0 ";
		}

		$binds[] = "[data-field=\"".$fid."\"]";
		// include a conditional wrapper
		$binds_wrap[] = "#conditional_".$fid;
	}
}

if(!empty($binds)){
	$bindtriggers = array_merge($binds, $binds_wrap);

	ob_start();
?>
<script type="text/javascript">
	jQuery(function($){

		function checked_total_<?php echo $field_base_id; ?>(items){
			var sum = 0;
			
			items.each(function(k,v){
				var val = $(v).val();
				sum += parseFloat( val );
			});
			return sum;
		}
		function docalc_<?php echo $field_base_id; ?>(){
			var <?php echo implode(', ',$binds_vars); ?>,
				total = <?php echo $formula; ?>,
				view_total = total;

			<?php if(!empty($field['config']['fixed'])){ ?>
			function addCommas(nStr){
				nStr += '';
				x = nStr.split('.');
				x1 = x[0];
				x2 = x.length > 1 ? '.' + x[1] : '';
				var rgx = /(\d+)(\d{3})/;
				while (rgx.test(x1)) {
					x1 = x1.replace(rgx, '$1' + '<?php echo $thousand_separator; ?>' + '$2');
				}
				return x1 + x2;
			}
			total = total.toFixed(2);
			view_total = addCommas( total );
			<?php } ?>

			$('#<?php echo $field_id; ?>').html( view_total );
			$('[data-field="<?php echo $field_base_id; ?>"]').val( total ).trigger('change');

		}
		$('body').on('change keyup', '<?php echo implode(',', $bindtriggers); ?>', function(e){
			docalc_<?php echo $field_base_id; ?>();
		});
		$( document ).on('cf.remove cf.add', function( e ){
			docalc_<?php echo $field_base_id; ?>();
		})
		docalc_<?php echo $field_base_id; ?>();
	});
	
</script>
<?php 

	$script_template = ob_get_clean();
	if( ! empty( $form[ 'grid_object' ] ) && is_object( $form[ 'grid_object' ] ) ){
		$form[ 'grid_object' ]->append( $script_template, $field[ 'grid_location' ] );
	}else{
		echo $script_template;
	}

} ?>
