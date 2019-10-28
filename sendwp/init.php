<?php

include_once CFCORE_PATH . 'sendwp/handler.php';

function caldera_forms_admin_enqueue_sendwp_installer() {
    wp_enqueue_script('caldera_forms_sendwp_installer', plugins_url('installer.js', __FILE__));
}
add_action('caldera_forms_admin_main_enqueue', 'caldera_forms_admin_enqueue_sendwp_installer');