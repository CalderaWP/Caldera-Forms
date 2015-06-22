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
		$placeholder = 'data-placeholder="' . esc_attr( $field['config']['placeholder'] ) . '"';
	}

?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<?php if( empty( $bound ) ){ ?>
		<select <?php echo $field_placeholder; ?> id="<?php echo $field_id; ?>" <?php echo $multi; ?> data-select-two="true" data-field="<?php echo $field_base_id; ?>" class="<?php echo $field_class; ?>" name="<?php echo $field_name; ?>" <?php echo $field_required; ?> <?php echo $placeholder; ?>>
		<?php
			if(isset( $field['config'] ) && isset($field['config']['default']) && isset($field['config']['option'][$field['config']['default']])){
				//if( $field['config']['option'][$field['config']['default']]['value'] )
				if( $field['config']['default'] === $field_value ){
					$field_value = $field['config']['option'][$field['config']['default']]['value'];
				}


			}


		if(!empty($field['config']['option'])){
			if(!empty($field['config']['default'])){
				if(!isset($field['config']['option'][$field['config']['default']])){
					echo "<option value=\"\"></option>\r\n";
				}
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
		<input type="text" data-select-two="true" id="<?php echo $field_id; ?>" data-field="<?php echo $field_base_id; ?>" name="<?php echo $field_name; ?>[]" multiple="multiple" value="<?php echo htmlentities( $field_value ); ?>">
		<?php } ?>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
<style>
.ccselect2-drop-active,.ccselect2-drop.ccselect2-drop-above.ccselect2-drop-active,.ccselect2-container-active .ccselect2-choice,.ccselect2-container-active .ccselect2-choices,.ccselect2-dropdown-open.ccselect2-drop-above .ccselect2-choice,.ccselect2-dropdown-open.ccselect2-drop-above .ccselect2-choices,.ccselect2-container-multi.ccselect2-container-active .ccselect2-choices,.ccselect2-container-multi .ccselect2-choices .ccselect2-search-choice-focus{
    border-color: <?php echo $field['config']['border']; ?>;
}.ccselect2-results .ccselect2-highlighted,.ccselect2-container-multi .ccselect2-choices .ccselect2-search-choice-focus{
	background: <?php echo $field['config']['color']; ?>;
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
				console.log( data );
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


















