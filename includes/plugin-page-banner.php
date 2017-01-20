<?php
add_action('admin_init', 'caldera_forms_notice_ignore');
add_action('admin_notices', 'caldera_forms_activation_admin_notice');

/**
 *Output plugin page banner
 *
 * @since 1.4.4
 * @uses "admin_notices" action
 */
function caldera_forms_activation_admin_notice() {

	$user_id = get_current_user_id();

	global $pagenow;

	if ( $pagenow == 'plugins.php' ) {

		if ( ! get_user_meta($user_id, 'caldera_forms_activation_ignore_notice')) { ?>
			<style>div.updated.caldera_forms,
				div.updated.caldera_forms header,
				div.updated.caldera_forms header img,
				div.updated.caldera_forms header h3,
				div.updated.caldera_forms .dismiss,
				.caldera_forms-banner-actions,
				.caldera_forms-banner-action,
				.caldera_forms-banner-action #mc_embed_signup,
				div.updated.caldera_forms .caldera_forms-banner-action span.dashicons:before {
					-webkit-box-sizing: border-box;
					/* Safari/Chrome, other WebKit */
					-moz-box-sizing: border-box;
					/* Firefox, other Gecko */
					box-sizing: border-box;
					/* Opera/IE 8+ */
					width: 100%;
					position: relative;
					padding: 0;
					margin: 0;
					overflow: hidden;
					float: none;
					display: block;
					text-align: left;
				}
				.caldera_forms-banner-action a,
				.caldera_forms-banner-action a:hover,
				div.updated.caldera_forms .caldera_forms-banner-action.mailchimp:hover,
				div.updated.caldera_forms .caldera_forms-banner-action.mailchimp span {
					-webkit-transition: all 500ms ease-in-out;
					-moz-transition: all 500ms ease-in-out;
					-ms-transition: all 500ms ease-in-out;
					-o-transition: all 500ms ease-in-out;
					transition: all 500ms ease-in-out;
				}
				div.updated.caldera_forms {
					margin: 1rem 0 2rem 0;
				}
				div.updated.caldera_forms header h3 {
					line-height: 1.4;
				}
				@media screen and (min-width: 280px) {
					div.updated.caldera_forms {
						border: 0px;
						background: transparent;
						-webkit-box-shadow: 0 1px 1px 1px rgba(0, 0, 0, 0.1);
						box-shadow: 0 1px 1px 1px rgba(0, 0, 0, 0.1);
					}
					div.updated.caldera_forms header {
						background: #fff;
						color: #93af51;
						position: relative;
						height: 5rem;
					}
					div.updated.caldera_forms header img {
						display: none;
						max-width: 130px;
						margin: 0 0 0 1rem;
						float: left;
					}
					div.updated.caldera_forms header h3 {
						float: left;
						max-width: 60%;
						margin: 1rem;
						display: inline-block;
						color: #93af51;
					}
					div.updated.caldera_forms header h3 span {
						color: #38383A;
						font-weight: 900;
						font-family: 'Open Sans Black', 'Open Sans Regular', Verdana, Helvetica, sans-serif;
					}
					div.updated.caldera_forms a.dismiss {
						display: block;
						position: absolute;
						left: auto;
						top: 0;
						bottom: 0;
						right: 0;
						width: 6rem;
						background: #a3bf61;
						color: #fff;
						text-align: center;
					}
					.caldera_forms a.dismiss:before {
						font-family: 'Dashicons';
						content: "\f153";
						display: inline-block;
						position: absolute;
						top: 50%;

						transform: translate(-50%);
						right: 40%;
						margin: auto;
						line-height: 0;
					}
					div.updated.caldera_forms a.dismiss:hover {
						color: #777;
						background: #4b4b4b;
					}

					/* END ACTIVATION HEADER
					 * START ACTIONS
					 */
					div.updated.caldera_forms .caldera_forms-banner-action {
						display: table;
					}
					.caldera_forms-banner-action a,
					.caldera_forms-banner-action #mc_embed_signup {
						background: rgba(0,0,0,.1);
						color: rgba(51, 51, 51, 1);
						padding: 0 1rem 0 6rem;
						height: 4rem;
						display: table-cell;
						vertical-align: middle;
					}
					.caldera_forms-banner-action.mailchimp {
						margin-bottom: -1.5rem;
						top: -.5rem;
					}
					.caldera_forms-banner-action.mailchimp p {
						margin: 9px 0 0 0;
					}

					.caldera_forms-banner-action #mc_embed_signup form {
						display: inline-block;
					}

					div.updated.caldera_forms .caldera_forms-banner-action:hover {
						background-color: #cfcfcf;
						color: #fff !important;
					}

					div.updated.caldera_forms .caldera_forms-banner-action a:hover {
						color: #fff;
					}


					div.updated.caldera_forms .caldera_forms-banner-action span {
						display: block;
						position: absolute;
						left: 0;
						top: 0;
						bottom: 0;
						height: 100%;
						width: auto;
					}
					div.updated.caldera_forms .caldera_forms-banner-action span.dashicons:before {
						padding: 2rem 1rem;
						color: #ff7e30;
						line-height: 0;
						top: 50%;
						transform: translateY(-50%);
						background: rgba(163, 163, 163, .25);
					}
					div.updated.caldera_forms .caldera_forms-banner-action a:hover,
					div.updated.caldera_forms .caldera_forms-banner-action.mailchimp:hover {
						background: rgba(0,0,0,.2);
					}
					div.updated.caldera_forms .caldera_forms-banner-action a {
						text-decoration: none;
					}

					div.updated.caldera_forms .caldera_forms-banner-action a,
					div.updated.caldera_forms .caldera_forms-banner-action #mc_embed_signup {
						position: relative;
						overflow: visible;
					}
					.caldera_forms-banner-action #mc_embed_signup form,
					.caldera_forms-banner-action #mc_embed_signup form input#mce-EMAIL {
						width: 100%;
					}
					div.updated.caldera_forms .mailchimp form input#mce-EMAIL + input.submit-button {
						display: block;
						position: relative;
						top: -1.75rem;
						float: right;
						right: 4px;
						border: 0;
						background: #cccccc;
						border-radius: 2px;
						font-size: 10px;
						color: white;
						cursor: pointer;
					}

					div.updated.caldera_forms .mailchimp form input#mce-EMAIL:focus + input.submit-button {
						background: #93af51;
					}

					.caldera_forms-banner-action #mc_embed_signup form input#mce-EMAIL div#placeholder,
					input#mce-EMAIL:-webkit-input-placeholder {opacity: 0;}
				}
				@media screen and (min-width: 780px) {
					div.updated.caldera_forms header h3 {line-height: 3;}

					div.updated.caldera_forms .mailchimp form input#mce-EMAIL + input.submit-button {
						top: -1.55rem;
					}
					div.updated.caldera_forms header img {
						display: inline-block;
					}
					div.updated.caldera_forms header h3 {
						max-width: 50%;
					}
					.caldera_forms-banner-action {
						width: 30%;
						float: left;
					}
					div.updated.caldera_forms .caldera_forms-banner-action a {

					}
					.caldera_forms-banner-action a,
					.caldera_forms-banner-action #mc_embed_signup {
						padding: 0 1rem 0 4rem;
					}
					div.updated.caldera_forms .caldera_forms-banner-action span.dashicons:before {

					}
					div.updated.caldera_forms .caldera_forms-banner-action.mailchimp {
						width: 40%;
					}
				}</style>

			<div class="updated caldera_forms">
				<header>
					<img src="<?php echo CFCORE_URL; ?>/assets/images/new-icon.png"  class="caldera_forms-logo" style="width:80px;hieght:80px;"/>

					<h3><?php esc_html_e( 'Thanks for installing Caldera Forms','caldera-forms'); ?></h3>

					<?php printf('<a href="%1$s" class="dismiss"></a>', '?caldera_forms_notice_ignore=0'); ?>
				</header>
				<div class="caldera_forms-banner-actions">

					<div class="caldera_forms-banner-action">
						<a href="https://calderaforms.com/getting-started?utm_source=wordpress&utm_medium=plugin-page&utm_term=v<?php echo CFCORE_VER; ?>&utm_campaign=post-install-banner">
							<span class="dashicons dashicons-admin-settings"></span>
							<?php esc_html_e('Get Started','caldera-forms'); ?>
						</a>
					</div>

					<div class="caldera_forms-banner-action">
						<a href="https://calderaforms.com/caldera-forms-add-ons?utm_source=wordpress&utm_medium=plugin-page&utm_term=v<?php echo CFCORE_VER; ?>&utm_campaign=post-install-banner" target="_blank">
							<span class="dashicons dashicons-download"></span>
							<?php esc_html_e('Go Further','caldera-forms'); ?>
						</a>
					</div>

					<div class="caldera_forms-banner-action mailchimp">
						<div id="mc_embed_signup">
							<span class="dashicons dashicons-edit"></span>
							<form action="//CalderaWP.us10.list-manage.com/subscribe/post?u=e8aeee202b02c1fe9eab2037c&amp;id=f402a6993d" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
								<div class="mc-field-group">
									<p><small>
											<?php esc_html_e('Get notified of plugin updates:','caldera-forms'); ?>
										</small></p>
									<input type="email" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
									<input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="submit-button" style="background-color:#ff7e30">

								</div>
								<div id="mce-responses" class="clear">
									<div class="response" id="mce-error-response" style="display:none"></div>
									<div class="response" id="mce-success-response" style="display:none"></div>
								</div>

							</form>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}
}




/**
 * Track dismissals of Caldera Forms plugin page banners
 *
 * @since 1.4.4
 * @uses "admin_init"
 */
function caldera_forms_notice_ignore() {
	$user_id = get_current_user_id();

	if ( isset($_GET['caldera_forms_notice_ignore']) && '0' == $_GET['caldera_forms_notice_ignore'] ) {
		add_user_meta($user_id, 'caldera_forms_activation_ignore_notice', 'true', true);
	}
}

