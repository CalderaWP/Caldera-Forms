<?php
if(!empty($field['config']['placeholder'])){
	$field_placeholder = 'placeholder="' . esc_attr(Caldera_Forms::do_magic_tags($field['config']['placeholder'])) . '"';
}

$mask = null;
if(!empty($field['config']['masked'])){
	$mask = "data-inputmask=\"'mask': '".$field['config']['mask']."'\" ";
}
$type_override = 'text';
if( !empty( $field['config']['type_override'] ) ){
	$type_override = $field['config']['type_override'];
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
?>
<?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<input <?php echo $field_placeholder; ?> type="<?php echo esc_attr( $type_override ); ?>" <?php echo $mask; ?><?php if(!empty( $sync ) ){ ?>data-binds="<?php echo esc_attr(wp_json_encode($binds)); ?>" data-sync="<?php echo esc_attr( $field['config']['default'] ); ?>"<?php } ?>data-field="<?php echo esc_attr( $field_base_id ); ?>" class="<?php echo esc_attr( $field_class ); ?>" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_value ); ?>" <?php echo $field_required; ?> <?php echo $field_structure['aria']; ?>>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>