<?php
if ( is_array( $field_value ) )  {
	if ( isset( $field_value[0] ) ) {
		$field_value = $field_value[0];
	}else{
		$field_value = ' ';
	}

}

if(!empty($field['config']['placeholder'])){
	$placeholder = Caldera_Forms::do_magic_tags( $field['config']['placeholder'] );

	$field_placeholder = 'placeholder="'. esc_attr( $placeholder ) .'"';
}
preg_match_all("/%(.+?)%/", $field['config']['default'], $hastags);
if(!empty($hastags[1])){
	$binds = array();
	foreach($hastags[1] as $tag_key=>$tag){
		foreach($form['fields'] as $key_id=>$fcfg){
			if( $key_id == $field_base_id ){ continue; } // ye bad to sync to itself
			if($fcfg['slug'] === $tag){
				$binds[] = $key_id;
				$field['config']['default'] = str_replace($hastags[0][$tag_key], '{{'.$key_id.'}}', $field['config']['default']);
			}
		}
	}
	$field_value = $field['config']['default'];
	$sync = true;
}

?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<textarea <?php echo $field_placeholder; ?> data-field="<?php echo esc_attr( $field_base_id ); ?>" <?php if(!empty( $sync ) ){ ?>data-binds="<?php echo esc_attr(wp_json_encode($binds)); ?>" data-sync="<?php echo esc_attr( $field['config']['default'] ); ?>"<?php } ?> class="<?php echo esc_attr( $field_class ); ?>" rows="<?php echo $field['config']['rows']; ?>" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" <?php echo $field_required; ?> <?php echo $field_structure['aria']; ?>><?php echo esc_html( $field_value ); ?></textarea>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
