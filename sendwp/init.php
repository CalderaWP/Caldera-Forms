<?php

include_once CFCORE_PATH . 'sendwp/handler.php';

/**
 * Enqueue the SendWP JS SDK.
 */
function caldera_forms_admin_enqueue_sendwp_installer() {
    wp_enqueue_script('caldera_forms_sendwp_installer', plugins_url('installer.js', __FILE__));
    wp_localize_script('caldera_forms_sendwp_installer', 'sendwp_vars', [
       'nonce'  =>  wp_create_nonce( 'sendwp_install_nonce' ),
       'security_failed_message'    =>  esc_html__( 'Security failed to check sendwp_install_nonce', 'caldera-forms'),
       'user_capability_message'    =>  esc_html__( 'Ask an administrator for install_plugins capability', 'caldera-forms'),
       'sendwp_connected_message'   =>  esc_html__( 'SendWP is already connected.', 'caldera-forms'),
    ]);
}
add_action('caldera_forms_admin_main_enqueue', 'caldera_forms_admin_enqueue_sendwp_installer');