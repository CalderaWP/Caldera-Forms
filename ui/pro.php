<?php
/**
 * This file is used to create cf-pro admin page WHEN CF PRO CAN NOT BE USED
 */
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

<div class="postbox" style="margin-top: 75px;padding: 8px;">
	<?php
        $message = __( 'Caldera Forms Pro could not be loaded because your site\'s version of PHP is out of date. Caldera Forms Pro requires PHP 5.6 or later.', 'caldera-forms' );
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-error' ), esc_html( $message ) );
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-warning' ), __( 'For more information, please see: ', 'cf-pro' ) . ' <a href="https://calderaforms.com/php?utm_source=wp-admin&utm_keyword=php_version">CalderaForms.com/php</a>' );
    ?>
</div>
