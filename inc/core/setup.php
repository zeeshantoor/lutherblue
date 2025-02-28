<?php
/**
 * Luther Blue Theme - Core setup functions
 */

if (!defined('ABSPATH')) {
    exit;
}

//Theme setup
function luther_blue_setup() {
    // Theme support
    add_theme_support('woocommerce');
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo', array(
        'flex-width'  => true,
        'flex-height' => true,
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Register menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'luther-blue'),
        'footer' => esc_html__('Footer Menu', 'luther-blue'),
        'top-notification' => esc_html__('Top Notification Menu', 'luther-blue'),
    ));

    // Remove sidebar support
    remove_theme_support('widgets-block-editor');
    remove_theme_support('widgets');
}
add_action('after_setup_theme', 'luther_blue_setup');

//Unregister all sidebars
function luther_blue_remove_sidebars() {
    unregister_sidebar('sidebar-1');
    unregister_sidebar('sidebar-2');
    unregister_sidebar('sidebar-3');
    unregister_sidebar('array-sidebar');
}
add_action('widgets_init', 'luther_blue_remove_sidebars', 11);

//Remove sidebar-related body classes
function luther_blue_remove_sidebar_body_class($classes) {
    return array_diff($classes, array('has-sidebar'));
}
add_filter('body_class', 'luther_blue_remove_sidebar_body_class'); 