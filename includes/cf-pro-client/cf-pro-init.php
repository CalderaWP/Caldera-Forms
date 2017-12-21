<?php

/**
 * Start Caldera Forms Pro API client
 *
 * NOTE: This file is included directly and MUST be PHP5.2 compatible. Hence why boostrap-cf-pro.php is a separate, non-PHp 5.2 compatible file.
 */
add_action( 'caldera_forms_includes_complete', 'caldera_forms_pro_client_init', 1 );
remove_action( 'caldera_forms_includes_complete', 'caldera_forms_pro_init', 2 );


function caldera_forms_pro_client_init(){
	if ( ! version_compare( PHP_VERSION, '5.6.0', '>=' ) ) {
		add_action( 'admin_notices', 'caldera_forms_pro_version_fail_warning' );

		function caldera_forms_pro_version_fail_warning(){
			$class   = 'notice notice-error';
			$message = __( 'Caldera Forms Pro could not be loaded because your PHP is out of date. Caldera Forms Pro requires PHP 5.6 or later.', 'cf-pro' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		}

	} else {
		if ( ! defined( 'CF_PRO_VER' ) ) {

			/**
			 * Define Plugin basename for updater
			 *
			 * @since 0.2.0
			 */
			define( 'CF_PRO_BASENAME', plugin_basename( __FILE__ ) );

			/**
			 * Caldera Forms Pro Client Version
			 */
			define( 'CF_PRO_VER', '1.1.1' );

			include_once dirname( __FILE__ ) . '/bootstrap-cf-pro.php';

		    register_activation_hook( __FILE__, 'caldera_forms_pro_activation_hook_callback' );

		}

	}
}
