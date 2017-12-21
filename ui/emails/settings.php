<?php
/**
 * Settings for Caldera Forms emails
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
?>
<div id="cf-email-settings-ui" aria-hidden="true" style="visibility: hidden;">
	<div style="margin:20px;">
		<div class="caldera-forms-clippy-zone-inner-wrap" style="background: white">
			<div class="caldera-forms-clippy"
			     style="background-color:white;border-left: 4px solid #dc3232;">
				<h2 style="margin-left: 12px">
					<?php esc_html_e( 'Deprecated Functionality', 'caldera-forms' ); ?>
				</h2>
				<p style="margin-left: 12px">
					<?php esc_html_e( 'SendGrid integration was our first attempt to improve email in Caldera Forms. But we decided to create something easier and provides more value to you -- our users. We call it Caldera Forms Pro.', 'caldera-forms' ); ?>
				</p>
				<a href="https://calderaforms.com/pro?utm-source=wp-admin&utm_campaign=email-settings&utm_term=email-setting"
				   target="_blank" class="bt-btn btn btn-green" style="width: 80%;margin-left:5%;">
					<?php esc_html_e( 'Learn More', 'caldera-forms' ); ?>
				</a>
			</div>
		</div>
	</div>
	<div class="cf-emails-field-group caldera-config-group" id="cf-emails-api-wrap">
		<label for="cf-emails-api" id="cf-emails-api-label">
			<?php esc_html_e( 'Email System', 'caldera-forms' ); ?>
		</label>
		<div class="cf-emails-field">
			<select class="cf-email-settings" id="cf-emails-api" aria-labelledby="cf-emails-api-label" aria-describedby="cf-emails-api-desc">
				<option value="wp" <?php if ( 'wp'  == Caldera_Forms_Email_Settings::get_method() ) : echo 'selected'; endif; ?> >
					<?php esc_html_e( 'WordPress', 'caldera-forms' ); ?>
				</option>
				<option value="sendgrid" <?php if ( 'sendgrid'  == Caldera_Forms_Email_Settings::get_method() ) : echo 'selected'; endif; ?> >
					<?php esc_html_e( 'SendGrid', 'caldera-forms' ); ?>
				</option>
				<option value="caldera" <?php if ( 'caldera'  == Caldera_Forms_Email_Settings::get_method() ) : echo 'selected'; endif; ?> disabled >
					<?php esc_html_e( 'Caldera (coming soon)', 'caldera-forms' ); ?>
				</option>
			</select>
		</div>
		<p class="description" id="cf-emails-api-desc" style="max-width: 440px; margin-bottom: 12px;">
			<?php esc_html_e( 'By default Caldera Forms uses WordPress to send emails. You can choose to use another method to increase reliability of emails and reduce server load.', 'caldera-forms' ); ?>
		</p>
	</div>
	<div class="cf-emails-field-group caldera-config-group" id="cf-emails-sendgrid-key-wrap">
		<label for="cf-emails-sendgrid-key" id="cf-emails-sendgrid-key-label">
			<?php esc_html_e( 'SendGrid API Key', 'caldera-forms' ); ?>
		</label>
		<div class="cf-emails-field-group">
			<input type="text" class="cf-email-settings" id="cf-emails-sendgrid-key" name="cf-emails-sendgrid-key" value="<?php echo esc_attr( Caldera_Forms_Email_Settings::get_key( 'sendgrid' ) ); ?>">
		</div>
		<p class="description" id="cf-emails-sendgrid-key-desc" style="max-width: 440px; margin-bottom: 12px;">
		<?php printf( '<span>%s</span> <span><a href="%s" target="_blank" rel="nofollow" title="%s">%s</a></span>',
					esc_html__( 'SendGrid API Key', 'caldera-forms' ),
					'https://CalderaWP.com/docs/configure-sendgrid',
					esc_attr__( 'Documentation for configuring SendGrid API', 'caldera-forms' ),
					esc_html__( 'Learn More', 'caldera-forms' )
				);
		?></p>

	</div>
	<?php echo Caldera_Forms_Email_Settings::nonce_field(); ?>
	<br><br>
	<div class="field-group">
		<button type="button" id="cf-email-settings-save" class="button button-primary">
			<?php esc_html_e( 'Save Email Settings', 'caldera-forms' ); ?>
		</button>
		<span class="spinner" style="float:none;" id="cf-email-spinner" aria-hidden="true"></span>
	</div>
</div>
