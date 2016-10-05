<?php



// magics!
$html_template = $field['config']['default'];
preg_match_all("/%(.+?)%/", $html_template, $hastags);
$bindfields = array();
if(!empty($hastags[1])){
	$binds = array();

	foreach($hastags[1] as $tag_key=>$tag){

		foreach($form['fields'] as $key_id=>$fcfg){
			if($fcfg['slug'] === $tag){
				$binds[] = '[data-field="'.$key_id.'"]';
				$bindfields[] = '"'.$key_id.'"';
				$html_template = str_replace($hastags[0][$tag_key], '{{'.$key_id.'}}', $html_template);
			}
		}
	}
	echo '<div id="html-content-' . esc_attr( $field_id ) . '" data-field="' . esc_attr( $field_id ) . '" class="' . esc_attr( $field['config']['custom_class'] ) . '"></div>';

	// create template block
	ob_start();
	echo '<script type="text/html" id="html-content-' . esc_attr( $field_id ) . '-tmpl">';
		echo do_shortcode( Caldera_Forms::do_magic_tags( $html_template ) );
	echo '</script>';
	
	?>
	<script type="text/javascript">
		window.addEventListener("load", function(){
			
			function htmltemplate<?php echo $field_id; ?>(){

				var template = jQuery('#html-content-<?php echo $field_id; ?>-tmpl').html(),
					target = jQuery('#html-content-<?php echo $field_id; ?>'),
					list = [<?php echo implode(',', $bindfields); ?>];

				for(var i =0; i < list.length; i++){
					
					var field = jQuery('[data-field="'+list[i]+'"]'),
						value = [];
					for(var f=0; f < field.length; f++){
						if( jQuery(field[f]).is(':radio,:checkbox') ){
							if(!jQuery(field[f]).prop('checked')){
								continue;
							}
						}
						if( jQuery(field[f]).is('input:file') ){
							var file_parts = field[f].value.split('\\');
							value.push( file_parts[file_parts.length-1] );
						}else{
							if( field[f].value ){
								value.push( field[f].value );
							}
						}
					}

					template = template.replace( new RegExp("\{\{" + list[i] + "\}\}","g"), value.join(', ') );
				}
				target.html(template).trigger('change');

			}
			jQuery('body').on('change keyup', '<?php echo implode(',', $binds); ?>', htmltemplate<?php echo $field_id; ?>);

			htmltemplate<?php echo $field_id; ?>();

		})
	</script>
	<?php
	$script_template = ob_get_clean();
	if( ! empty( $form[ 'grid_object' ] ) && is_object( $form[ 'grid_object' ] ) ){
		$form[ 'grid_object' ]->append( $script_template, $field[ 'grid_location' ] );
	}else{
		echo $script_template;
	}
			
}else{
	echo '<div class="' . esc_attr( $field['config']['custom_class'] ) . '">' . do_shortcode( Caldera_Forms::do_magic_tags( $html_template ) ) . '</div>';
}



