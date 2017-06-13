<?php 

$bound = null;
if( !empty( $field['config']['advanced_populate']['filter'] ) ){
	preg_match_all("/%(.+?)%/", $field['config']['advanced_populate']['filter'], $hastags);
	if(!empty($hastags[1])){
		
		foreach($hastags[1] as $tag_key=>$tag){
			foreach($form['fields'] as $key_id=>$fcfg){
				if($fcfg['slug'] === $tag){
					$bound = '[data-field="'.$key_id.'_'.$current_form_count.'"]';
				}
			}
		}
	}	
}

$field_value = Caldera_Forms_Field_Util::find_select_field_value( $field, $field_value );
// default
if( empty( $field['config']['border'] ) ){
	$field['config']['border'] = '#b6b6b6';
}
if( empty( $field['config']['color'] ) ){
	$field['config']['color'] = '#8f8f8f';
}

	echo $wrapper_before;
	if ( isset( $field[ 'slug' ] ) && isset( $_GET[ $field[ 'slug' ] ] ) ) {
		$field_value = Caldera_Forms_Sanitize::sanitize( $_GET[ $field[ 'slug' ] ] );
	}

	$multi = '';
	if( !empty( $field['config']['multi'] ) ){
		$multi = 'multiple="multiple"';
		$field_name .= '[]';
	}

	$placeholder = '';
	if( !empty( $field['config']['placeholder'] ) ){
		$placeholder = 'data-placeholder="' . esc_attr( Caldera_Forms::do_magic_tags( $field['config']['placeholder'] ) ). '"';
	}

?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<?php if( empty( $bound ) ){ ?>
		<select <?php echo $field_placeholder; ?> id="<?php echo esc_attr( $field_id ); ?>" <?php echo $multi; ?> data-select-two="true" data-field="<?php echo esc_attr( $field_base_id ); ?>" class="<?php echo esc_attr( $field_class ); ?>" name="<?php echo esc_attr( $field_name ); ?>" <?php echo $field_required; ?> <?php echo $placeholder; ?>>
		<?php

		if(!empty($field['config']['option'])){

			if ( ! empty( $field_value ) ) {
				echo "<option value=\"\"></option>\r\n";

			}elseif( !empty( $field['config']['placeholder'] ) ){
				echo '<option value=""></option>';
			}

			foreach($field['config']['option'] as $option_key=>$option){
				if(!isset($option['value'])){
					$option['value'] = htmlspecialchars( $option['label'] );
				}

				?>
				<option value="<?php echo $option['value']; ?>" <?php if( in_array( $option['value'] , (array) $field_value ) ){ ?>selected="selected"<?php } ?>><?php echo $option['label']; ?></option>
				<?php
			}
		} ?>
		</select>
		<?php }else{ ?>
		<input type="text" data-select-two="true" id="<?php echo esc_attr( $field_id ); ?>" data-field="<?php echo esc_attr( $field_base_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>[]" multiple="multiple" value="<?php echo htmlentities( $field_value ); ?>">
		<?php } ?>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>

<?php
ob_start();
?>
<style>
#s2id_<?php echo $field_id; ?>.ccselect2-container.ccselect2-dropdown-open .ccselect2-choice,
#s2id_<?php echo $field_id; ?>.ccselect2-container.ccselect2-dropdown-open .ccselect2-choices,
.s2id_<?php echo $field_id; ?>.ccselect2-drop-active,
.s2id_<?php echo $field_id; ?>.ccselect2-drop.ccselect2-drop-above.ccselect2-drop-active,
.s2id_<?php echo $field_id; ?>.ccselect2-container-active .ccselect2-choice,
.s2id_<?php echo $field_id; ?>.ccselect2-container-active .ccselect2-choices,
.s2id_<?php echo $field_id; ?>.ccselect2-dropdown-open.ccselect2-drop-above .ccselect2-choice,
.s2id_<?php echo $field_id; ?>.ccselect2-dropdown-open.ccselect2-drop-above .ccselect2-choices,
.s2id_<?php echo $field_id; ?>.ccselect2-container-multi.ccselect2-container-active .ccselect2-choices,
.s2id_<?php echo $field_id; ?>.ccselect2-container-multi .ccselect2-choices .ccselect2-search-choice-focus{
    border-color: <?php echo $field['config']['border']; ?> !important;
}
.s2id_<?php echo $field_id; ?> .ccselect2-results .ccselect2-highlighted,
.s2id_<?php echo $field_id; ?> .ccselect2-container-multi .ccselect2-choices .ccselect2-search-choice-focus{
	background: <?php echo $field['config']['color']; ?> !important;
}
.has-error .s2id_<?php echo $field_id; ?> .ccselect2-container .ccselect2-choice {
	border-color: #dd4b39 !important;
	background-color: #f2dede !important;
	background-image: none;
}
</style>
<script>
jQuery( function($){
	<?php if( !empty( $bound ) ){ ?>
	var opts = {
		ajax: {
			url: ajaxurl,
			dataType: 'json',
			quietMillis: 250,
			data: function (term, page) {
				return {
					action : 'cf_filter_populate',
					q: $('<?php echo $bound; ?>').val(), // search term
					<?php if( !empty( $field['config']['easy_pod'] ) ){?>easy_pod : '<?php echo $field['config']['easy_pod']; ?>'<?php } ?>
				};
			},
			results: function (data, page) {

				return { results: data };
			},
			cache: true
		}
	};
	<?php }else{ ?>	
	var opts = {};
	<?php } ?>

	$(document).on('cf.add', function(){
		$('#<?php echo $field_id; ?>').select2( opts );
	}).trigger('cf.add');
});
</script>
<?php
	$script_template = ob_get_clean();
	if( ! empty( $form[ 'grid_object' ] ) && is_object( $form[ 'grid_object' ] ) ){
		$form[ 'grid_object' ]->append( $script_template, $field[ 'grid_location' ] );
	}else{
		echo $script_template;
	}

















