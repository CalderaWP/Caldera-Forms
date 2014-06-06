<a class="add-new-h2 caldera-add-group caldera-add-row" href="#code_panels_tag"><?php echo __('Add Row', 'caldera-forms'); ?></a>
<div id="newfield-tool" class="button button-primary button-small layout-new-form-field" title="<?php echo __('Drag onto the form grid below', 'caldera-forms'); ?>">
	<i class="icon-edit" style="display:none;"></i>
	<i class="dashicons dashicons-menu" style="display:none;"></i>
	<span id="new-form-element" class="layout_field_name"><span class="dashicons dashicons-menu" style="margin: 1px 0px 0px -5px;"></span> <?php echo __('Add Element', 'caldera-forms'); ?></span>
	<div class="drag-handle">
		<div class="field_preview"></div>
	</div><input value="" type="hidden" class="field-location">	
</div>
<span id="dismiss-add-element" class="ajax-trigger" data-action="cf_dismiss_pointer" data-pointer="add_element"></span>
<?php
$haspointer = get_user_meta( get_current_user_id() , 'cf_pointer_add_element' );
if(empty($haspointer)){ ?>
<script>
	
	jQuery(document).ready( function($) {
		$( '#new-form-element' ).pointer( {
			content: '<h3>Form Elements & Fields</h3><p><img src="<?php echo CFCORE_URL . 'assets/images/howto.gif'; ?>"></p>',
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