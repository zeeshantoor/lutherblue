<?php
/**
 * Luther Blue Theme functions and enqueue scripts
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('LUTHER_BLUE_VERSION', '1.0.0');
define('LUTHER_BLUE_DIR', get_template_directory());
define('LUTHER_BLUE_URI', get_template_directory_uri());


// Core functionality
require_once get_template_directory() . '/inc/core/setup.php';

// Admin and customizer
require_once get_template_directory() . '/inc/admin/theme-options.php';
// require_once get_template_directory() . '/inc/customizer/customizer.php';

// Helper functions
require_once get_template_directory() . '/inc/helpers/media.php';
// require_once get_template_directory() . '/inc/helpers/utilities.php';

// WooCommerce functionality (only if WooCommerce is active)
if (class_exists('WooCommerce')) {
    require_once get_template_directory() . '/inc/woocommerce/setup.php';
    require_once get_template_directory() . '/inc/woocommerce/template-functions.php';
    require_once get_template_directory() . '/inc/woocommerce/ajax-cart.php';
    require_once get_template_directory() . '/inc/woocommerce/shop.php';
}

// Add this line with your other require_once statements
require_once get_template_directory() . '/inc/core/login.php'; 


//Enqueue theme main style and scripts
function luther_blue_enqueue_scripts() {
    wp_enqueue_style('luther-blue-main', LUTHER_BLUE_URI . '/assets/css/main.css', array(), LUTHER_BLUE_VERSION);
    
    wp_enqueue_script('luther-blue-main', LUTHER_BLUE_URI . '/assets/js/main.js', array('jquery'), LUTHER_BLUE_VERSION, true);
    
    wp_enqueue_script('luther-blue-loaders', LUTHER_BLUE_URI . '/assets/js/components/loaders.js', array('jquery'), LUTHER_BLUE_VERSION, true);
    
    wp_enqueue_script('luther-blue-cart-ajax', LUTHER_BLUE_URI . '/assets/js/ajax-cart.js', array('jquery', 'luther-blue-main', 'luther-blue-loaders'), LUTHER_BLUE_VERSION, true);    

    wp_localize_script('luther-blue-cart-ajax', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('luther-blue-ajax-nonce')
    ));
}
add_action('wp_enqueue_scripts', 'luther_blue_enqueue_scripts');


