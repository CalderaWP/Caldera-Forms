<div class="caldera-backdrop caldera-forms-insert-modal" style="display: none;"></div>
<form id="calderaf_forms_shortcode_modal" class="caldera-modal-wrap caldera-forms-insert-modal" style="display: none; width: 700px; max-height: 500px; margin-left: -350px;">
	<div class="caldera-modal-title" id="calderaf_forms_shortcode_modalTitle" style="display: block;">
		<a href="#close" class="caldera-modal-closer" data-dismiss="modal" aria-hidden="true" id="calderaf_forms_shortcode_modalCloser">×</a>
		<h3 class="modal-label" id="calderaf_forms_shortcode_modalLable"><?php echo __('Insert Caldera Form', 'caldera-forms'); ?></h3>
	</div>
	<div class="caldera-modal-body none" id="calderaf_forms_shortcode_modalBody" style="width: 70%;">
		<div class="modal-body modal-forms-list">
		<?php

			$forms = Caldera_Forms_Forms::get_forms( true );
			if(!empty($forms)){
				foreach($forms as $form_id=>$form){

					echo '<div class="modal-list-item"><label><input name="insert_form_id" autocomplete="off" class="selected-form-shortcode" value="' . $form_id . '" type="radio">' . $form['name'];
					if(!empty($form['description'])){
						echo '<p style="margin-left: 20px;" class="description"> '.$form['description'] .' </p>';
					}
					echo ' </label></div>';

				}
			}else{
				echo '<p>' . __('You don\'t have any forms to insert.', 'caldera-forms') .'</p>';
			}

		?>
		</div>
	</div>
	<div class="caldera-modal-body none" id="calderaf_forms_shortcode_modalBody_options" style="left: 70%;">
		<div class="modal-body modal-shortcode-options">
			<h4><?php esc_html_e('Options', 'caldera-forms'); ?></h4>
			<label><input type="checkbox" value="1" class="set_cf_option set_cf_modal"> <?php esc_html_e('Set as Modal', 'caldera-forms'); ?></label>
			<div class="modal-forms-setup" style="display:none;">
				<label><?php esc_html_e('Open Modal Trigger Type', 'caldera-forms'); ?></label>
				<select name="modal_button_type" class="modal_trigger_type" style="width: 100%;">
					<option value="link"><?php esc_html_e('Link', 'caldera-forms'); ?></option>
					<option value="button"><?php esc_html_e('Button', 'caldera-forms'); ?></option>					
				</select>
				<label><?php esc_html_e('Open Modal Text', 'caldera-forms'); ?></label>
				<input type="text" name="modal_button_text" class="modal_trigger" style="width: 100%;">
				<label><?php esc_html_e('Modal Width', 'caldera-forms'); ?></label>
				<input type="number" name="modal_width" class="modal_width" style="width: 60px;">px

			</div>

		</div>

	</div>
	<div class="caldera-modal-footer" id="calderaf_forms_shortcode_modalFooter" style="display: block;">
	<?php if(!empty($forms)){ ?>		
		<button class="button caldera-form-shortcode-insert" style="float:right;"><?php esc_html_e('Insert Form', 'caldera-forms'); ?></button>
	<?php }else{ ?>
		<button class="button caldera-modal-closer"><?php echo __('Close', 'caldera-forms'); ?></button>
	<?php } ?>
	</div>
</form>
