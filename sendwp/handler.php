<?php

add_action( 'wp_ajax_caldera_forms_sendwp_remote_install', 'wp_ajax_caldera_forms_sendwp_remote_install_handler' );
function wp_ajax_caldera_forms_sendwp_remote_install_handler () {
    $all_plugins = get_plugins();
    $is_sendwp_installed = false;
    foreach(get_plugins() as $path => $details ) {
        if(false === strpos($path, '/sendwp.php')) continue;
        $is_sendwp_installed = true;
        activate_plugin( $path );
        break;
    }

    if( ! $is_sendwp_installed ) {

        $plugin_slug = 'sendwp';

        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        
        /*
        * Use the WordPress Plugins API to get the plugin download link.
        */
        $api = plugins_api( 'plugin_information', array(
            'slug' => $plugin_slug,
        ) );
        if ( is_wp_error( $api ) ) {
            ob_end_clean();
            echo json_encode( array( 'error' => $api->get_error_message(), 'debug' => $api ) );
            exit;
        }
        
        /*
        * Use the AJAX Upgrader skin to quietly install the plugin.
        */
        $upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
        $install = $upgrader->install( $api->download_link );
        if ( is_wp_error( $install ) ) {
            ob_end_clean();
            echo json_encode( array( 'error' => $install->get_error_message(), 'debug' => $api ) );
            exit;
        }
        
        /*
        * Activate the plugin based on the results of the upgrader.
        * @NOTE Assume this works, if the download works - otherwise there is a false positive if the plugin is already installed.
        */
        $activated = activate_plugin( $upgrader->plugin_info() );

    }

    /*
     * Final check to see if SendWP is available.
     */
    if( ! function_exists('sendwp_get_server_url') ) {
        ob_end_clean();
        echo json_encode( array(
            'error' => __( 'Something went wrong. SendWP was not installed correctly.' ),
            'install' => $install,
            ) );
        exit;
    }
    
    echo json_encode( array(
        'partner_id' => 2400,
        'register_url' => sendwp_get_server_url() . '_/signup',
        'client_name' => sendwp_get_client_name(),
        'client_secret' => sendwp_get_client_secret(),
        'client_redirect' => sendwp_get_client_redirect(),
    ) );
    exit;
}
