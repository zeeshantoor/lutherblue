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

// Helper functions
require_once get_template_directory() . '/inc/helpers/media.php';

// WooCommerce functionality (only if WooCommerce is active)
if (class_exists('WooCommerce')) {
    require_once get_template_directory() . '/inc/woocommerce/setup.php';
    require_once get_template_directory() . '/inc/woocommerce/template-functions.php';
    require_once get_template_directory() . '/inc/woocommerce/ajax-cart.php';
    require_once get_template_directory() . '/inc/woocommerce/shop.php';
    require_once get_template_directory() . '/inc/woocommerce/product-loop.php';
    require_once get_template_directory() . '/inc/woocommerce/acf-fields.php';
}

// Add this line with your other require_once statements
require_once get_template_directory() . '/inc/core/login.php'; 


//Enqueue theme main style and scripts
function luther_blue_enqueue_scripts() {
    wp_enqueue_style('luther-blue-main', LUTHER_BLUE_URI . '/assets/css/main.css', array(), LUTHER_BLUE_VERSION);
    
    wp_enqueue_style('luther-blue-product-loop', LUTHER_BLUE_URI . '/assets/css/woocommerce/product-loop.css', array('luther-blue-main'), LUTHER_BLUE_VERSION);
    
    wp_enqueue_script('luther-blue-main', LUTHER_BLUE_URI . '/assets/js/main.js', array('jquery'), LUTHER_BLUE_VERSION, true);
    
    wp_enqueue_script('luther-blue-loaders', LUTHER_BLUE_URI . '/assets/js/components/loaders.js', array('jquery'), LUTHER_BLUE_VERSION, true);
    
    wp_enqueue_script('luther-blue-cart-ajax', LUTHER_BLUE_URI . '/assets/js/ajax-cart.js', array('jquery', 'luther-blue-main', 'luther-blue-loaders'), LUTHER_BLUE_VERSION, true);    
    
    wp_enqueue_script('luther-blue-product-loop', LUTHER_BLUE_URI . '/assets/js/product-loop.js', array('jquery', 'luther-blue-cart-ajax'), LUTHER_BLUE_VERSION, true);
    
    // Add responsive layout handler for product grid
    wp_enqueue_script('luther-blue-responsive-layout', LUTHER_BLUE_URI . '/assets/js/product-loop-responsive.js', array('jquery', 'luther-blue-product-loop'), LUTHER_BLUE_VERSION, true);

    wp_localize_script('luther-blue-cart-ajax', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('luther-blue-ajax-nonce')
    ));
}
add_action('wp_enqueue_scripts', 'luther_blue_enqueue_scripts');

/**
 * Enqueue WooCommerce single product scripts and styles
 */
function luther_blue_enqueue_single_product_assets() {
    if (is_product()) {
        wp_enqueue_style(
            'luther-blue-single-product',
            get_template_directory_uri() . '/assets/css/woocommerce/single-product.css',
            array(),
            LUTHER_BLUE_VERSION
        );
        
        // Enqueue AJAX add to cart styles
        wp_enqueue_style(
            'luther-blue-single-product-ajax',
            get_template_directory_uri() . '/assets/css/woocommerce/single-product-ajax.css',
            array('luther-blue-single-product'),
            LUTHER_BLUE_VERSION
        );
        
        // Enqueue mobile-specific styles for single product
        wp_enqueue_style(
            'luther-blue-single-product-mobile',
            get_template_directory_uri() . '/assets/css/woocommerce/single-product-mobile.css',
            array('luther-blue-single-product'),
            LUTHER_BLUE_VERSION
        );

        wp_enqueue_script(
            'luther-blue-single-product',
            get_template_directory_uri() . '/assets/js/woocommerce/single-product.js',
            array('jquery'),
            LUTHER_BLUE_VERSION,
            true
        );
        
        // Enqueue AJAX add to cart script
        wp_enqueue_script(
            'luther-blue-single-product-ajax',
            get_template_directory_uri() . '/assets/js/woocommerce/single-product-ajax.js',
            array('jquery', 'wc-add-to-cart', 'wc-add-to-cart-variation'),
            LUTHER_BLUE_VERSION,
            true
        );
        
        // Enqueue mobile-specific script for single product
        wp_enqueue_script(
            'luther-blue-single-product-mobile',
            get_template_directory_uri() . '/assets/js/woocommerce/single-product-mobile.js',
            array('jquery', 'luther-blue-single-product'),
            LUTHER_BLUE_VERSION,
            true
        );
        
        // Enqueue variation radios script for handling add to cart button text
        wp_enqueue_script(
            'luther-blue-variation-radios',
            get_template_directory_uri() . '/assets/js/woocommerce/variation-radios.js',
            array('jquery', 'wc-add-to-cart-variation'),
            LUTHER_BLUE_VERSION,
            true
        );
        
        // Add inline script to trigger gallery initialization
        wp_add_inline_script(
            'luther-blue-single-product',
            'jQuery(document).ready(function($) { if(typeof initProductGallery === "function") { initProductGallery(); } });',
            'after'
        );
        
        // Localize script with AJAX object for single product
        wp_localize_script(
            'luther-blue-single-product-ajax',
            'ajax_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('luther-blue-ajax-nonce')
            )
        );
    }
}
add_action('wp_enqueue_scripts', 'luther_blue_enqueue_single_product_assets');


