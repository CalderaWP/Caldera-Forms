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

if( !isset( $field['config']['decimal_separator'] ) ){
	$field['config']['decimal_separator'] = '.';
}

$thousand_separator = $field['config']['thousand_separator'];
$decimal_separator = $field['config']['decimal_separator'];
$current_form_count = Caldera_Forms_Render_Util::get_current_form_count();
$target_id = Caldera_Forms_Field_Util::get_base_id( $field, $current_form_count, $form );
$form_id_atr = $form['ID' ] . '_' . $current_form_count;
$attrs = array(
	'type' => 'hidden',
	'name' => $field_name,
	'value' => 0,
	'data-field' => $target_id,
	'data-calc-field' => $field[ 'ID' ],
	'data-type' => 'calculation'
);
$attr_string =  caldera_forms_field_attributes( $attrs, $field, $form );


?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<<?php echo $elementType . $field_structure['aria']; ?> class="<?php echo $field['config']['classes']; ?>"><?php echo $field['config']['before']; ?>
			<span id="<?php echo esc_attr( $field_id ); ?>"><?php echo $field_value; ?></span><?php echo $field['config']['after']; ?></<?php echo $elementType; ?>>
				<input type="hidden" <?php echo $attr_string; ?> >
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

	$functions = Caldera_Forms_Field_Util::get_math_functions( $form );
	$formula = str_replace(  ' ', '', $formula );
	foreach ( $functions as $function ){
		$formula = preg_replace("/\b$function\(\b/", "Math.$function(", $formula);
	}

}

$formula = str_replace("\r",'', str_replace("\n",'', str_replace(' ','', trim( Caldera_Forms::do_magic_tags( $formula ) ) ) ) );
$binds = array();
$binds_wrap = array();
$binds_vars = array();
foreach ( Caldera_Forms_Forms::get_fields( $form, false ) as $fid => $c_field ) {
	if(false !== strpos($formula, $fid)){
		$formula  = str_replace($fid, $fid, $formula);
		$bind_var = $bind = '';
		$type     = Caldera_Forms_Field_Util::get_type( $c_field, $form );
		$_fid     = $fid . '_' . $current_form_count;
		switch( $type ){
			case 'checkbox' :
				$checkbox_sum = Caldera_Forms_Field_Calculation::checkbox_mode( $c_field, $form );
				if( $checkbox_sum ){
					$bind_var = "checked_total_" . $target_id. "(jQuery('[data-checkbox-field=\"".$_fid."\"]:checked'))";
				}else{
					$bind_var = "checked_highest_" . $target_id. "(jQuery('[data-checkbox-field=\"".$_fid."\"]:checked'))";
				}
				$bind = "[data-checkbox-field=\"".$_fid."\"]";
				break;
			case 'toggle_switch' :
			case 'radio' :
				$bind_var = "jQuery( '#" . $form_id_atr . "').find('[data-radio-field=\"".$_fid."\"]:checked').data( 'calc-value' )";
				$bind = "[data-radio-field=\"".$_fid."\"]";

				break;
			case 'calculation' :
				$bind_var = "jQuery( '#" . $form_id_atr . "').find( '[data-field=\"$_fid\"]' ).val()";
				$bind     = '[data-field="' . $_fid . '"]';
				break;
			case 'dropdown' :
				$bind_var = "jQuery( '#" . $form_id_atr . "').find( '[data-field=\"" . $fid . "\"] option:selected' ).data( 'calc-value' )";
				break;

			default :
				$bind_var = "jQuery( '#" . $form_id_atr . "').find( '[data-field=\"" . $fid . "\"]' ).val()";
				break;
		}
		$bind_var     = trim( $bind_var );
		$binds_vars[] = $fid." = parseFloat( $bind_var  ) || 0 ";
		if( ! $bind ){
			$bind = "#$_fid";
		}
		$binds[] = $bind;
		// include a conditional wrapper
		$binds_wrap[] = "#conditional_".$fid;		
	}
}


if(!empty($binds)){
	$bindtriggers = array_merge($binds, $binds_wrap);

	ob_start();
?>
<script type="text/javascript">
	window.addEventListener("load", function(){

		function checked_total_<?php echo $target_id; ?>(items){
			var sum = 0;
			items.each(function(k,v){
				var val = jQuery(v).data( 'calc-value' );
				sum += parseFloat( val );
			});
			return sum;
		}

		function checked_highest_<?php echo $target_id; ?>(items){
			var highest = 0;
			items.each(function(k,v){
				if( parseFloat( v.value ) > parseFloat( highest ) ){
					highest = parseFloat( v.value );
				}
			});
			return highest;
		}

		function docalc_<?php echo $target_id; ?>(){
			var <?php echo implode(', ',$binds_vars); ?>,
				total = <?php echo $formula; ?>,
				view_total = total;

			<?php if(!empty($field['config']['fixed'])){ ?>
			function addCommas(nStr){
				nStr += '';
				x = nStr.split('.');
				x1 = x[0];
				x2 = x.length > 1 ? '<?php echo $decimal_separator; ?>' + x[1] : '';
				var rgx = /(\d+)(\d{3})/;
				while (rgx.test(x1)) {

					x1 = x1.replace(rgx, '$1' + '<?php echo $thousand_separator; ?>' + '$2');
				}
				return x1 + x2;
			}

			if( 'number' != typeof  total ){
				total = parseInt( total, 10 );
			}

			total = total.toFixed(2);
			view_total = addCommas( total );
			<?php } ?>
			if( view_total.toString().length > 18 ){
				view_total = Math.round( view_total );
			}
			jQuery('#<?php echo $field_id; ?>').html( view_total );
			jQuery('[data-field="<?php echo esc_attr( $target_id ); ?>"]').val( total ).trigger('change');

		}
		jQuery('body').on('change keyup', '<?php echo implode(',', $bindtriggers); ?>', function(e){
			docalc_<?php echo $target_id; ?>();
		});
		jQuery( document ).on('cf.remove cf.add', function( e ){
			docalc_<?php echo $target_id; ?>();
		});
		docalc_<?php echo $target_id; ?>();
	});
	
</script>
<?php 

	$script_template = ob_get_clean();
	Caldera_Forms_Render_Util::add_inline_data( $script_template, $form );
}

