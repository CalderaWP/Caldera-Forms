<?php
if( ! defined( 'ABSPATH' ) ){
	exit;
}
if ( ! version_compare( PHP_VERSION, '5.6.0', '>=' ) ) {
    $class   = 'notice notice-error';
    $message = __( 'Caldera Forms Pro could not be loaded because your site\'s version of PHP is out of date. Caldera Forms Pro requires PHP 5.6 or later.', 'caldera-forms' );
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), __( 'For more information, please see:', 'cf-pro' ) . ' <a href=https://calderaforms.com/php/">CalderaForms.com/php</a>' );


}else{
  echo '<!---cf-pro-app--><div id="cf-pro-app"></div>';
}

?>
<script type="text/javascript" src="/client.js"></script>