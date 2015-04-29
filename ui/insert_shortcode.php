<div class="caldera-backdrop caldera-forms-insert-modal" style="display: none;"></div>
<div id="calderaf_forms_shortcode_modal" class="caldera-modal-wrap caldera-forms-insert-modal" style="display: none; width: 600px; max-height: 500px; margin-left: -300px;">
	<div class="caldera-modal-title" id="calderaf_forms_shortcode_modalTitle" style="display: block;">
		<a href="#close" class="caldera-modal-closer" data-dismiss="modal" aria-hidden="true" id="calderaf_forms_shortcode_modalCloser">×</a>
		<h3 class="modal-label" id="calderaf_forms_shortcode_modalLable"><?php echo __('Insert Caldera Form', 'caldera-forms'); ?></h3>
	</div>
	<div class="caldera-modal-body none" id="calderaf_forms_shortcode_modalBody">
		<div class="modal-body">
		<?php

			$forms = Caldera_Forms::get_forms();
			if(!empty($forms)){
				foreach($forms as $form_id=>$form){

					echo '<div class="modal-list-item"><label><input name="insert_form_id" autocomplete="off" class="selected-form-shortcode" value="' . $form_id . '" type="radio">' . $form['name'];
					if(!empty($form['description'])){
						echo '<span class="description"> '.$form['description'] .' </span>';
					}
					echo ' </label></div>';
					//dump($form,0);

				}
			}else{
				echo '<p>' . __('You don\'t have any forms to insert.', 'caldera-forms') .'</p>';
			}

		?>
		</div>
	</div>
	<div class="caldera-modal-footer" id="calderaf_forms_shortcode_modalFooter" style="display: block;">
	<?php if(!empty($forms)){ ?>
		<button class="button caldera-form-shortcode-insert"><?php echo __('Insert Form', 'caldera-forms'); ?></button>
	<?php }else{ ?>
		<button class="button caldera-modal-closer"><?php echo __('Close', 'caldera-forms'); ?></button>
	<?php } ?>
	</div>
</div>