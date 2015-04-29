<div class="caldera-editor-header">
	<ul class="caldera-editor-header-nav">
		<li class="caldera-editor-logo">
			<span class="dashicons-cf-logo"></span>
			<?php echo $form['name']; ?>
		</li>
		<?php if(!empty($form['description'])){ ?>
		<li class="caldera-element-type-label">
			<?php echo $form['description']; ?>
		</li>
		<?php } ?>
		<?php if( current_user_can( 'delete_others_posts' ) && empty( $form['_external_form'] ) ){ ?>
		<li class="caldera-forms-toolbar-item">
			<a class="button" href="admin.php?page=caldera-forms&edit=<?php echo $form['ID']; ?>"><?php echo __('Edit'); ?></a>
		</li>		
		<?php } ?>
	</ul>
</div>
<span class="form_entry_row highlight">
<span class="form-control form-entry-trigger ajax-trigger" data-autoload="true" data-page="1" data-status="active" data-callback="setup_pagination" data-group="entry_nav" data-active-class="highlight" data-load-class="spinner" data-active-element="#form_row_<?php echo $form['ID']; ?>" data-template="#forms-list-alt-tmpl" data-form="<?php echo $form['ID']; ?>" data-target="#form-entries-viewer" data-action="browse_entries"></span>
</span>
<?php include CFCORE_PATH . 'ui/entries_toolbar.php'; ?>
<div class="form-extend-page-wrap">	
	<div id="form-entries-viewer"></div>
</div>

<?php
	do_action('caldera_forms_admin_templates');
?>

<script type="text/javascript">

function cf_clear_panel(el){
	jQuery(jQuery(el).data('target')).empty();
}
jQuery(function($){
	$('.caldera-entry-exporter').show();
	$('.caldera-editor-header').on('click', '.caldera-editor-header-nav a', function(e){
		var clicked = $(this);
		if(clicked.hasClass('button')){
			return;
		}
		e.preventDefault();
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
<?php
include CFCORE_PATH . 'ui/entry_navigation.php';
?>