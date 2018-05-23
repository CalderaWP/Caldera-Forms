<?php
global $wpdb;
echo "error";

if ($wpdb != null){
    $wpdb->print_error();
}
