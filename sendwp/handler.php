<?php

add_action( 'wp_ajax_caldera_forms_sendwp_remote_install', 'caldera_forms_sendwp_remote_install_handler' );
function caldera_forms_sendwp_remote_install_handler () {

    //Security check via senwp_install_nonce
    $security = check_ajax_referer('sendwp_install_nonce', 'sendwp_nonce', false);
    if ( ! $security ) {
        ob_end_clean();
        echo json_encode( array( 'error' => true, 'debug' => '!security') );
        exit;
    } else if( ! current_user_can('install_plugins') ) {
        ob_end_clean();
        echo json_encode( array( 'error' => true, 'debug' => '!user_capability') );
        exit;
    }

    $all_plugins = get_plugins();
    $is_sendwp_installed = false;
    foreach(get_plugins() as $path => $details ) {
        if(false === strpos($path, '/sendwp.php')) continue;
        $is_sendwp_installed = true;
        activate_plugin( $path );
        break;
    }

    //Display an error message if a connection is activated on the website
    if( $is_sendwp_installed && sendwp_client_connected() ){
        ob_end_clean();
        echo json_encode( array( 'error' => true, 'debug' => 'sendwp_connected' ) );
        exit;
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
            echo json_encode( array( 'error' => esc_html( $api->get_error_message() ), 'debug' => $api ) );
            exit;
        }
        
        /*
        * Use the AJAX Upgrader skin to quietly install the plugin.
        */
        $upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
        $install = $upgrader->install( $api->download_link );
        if ( is_wp_error( $install ) ) {
            ob_end_clean();
            echo json_encode( array( 'error' => esc_html( $install->get_error_message() ), 'debug' => $api ) );
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
            'error' => esc_html__( 'Something went wrong. SendWP was not installed correctly.' ),
            'install' => $install,
            ) );
        exit;
    }
    
    echo json_encode( array(
        'partner_id' => 2400,
        'register_url' => esc_url( sendwp_get_server_url() . '_/signup' ),
        'client_name' => esc_attr( sendwp_get_client_name() ),
        'client_secret' => esc_attr( sendwp_get_client_secret() ),
        'client_redirect' => esc_url( sendwp_get_client_redirect() ),
        'client_url' => esc_url( sendwp_get_client_url() )
    ) );
    exit;
}
