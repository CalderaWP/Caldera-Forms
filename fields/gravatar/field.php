<?php
if(!empty($field['config']['email'])){
	$email = self::do_magic_tags( $field['config']['email'] );
	if( !is_email( $email ) ){
		$email = '';
	}
}else{
	$email = '';
}

?><?php echo $wrapper_before; ?>
	<?php echo $field_before; ?>
	<div style="text-align:center;" class="live-gravatar">
		<?php /*<span style="background: url(<?php echo admin_url('images/spinner.gif'); ?>) no-repeat scroll center center transparent;overflow: hidden;border-radius:<?php echo $field['config']['border_radius']; ?>px; border:<?php echo $field['config']['border_size']; ?>px solid <?php echo $field['config']['border_color']; ?>;display: inline-block;"> */ ?>
		<span style="overflow: hidden;border-radius:<?php echo $field['config']['border_radius']; ?>px; border:<?php echo $field['config']['border_size']; ?>px solid <?php echo $field['config']['border_color']; ?>;display: inline-block;">
			<span style="border-radius:<?php echo $field['config']['border_radius']; ?>px;width:<?php echo $field['config']['size']; ?>px;height:<?php echo $field['config']['size']; ?>px;display:inline-block;" id="<?php echo $field_id; ?>_gravatar"><?php

				echo get_avatar( $email, (int) $field['config']['size'], $field['config']['generator']);

			?></span>
		</span>
	</div>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
<?php
if(empty($field['config']['email'])){
	return;
}

ob_start();
?>
<script type="text/javascript">

jQuery(function($){
	
	var timeout_<?php echo $field['config']['email'] .'_'.$current_form_count; ?>,
		loading_<?php echo $field['config']['email'] .'_'.$current_form_count; ?>,
		current_<?php echo $field['config']['email'] .'_'.$current_form_count; ?>;
	
	$(document).on('keyup change cf.add','[data-field="<?php echo $field['config']['email'] .'_'.$current_form_count; ?>"]', function(){

		if(timeout_<?php echo $field['config']['email'] .'_'.$current_form_count; ?>){
			clearTimeout(timeout_<?php echo $field['config']['email'] .'_'.$current_form_count; ?>);
		}
		if(loading_<?php echo $field['config']['email'] .'_'.$current_form_count; ?>){
			loading_<?php echo $field['config']['email'] .'_'.$current_form_count; ?>.abort();
		}

		var email 		= this.value,
			container	= $('#<?php echo $field_id; ?>_gravatar');
		if(email.indexOf('@') < 0 || email.length <= email.indexOf('@') + 1 || current_<?php echo $field['config']['email'] .'_'.$current_form_count; ?> === email){
			if(email.length > 0){
				return;
			}
		}
		timeout_<?php echo $field['config']['email'] .'_'.$current_form_count; ?> = setTimeout(function(){
			container.find('img').animate({opacity: .5}, 200);
			loading_<?php echo $field['config']['email'] .'_'.$current_form_count; ?> = $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
				action	:	'cf_live_gravatar_get_gravatar',
				email	:	email,
				size	:	'<?php echo $field['config']['size']; ?>',
				generator: '<?php echo $field['config']['generator']; ?>'
			}, function(res){
				if(res.length){
					current_<?php echo $field['config']['email'] .'_'.$current_form_count; ?> = email;
					var image = $(res).load(function(){
						var img = $(this).css('opacity', .5);
						container.find('img').animate({opacity: 0}, 200, function(){
							container.html(img).find('img').animate({opacity: 1}, 200);
						})
					});

					//
				}else{
					container.find('img').animate({opacity: 1}, 200);
				}
			});

		}, 100);
	});
	$(document).on('cf.add', '#conditional_<?php echo $field_id; ?>', function(){
		$('[data-field="<?php echo $field['config']['email'] .'_'.$current_form_count; ?>"]').trigger('change');
	});

})

</script>
<?php
	$script_template = ob_get_clean();
	$grid->append( $script_template, $location );