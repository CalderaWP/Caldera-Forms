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
<span class="form-control form-entry-trigger ajax-trigger"
      data-autoload="true" data-page="1" 
      data-status="active"
      data-callback="setup_pagination" 
      data-group="entry_nav" 
      data-active-class="highlight" 
      data-load-class="spinner"
      data-active-element="#form_row_<?php echo $form[ 'ID' ]; ?>" 
      data-template="#forms-list-alt-tmpl"
      data-form="<?php echo $form[ 'ID' ]; ?>" 
      data-target="#form-entries-viewer" data-action="browse_entries"
      data-nonce="<?php echo wp_create_nonce( 'view_entries' ); ?>"
></span>
</span>
<?php 
$is_pinned = true;
include CFCORE_PATH . 'ui/entries_toolbar.php';
?>
<div class="form-extend-page-wrap">	
	<div id="form-entries-viewer"></div>
	<div class="tablenav caldera-table-nav" style="display:none;">
		
		<div class="tablenav-pages">
			<input title="<?php echo esc_attr( esc_html__( 'Entries per page', 'caldera-forms' ) ); ?>" id="cf-entries-list-items" type="number" value="<?php echo $entry_perpage; ?>" class="screen-per-page">
			<span class="pagination-links">
				<a href="#first" title="Go to the first page" data-page="first" class="first-page">«</a>
				<a href="#prev" title="Go to the previous page" data-page="prev" class="prev-page">‹</a>
				<span class="paging-input"><input type="text" size="1" name="paged" title="Current page" class="current-page"> of <span class="total-pages"></span></span>
				<a href="#next" title="Go to the next page" data-page="next" class="next-page">›</a>
				<a href="#last" title="Go to the last page" data-page="last" class="last-page">»</a>
			</span>
		</div>
	</div>
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
	var ready_limit_change;
	$(document).on('change', '#cf-entries-list-items', function(){
		if( ready_limit_change ){
			clearTimeout( ready_limit_change );
		}
		ready_limit_change = setTimeout( function(){
			$('.status_toggles.button-primary').trigger('click');	
		}, 280 );
		
	});	

})

</script>
<?php
include CFCORE_PATH . 'ui/entry_navigation.php';
?>
