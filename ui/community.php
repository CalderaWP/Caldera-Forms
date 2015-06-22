<div class="caldera-editor-header">
	<ul class="caldera-editor-header-nav">
		<li class="caldera-editor-logo">
			<span class="dashicons-cf-logo"></span>
			<?php echo __('Caldera Forms', 'caldera-forms'); ?>
		</li>
		<li class="caldera-element-type-label">
			<?php echo __('Community', 'caldera-forms'); ?>
		</li>
	</ul>
</div>

<div class="caldera-editor-header caldera-editor-subnav">
	<ul class="caldera-editor-header-nav ajax-trigger" data-load-class="spinner" data-request="<?php echo CFCORE_EXTEND_URL . 'channels/community/?version=' . CFCORE_VER; ?>" data-target="#main-cat-nav" data-target-insert="append" data-template="#nav-items-tmpl" data-event="loadchannels" data-autoload="true" id="main-cat-nav" >
	</ul>
</div>
<div class="form-extend-page-wrap" id="form-extend-viewer" style="visibility:visible;"></div>

<?php
	do_action('caldera_forms_admin_templates');
?>

<script type="text/javascript">

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