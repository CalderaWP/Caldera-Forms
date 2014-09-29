<div class="caldera-editor-header">
	<ul class="caldera-editor-header-nav">
		<li class="caldera-editor-logo">
			<span class="dashicons-cf-logo"></span>
			<?php echo __('Caldera Forms', 'caldera-forms'); ?>
		</li>
		<li class="caldera-element-type-label">
			<?php echo __('Extend', 'caldera-forms'); ?>
		</li>
		<li id="tab_license"><a href="#form-license-viewer">Licenses</a></li>
	</ul>
</div>

<div class="caldera-editor-header caldera-editor-subnav">
	<ul class="caldera-editor-header-nav ajax-trigger" data-load-class="spinner" data-request="<?php echo CFCORE_EXTEND_URL . 'channels/marketing/?version=' . CFCORE_VER; ?>" data-target="#main-cat-nav" data-target-insert="append" data-template="#nav-items-tmpl" data-event="none" data-autoload="true" id="main-cat-nav" >
	</ul>
</div>
<div class="form-extend-page-wrap" id="form-extend-viewer"></div>
<div class="form-extend-page-wrap" id="form-license-viewer" style="display:none;">
	<?php
	$addons = apply_filters( 'caldera_forms_get_active_addons', array() );
	if(empty($addons)){
		echo '<p class="description">' . __('No licensed addons installed.', 'caldera-forms') . '</p>';
	}else{
		foreach($addons as $slug=>$addon){
			$plugin = get_plugin_data( $addon['file'] );
			if($addon['type'] == 'selldock'){
				// selldock input
				$license = get_option('_' . $addon['slug'] . '_license_key');
				if(empty($license)){
					$license = null;
				}
				?>
				<div class="caldera-config-group">
					<label for="<?php echo $slug; ?>_licensekey"><?php echo $plugin['Name']; ?></label>
					<div class="caldera-config-field">
						<input class="ajax-trigger" data-version="<?php echo $plugin['Version']; ?>" data-target="#<?php echo $slug; ?>_licensekey_result" data-event="validate" data-action="selldock_activate_<?php echo $addon['slug']; ?>" data-slug="<?php echo $addon['slug']; ?>" type="password" value="<?php echo $license; ?>" name="license" id="<?php echo $slug; ?>_licensekey">&nbsp;
						<input type="button" value="Validate" name="selldock-check-keygen" class="button ajax-trigger" data-for="#<?php echo $slug; ?>_licensekey">
						<div class="selldock-message" id="<?php echo $slug; ?>_licensekey_result"></div>
					</div>
				</div>
				<?php
			}
		}
	}

	?>
</div>

<?php
	do_action('caldera_forms_admin_templates');
?>

<script type="text/javascript">
<?php

echo "var existing_addons = " . json_encode($addons).";";

?>
function update_existing(){
	for(var slug in existing_addons){
		jQuery('.panel_' + slug).css('opacity', .5).prop('title', '<?php echo __('Already Installed', 'caldera-forms'); ?>!');
	}
}


function cf_clear_panel(el){
	jQuery(jQuery(el).data('target')).empty();
}
jQuery(function($){
	$('.caldera-editor-header').on('click', '.caldera-editor-header-nav a', function(e){
		e.preventDefault();

		var clicked = $(this);

		// remove active tab
		$('.caldera-editor-header-nav li').removeClass('active');

		// hide all tabs
		$('.form-extend-page-wrap').hide();

		// show new tab
		$( clicked.attr('href') ).show();

		// set active tab
		clicked.parent().addClass('active');

	});

})

</script>