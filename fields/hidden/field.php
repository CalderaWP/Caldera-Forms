<?php
$sync = false;
if ( is_array( $field_value ) )  {
	if ( isset( $field_value[0] ) ) {
		$field_value = $field_value[0];
	}else{
		$field_value = ' ';
	}
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
<input type="hidden" id="<?php echo esc_attr( $field_id ); ?>" class="<?php echo esc_attr( $field['config']['custom_class'] ); ?>" <?php if(!empty( $sync ) ){ ?>data-binds="<?php echo esc_attr( wp_json_encode( $binds ) ); ?>"" data-sync="<?php echo esc_attr( $field['config']['default'] ); ?>"<?php } ?> data-field="<?php echo esc_attr( $field_base_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo htmlentities( $field_value ); ?>">

