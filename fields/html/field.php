<?php
// magics!
$field['config']['default'] = self::do_magic_tags($field['config']['default']);

preg_match_all("/%(.+?)%/", $field['config']['default'], $hastags);
if(!empty($hastags[1])){
	$binds = array();

	foreach($hastags[1] as $tag_key=>$tag){

		foreach($form['fields'] as $key_id=>$fcfg){
			if($fcfg['slug'] === $tag){
				$binds[] = '[data-field="'.$key_id.'_'.$current_form_count.'"]';
				$bindfields[] = '"'.$key_id.'_'.$current_form_count.'"';
				$field['config']['default'] = str_replace($hastags[0][$tag_key], '{{'.$key_id.'_'.$current_form_count.'}}', $field['config']['default']);
			}
		}
	}
	echo '<div id="html-content-'.$field_id.'" data-field="'.$field_id.'"></div>';
	echo '<script type="text/html" id="html-content-'.$field_id.'-tmpl">';
		echo do_shortcode( $field['config']['default'] );
	echo '</script>';
	?>
	<script type="text/javascript">
		jQuery(function($){

			function htmltemplate<?php echo $field_id; ?>(){

				var template = $('#html-content-<?php echo $field_id; ?>-tmpl').html(),
					target = $('#html-content-<?php echo $field_id; ?>'),
					list = [<?php echo implode(',', $bindfields); ?>];

				for(var i =0; i < list.length; i++){
					template = template.replace( new RegExp("\{\{" + list[i] + "\}\}","g"), $('[data-field="'+list[i]+'"]').val() );
				}
				target.html(template).trigger('change');

			}
			$('body').on('change', '<?php echo implode(',', $binds); ?>', htmltemplate<?php echo $field_id; ?>);

			htmltemplate<?php echo $field_id; ?>();

		})
	</script>
	<?php
}else{
	echo do_shortcode( $field['config']['default'] );
}


?>