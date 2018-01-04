<?php
/**
Plugin Name: Free Videos Post Type
Plugin URI: http://wordpress.org/plugins/hello-dolly/
*/
add_action( 'init', 'caldera_learn_free_video_cpt' );


function caldera_learn_free_video_cpt() {
    $labels = array(
        'name'               => _x( 'Free Videos', 'post type general name', 'your-plugin-textdomain' ),
        'singular_name'      => _x( 'Free Video', 'post type singular name', 'your-plugin-textdomain' ),
        'menu_name'          => _x( 'Free Videos', 'admin menu', 'your-plugin-textdomain' ),
        'name_admin_bar'     => _x( 'Free Video', 'add new on admin bar', 'your-plugin-textdomain' ),
        'add_new'            => _x( 'Add New', 'Free Video', 'your-plugin-textdomain' ),
        'add_new_item'       => __( 'Add New Free Video', 'your-plugin-textdomain' ),
        'new_item'           => __( 'New Free Video', 'your-plugin-textdomain' ),
        'edit_item'          => __( 'Edit Free Video', 'your-plugin-textdomain' ),
        'view_item'          => __( 'View Free Video', 'your-plugin-textdomain' ),
        'all_items'          => __( 'All Free Videos', 'your-plugin-textdomain' ),
        'search_items'       => __( 'Search Free Videos', 'your-plugin-textdomain' ),
        'parent_item_colon'  => __( 'Parent Free Videos:', 'your-plugin-textdomain' ),
        'not_found'          => __( 'No Free Videos found.', 'your-plugin-textdomain' ),
        'not_found_in_trash' => __( 'No Free Videos found in Trash.', 'your-plugin-textdomain' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'Description.', 'your-plugin-textdomain' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'Free Video' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
    );

    register_post_type( 'free-videos', $args );
}