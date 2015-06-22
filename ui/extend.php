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

<div class="form-extend-page-wrap" id="form-extend-viewer" style="visibility:visible;"></div>
<div class="form-extend-page-wrap" id="form-license-viewer" style="display:none;">
	<div>
		<em>
			<?php _e( 'Note: We are currently moving Caldera Forms licenses to the CalderaWP License Manager. This panel will be removed in a future version of Caldera Forms.', 'caldera-forms' ); ?>
			</em>
	</div>
	<?php
	$addons = apply_filters( 'caldera_forms_get_active_addons', array() );
	if(empty($addons)){
		echo '<p class="description">' . __('No licensed addons installed.', 'caldera-forms') . '</p>';
	}else{
		foreach($addons as $slug=>$addon){
			$plugin = get_plugin_data( $addon['file'] );		
			if( !empty( $addon['template'] ) ){
				include $addon['template'];
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
