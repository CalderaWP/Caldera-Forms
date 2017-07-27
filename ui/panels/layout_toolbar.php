&nbsp;
<a class="add-new-h2 caldera-add-group caldera-add-page ajax-trigger" 
	
	data-addtitle="<?php echo __('Page', 'caldera-forms'); ?>"
	data-template="#grid-page-tmpl"
	data-target-insert="append"
	data-request="add_new_grid_page"
	data-target="#grid-pages-panel"
	data-callback="add_page_grid"

 href="#code_panels_tag"><?php echo __('Add Page', 'caldera-forms'); ?></a>
<div id="newfield-tool" class="button button-primary button-small layout-new-form-field" title="<?php esc_attr_e( 'Drag this box onto the layout grid where you want the field to appear', 'caldera-forms'); ?>">
	<i class="icon-edit" style="display:none;"></i>
	<i class="dashicons dashicons-admin-page" style="display:none;"></i>
	<i class="dashicons dashicons-menu" style="display:none;"></i>
	<span id="new-form-element" class="layout_field_name"><span class="dashicons dashicons-move" style="margin: 1px 0px 0px -5px;"></span>
		<?php esc_html_e( 'Add Field', 'caldera-forms' ); ?>
	</span>
	<div class="drag-handle">
		<div class="field_preview"></div>
	</div><input value="" type="hidden" class="field-location">	
</div>

<?php
/*
<button class="button button-small compact-mode" style="margin-top:-3px; margin-left:10px;" type="button"><?php _e('Compact', 'caldera-forms'); ?></button>
*/
?>
<span id="dismiss-add-element" class="ajax-trigger" data-action="cf_dismiss_pointer" data-pointer="add_element" data-nonce="<?php echo esc_attr( wp_create_nonce( 'cf_dismiss_pointer' ) ); ?>"></span>
<?php
$haspointer = get_user_meta( get_current_user_id() , 'cf_pointer_add_element' );
if(empty($haspointer)){ ?>
<script>
	
	jQuery(document).ready( function($) {
		$( '#new-form-element' ).pointer( {
			content: '<h3><?php _e( 'Form Elements & Fields', 'caldera-forms' ); ?></h3><p><img src="<?php echo CFCORE_URL . 'assets/images/howto.gif'; ?>"></p>',
			position: {
				edge: 'top',
				align: 'left'
			},
			close: function() {
				$('#dismiss-add-element').trigger('click');
			}
		} ).pointer( 'open' );
	});

</script>
<?php } ?>
<script>
	jQuery(document).on('click','.compact-mode', function(){

		var form = jQuery('.caldera-forms-options-form'),
			clicked = jQuery(this);

		if(form.hasClass('mini-mode')){
			form.removeClass('mini-mode');
			clicked.removeClass('button-primary');
		}else{
			form.addClass('mini-mode');
			clicked.addClass('button-primary');
		}

	});
</script>