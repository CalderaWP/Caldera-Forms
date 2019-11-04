<?php

if(!isset($element['mailer']['sender_name'])){
	$element['mailer']['sender_name'] = __('Caldera Forms Notification', 'caldera-forms');
}
if(!isset($element['mailer']['sender_email'])){
	$element['mailer']['sender_email'] = Caldera_Forms_Email_Fallback::get_fallback( $element );
}
if(!isset($element['mailer']['email_type'])){
	$element['mailer']['email_type'] = 'html';
}
if(!isset($element['mailer']['recipients'])){
	$element['mailer']['recipients'] = '';
}
if(!isset($element['mailer']['email_subject'])){
	$element['mailer']['email_subject'] = $element['name'];
}
if(!isset($element['mailer']['email_message'])){
	$element['mailer']['email_message'] = '{summary}';
}

// backwords-compat
if ( ! empty( $element['mailer']['enable_mailer'] ) ) {
	$element['mailer']['on_insert'] = 1;
}


?>
<div class="mailer-control-panel wrapper-instance-pane">

	<div class="caldera-config-group">
		<label class="screen-reader-text"><?php esc_html_e('Use The Mailer', 'caldera-forms'); ?> </label>
		<div class="caldera-config-field">
			<div style="width:100%;text-align:center;" class="toggle_processor_event">

				<label style="width: 100%;" title="<?php echo esc_attr( __( 'Enable Or Disable Mailer', 'caldera-forms') ); ?>" class="button button-small <?php if( !empty( $element['mailer']['on_insert'] ) ){ echo 'activated'; } ?>"><input type="checkbox" style="display:none;" value="1" name="config[mailer][on_insert]" <?php if( !empty( $element['mailer']['on_insert'] ) ){ echo 'checked="checked"'; } ?>>
				<span class="is_active" style="width: 100%;<?php if( empty( $element['mailer']['on_insert'] ) ){ ?> display:none;visibility: hidden;<?php } ?>"><?php esc_html_e( 'Disable Mailer', 'caldera-forms' ); ?></span>
				<span class="not_active" style="width: 100%;<?php if( !empty( $element['mailer']['on_insert'] ) ){ ?> display:none;visibility: hidden;<?php } ?>"><?php esc_html_e( 'Enable Mailer', 'caldera-forms' ); ?></span>
				</label>
			</div>
		</div>
	</div>

	<div class="mailer_config_panel caldera-config-processor-notice" style="display:<?php if( empty( $element['mailer']['on_insert'] ) && empty( $element['mailer']['on_insert'] ) ){ ?> block;<?php }else{ ?>none;<?php }?>clear: both; padding: 20px 0px 0px;width:550px;">
		<p style="padding:12px; text-align:center;background:#e7e7e7;" class="description"><?php _e('Mailer is currently disabled', 'caldera-forms'); ?></p>
	</div>

	<div class="mailer_config_panel caldera-config-processor-setup" <?php if( empty( $element['mailer']['on_insert'] ) && empty( $element['mailer']['on_insert'] ) ){ echo 'style="display:none;"'; } ?>>
		<div class="caldera-config-group">
			<label for="cf-email-from-name">
				<?php  esc_html_e( 'From Name', 'caldera-forms' ); ?> 
			</label>
			<div class="caldera-config-field">
				<input type="text" class="field-config magic-tag-enabled" name="config[mailer][sender_name]" value="<?php echo $element['mailer']['sender_name']; ?>" style="width:400px;" id="cf-email-from-name" aria-describedby="cf-email-from-name-description" >
				<p class="description" id="cf-email-from-name-description">
					<?php esc_html_e( 'Name for email sender', 'caldera-forms'); ?>
				</p>
			</div>
		</div>


        <div class="caldera-config-group">
            <label for="cf-email-from-email" class="no-pro-enhanced">
                <?php esc_html_e('From Email', 'caldera-forms'); ?>
            </label>
            <label for="cf-email-from-email" class="pro-enhanced">
                <?php esc_html_e('Reply To Email', 'caldera-forms'); ?>
            </label>

            <div class="caldera-config-field">
                <input type="email" class="field-config" name="config[mailer][sender_email]" value="<?php echo $element['mailer']['sender_email']; ?>" style="width:400px;" id="cf-email-from-email" aria-describedby="cf-email-from-email-description">
                <p class="description no-pro-enhanced" id="cf-email-from-email-description">
                    <?php esc_html_e( 'Email Address for sender. If you want to use a form field use the "Reply To Email" setting below.', 'caldera-forms'); ?>
                    <strong><?php esc_html_e( 'Do Not Use A Magic Tag', 'caldera-forms' ); ?>.</strong>
                </p>
                <p class="description pro-enhanced" id="cf-email-from-email-description">
                    <?php esc_html_e('The email address of the person filling in the form. This will allow replies to the email to go to the sender.', 'caldera-forms'); ?>
                </p>
            </div>
        </div>

        <div class="caldera-config-group no-pro-enhanced">
            <label for="cf-email-from-replyto">
                <?php esc_html_e('Reply To Email', 'caldera-forms'); ?>
            </label>
            <div class="caldera-config-field">
                <input type="text" class="field-config magic-tag-enabled" name="config[mailer][reply_to]" value="<?php if(isset( $element['mailer']['reply_to'] ) ){ echo $element['mailer']['reply_to']; } ?>" style="width:400px;" id="cf-email-from-replyto" aria-describedby="cf-email-from-replyto-description">
                <p class="description" id="cf-email-from-replyto-description">
                    <?php esc_html_e('The email address of the person filling in the form. This will allow replies to the email to go to the sender.', 'caldera-forms'); ?>
                </p>
            </div>
        </div>

        <div class="caldera-config-group">
			<label for="cf-email-type">
				<?php esc_html_e('Email Type', 'caldera-forms'); ?>
			</label>
			<div class="caldera-config-field" id="cf-email-type">
				<select class="field-config" name="config[mailer][email_type]">
				<option value="html" <?php if($element['mailer']['email_type'] == 'html'){ echo 'selected="selected"'; } ?>>HTML</option>
				<option value="text" <?php if($element['mailer']['email_type'] == 'text'){ echo 'selected="selected"'; } ?>>Text</option>
				</select>
			</div>
		</div>
		<div class="caldera-config-group">
			<label>
				<?php esc_html_e('CSV Include', 'caldera-forms'); ?>
			</label>
			<div class="caldera-config-field">
				<label>
					<input type="checkbox" class="field-config" name="config[mailer][csv_data]" value="1" <?php if(isset($element['mailer']['csv_data'])){ echo 'checked="checked";'; } ?>>
					<?php esc_html_e('Attach a CSV version of the submission', 'caldera-forms'); ?>
				</label>
			</div>
		</div>


		<div class="caldera-config-group">
			<label for="cf-email-recipients">
				<?php esc_html_e('Email Recipients', 'caldera-forms'); ?> </label>
			<div class="caldera-config-field">
				<input type="text" class="field-config magic-tag-enabled" name="config[mailer][recipients]" value="<?php echo $element['mailer']['recipients']; ?>" style="width:400px;" id="cf-email-recipients" aria-describedby="cf-email-recipients-description" />
				<p class="description" id="cf-email-recipients-description">
					<?php esc_html_e( 'Who to send email to? Use a comma separated list of email addresses to send to more than one address.', 'caldera-forms'); ?>
				</p>
			</div>

		</div>
		<div class="caldera-config-group">
			<label for="cf-email-bcc">
				<?php esc_html_e('BCC', 'caldera-forms'); ?>
			</label>
			<div class="caldera-config-field">
				<input type="text" class="field-config magic-tag-enabled" name="config[mailer][bcc_to]" value="<?php if(isset( $element['mailer']['bcc_to'] ) ){ echo $element['mailer']['bcc_to']; } ?>" style="width:400px;" id="cf-email-bcc" aria-describedby="cf-email-bcc-description" />
				<p class="description" id="cf-email-bcc-description">
					<?php esc_html_e('Comma separated list of email addresses to send a BCC to.', 'caldera-forms'); ?>
				</p>
			</div>
		</div>

		<div class="caldera-config-group">
			<label for="cf-email-subject">
				<?php esc_html_e('Email Subject', 'caldera-forms'); ?>
			</label>
			<div class="caldera-config-field">
				<input type="text" class="field-config magic-tag-enabled" name="config[mailer][email_subject]" value="<?php echo $element['mailer']['email_subject']; ?>" style="width:400px;" id="cf-email-subject" aria-describedby="cf-email-subject-description">
				<p class="description" id="cf-email-subject-description">
					<?php esc_html_e('Use %field_slug% to use a value from the form', 'caldera-forms'); ?>
				</p>
			</div>
		</div>
		<div class="caldera-config-group">
			<label for="mailer_email_message">
				<?php esc_html_e('Email Message', 'caldera-forms'); ?> </label>
			<div class="caldera-config-field" style="max-width: 600px;">
				<?php wp_editor( $element['mailer']['email_message'], "mailer_email_message", array(
					'textarea_name' => 'config[mailer][email_message]') );
				?>
				<p class="description">
					<?php esc_html_e('Magic tags, %field_slug% are replaced with submitted data. Use {summary} to build an automatic mail based on form content. Leaving the mailer blank, will create an automatic summary.', 'caldera-forms'); ?>
				</p>
			</div>
		</div>


		<?php
		/**
		 * Runs below the mail message field in email notifciation tab
		 *
		 * @since unknown
		 *
		 * @param array $element Form config
		 */
		do_action( 'caldera_forms_mailer_config', $element );
		?>
		

		<div class="caldera-config-group">
			<label for="preview_email" id="preview_email-label">
				<?php esc_html_e( 'Save Preview', 'caldera-forms'); ?>
			</label>
			<div class="caldera-config-field">
				<label>
					<input type="checkbox" id="preview_email" class="field-config cf-email-preview-toggle" value="1" name="config[mailer][preview_email]"  aria-describedby="preview_email-description" aria-labelledby="preview_email-label" <?php if(!empty($element['mailer']['preview_email'])){ echo 'checked="checked";'; } ?>>
					<span id="preview_email-description">
						<?php esc_html_e( 'Allows you to preview the message and who the message is sent to, as well as the subject. You should turn this off when not testing.', 'caldera-forms'); ?>
					</span>
				</label>
			</div>
		</div>

		<div class="caldera-config-group">
			<label>
				<?php esc_html_e('Debug Mailer', 'caldera-forms'); ?>
			</label>
			<div class="caldera-config-field">
				<label><input type="checkbox" value="1" name="config[debug_mailer]" class="field-config"<?php if(isset($element['debug_mailer'])){ echo ' checked="checked"'; } ?>> <?php esc_html_e('Enable email send transaction log', 'caldera-forms'); ?></label>
				<p class="description"><?php esc_html_e('If set, entries will have a "Mailer Debug" meta tab to see the transaction log. Do not keep this enabled on production as it sends two emails for tracking.', 'caldera-forms'); ?></p>
				<p class="description">
					<?php echo sprintf( esc_html( 'If you are having email issues, we strongly recommend %sSendWP%s.', 'caldera-forms' ), '<a href="https://sendwp.com?utm_source=Caldera+Forms+Plugin&utm_medium=Forms_Edit+Forms_Email&utm_campaign=SendWP+banner+ad" target="_blank" rel="nofollow">', '</a>' ); ?>
				</p>

				<a href="https://sendwp.com?utm_source=Caldera+Forms+Plugin&utm_medium=Forms_Edit+Forms_Email&utm_campaign=SendWP+banner+ad" target="_blank" rel="nofollow" style="text-decoration:none;">
					<div class="mailer_config_panel caldera-config-processor-notice" style="clear: both; padding: 20px 0px 0px;width:550px;">
						<p style="padding:12px;text-align:center;color:white;background:#21394a;" class="description">
							<?php echo sprintf( esc_html__('%sSendWP%s - Fix Your WordPress Email%sThe easy solution to transactional email in WordPress', 'caldera-forms'), '<strong>', '</strong>', '<br />' ); ?>
						</p>
					</div>
				</a>

			</div>
		</div>

	</div>
</div>

<?php //Set Different From email and Reply-to text depending on Pro delivery status of the form
    if( caldera_forms_pro_is_active() === true ) {

        $enhanced_delivery = \calderawp\calderaforms\pro\container::get_instance()->get_settings()->get_enhanced_delivery();

        if( $enhanced_delivery === true ) {

            $send_local = \calderawp\calderaforms\pro\container::get_instance()->get_settings()->get_form( $element['ID'] )->get_send_local();
            ?>
            <script type="text/javascript">
              var cfId = "<?php echo $element['ID'] ?>";
              var $check = jQuery("<input id='cf-pro-send-local-" + cfId + "' type='checkbox'/>" );
            </script>
            <?php
             if( $send_local === false ) { ?>
                <script type="text/javascript">
                    jQuery($check).prop('checked', false)
                </script>
            <?php } else if ( $send_local === true ) { ?>
                <script type="text/javascript">
                    jQuery($check).prop('checked', true);
                </script>
            <?php } ?>

            <script type="text/javascript">

                jQuery(function ($) {
                  var checkProStatus = function () {
                    if ( $check.prop("checked") === true) {
						$(".pro-enhanced").show().attr('aria-hidden', false);
						$(".no-pro-enhanced").hide().attr('aria-hidden', true);
                    } else {
						$(".pro-enhanced").hide().attr('aria-hidden', true);
						$(".no-pro-enhanced").show().attr('aria-hidden', false);
                    }
                  };

                  jQuery(function ($) {
                      $( 'body' ).on( 'change', $check, function(e) {
                        e.preventDefault();
                        if( $( $check ).prop('checked') !== true ){
                          $($check).prop('checked', true);
                        } else if( $( $check ).prop('checked') !== false ) {
                          $($check).prop('checked', false);
                        }
						checkProStatus();
                      });
                  });

                  $('.caldera-forms-options-form').on('click', '#tab_mailer', function() {
                    checkProStatus();
                  });

                  checkProStatus();
                });

            </script>

        <?php } else { ?>
            <script type="text/javascript">
              jQuery(".pro-enhanced").hide().attr('aria-hidden', true);
              jQuery(".no-pro-enhanced").show().attr('aria-hidden', false);
            </script>
        <?php }

    } else { ?>

        <script type="text/javascript">
          jQuery(".pro-enhanced").hide().attr('aria-hidden', true);
          jQuery(".no-pro-enhanced").show().attr('aria-hidden', false);
        </script>
    <?php } ?>

<script type="text/javascript">
	
    jQuery('body').on('change', '#mailer_status_select', function(){
        var status = jQuery(this);

        if(status.val() === '0'){
            jQuery('.mailer_config_panel').slideUp(100);
        }else{
            jQuery('.mailer_config_panel').slideDown(100);
        }
    });

</script>






