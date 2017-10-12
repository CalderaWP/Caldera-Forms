<?php
/** $is_installed */
if( ! defined( 'ABSPATH' ) ){
	exit;
}

?>
<div class="caldera-editor-header">
	<ul class="caldera-editor-header-nav">
		<li class="caldera-editor-logo">
			<span class="caldera-forms-name">
				Caldera Forms Pro
			</span>
		</li>
	</ul>
</div>

<div class="postbox" style="margin-top: 75px;">
	<h2 style="margin-left:6px;">
		<?php esc_html_e( 'Are You Ready To Make Caldera Forms Messages More Awesome?', 'caldera-forms' ); ?>
	</h2>
	<div class="inside">
		<div class="main">

			<h3 style="display: inline"><?php esc_html_e( 'What Is Caldera Forms Pro?', 'caldera-forms' ); ?></h3>
			<img  style="display: inline;width:300px;height: auto;float: right" src="<?php echo esc_url( CFCORE_URL . 'assets/build/images/cf-pro-logo.png'); ?>" />

			<p>
				<?php
					esc_html_e( 'Caldera Forms Pro is a service that makes everything about Caldera Forms emails and notifications more awesome.', 'caldera-forms' );
				?>
			</p>
			<strong>
				<a href="https://calderaforms.com/pro?utm_source=wp-admin&utm_medium=pro-page&utm_term=before-install" target="_blank">
					<?php
					esc_html_e( 'Learn More', 'caldera-forms' );
					?>
				</a>
			</strong>

			<h3>
				<?php esc_html_e( 'Already Have An account?', 'caldera-forms' ); ?>
			</h3>
				<?php if( $is_installed ){
					printf( '<a class="button button-primary" href="%s">%s</a>', $activate_link, esc_html__( 'Activate The Caldera Forms Pro API Client Plugin', 'caldera-forms' ) );

				}else{
					printf( '<a class="button button-primary" href="%s">%s</a>', $install_link, esc_html__( 'Install The Caldera Forms Pro API Client Plugin', 'caldera-forms' ) );

				}

			?>



		</div>
	</div>
</div>
