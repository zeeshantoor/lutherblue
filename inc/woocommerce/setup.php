<?php
/**
 * Luther Blue Theme - WooCommerce setup
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Remove default WooCommerce styles
 */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Redirect cart page to checkout
 */
function luther_blue_redirect_cart_to_checkout() {
    if (is_cart()) {
        wp_redirect(wc_get_checkout_url());
        exit;
    }
}
add_action('template_redirect', 'luther_blue_redirect_cart_to_checkout');

/**
 * Remove cart page from WooCommerce menu items
 */
function luther_blue_remove_cart_menu_items($items) {
    if (isset($items['cart'])) {
        unset($items['cart']);
    }
    return $items;
}
add_filter('woocommerce_account_menu_items', 'luther_blue_remove_cart_menu_items');

/**
 * Modify add to cart button text and behavior
 */
function luther_blue_add_to_cart_text($text) {
    return __('Add to Cart', 'luther-blue');
}
add_filter('woocommerce_product_single_add_to_cart_text', 'luther_blue_add_to_cart_text');
add_filter('woocommerce_product_add_to_cart_text', 'luther_blue_add_to_cart_text');

/**
 * Remove cart fragments if cart page is disabled
 */
function luther_blue_disable_cart_fragments($fragments) {
    if (is_cart()) {
        return array();
    }
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'luther_blue_disable_cart_fragments');

/**
 * Modify proceed to checkout button to work with AJAX cart
 */
function luther_blue_modify_checkout_button_text($text) {
    return __('Checkout', 'luther-blue');
}
add_filter('woocommerce_proceed_to_checkout_button_text', 'luther_blue_modify_checkout_button_text');

/**
 * Remove cart page related hooks
 */
function luther_blue_remove_cart_hooks() {
    // Remove cart menu item from My Account
    remove_action('woocommerce_account_cart_endpoint', 'woocommerce_account_cart_endpoint_content');
}
add_action('init', 'luther_blue_remove_cart_hooks');

/**
 * Modify add to cart response to always return fragments
 */
function luther_blue_add_to_cart_response($response) {
    if (isset($response['redirect'])) {
        unset($response['redirect']);
    }
    return $response;
}
add_filter('woocommerce_add_to_cart_fragments', 'luther_blue_add_to_cart_response');

/**
 * Update add to cart button URL to use AJAX
 */
function luther_blue_update_add_to_cart_url($url) {
    return '';
}
add_filter('woocommerce_product_add_to_cart_url', 'luther_blue_update_add_to_cart_url');

/**
 * Modify cart link to open AJAX cart
 */
function luther_blue_cart_link_fragment($fragments) {
    ob_start();
    ?>
    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['span.cart-count'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'luther_blue_cart_link_fragment');

/**
 * Remove cart page from sitemap
 */
function luther_blue_remove_cart_from_sitemap($urls) {
    foreach ($urls as $key => $url) {
        if (is_object($url) && $url->loc === wc_get_cart_url()) {
            unset($urls[$key]);
        }
    }
    return $urls;
}
add_filter('wpseo_sitemap_entry', 'luther_blue_remove_cart_from_sitemap');

/**
 * Modify any cart links to trigger AJAX cart
 */
function luther_blue_modify_cart_links($url, $endpoint) {
    if ($endpoint === 'cart') {
        return 'javascript:void(0);';
    }
    return $url;
}
add_filter('woocommerce_get_endpoint_url', 'luther_blue_modify_cart_links', 10, 2);

/**
 * Fix WooCommerce cart persistence
 */
// function luther_blue_woocommerce_cart_persistence() {
//     // Ensure WooCommerce is active
//     if (class_exists('WooCommerce')) {
//         // Make sure session is started
//         if (!WC()->session->has_session()) {
//             WC()->session->set_customer_session_cookie(true);
//         }
        
//         // Force cart to load from session
//         if (WC()->cart && !WC()->cart->cart_contents_count && WC()->session->get('cart')) {
//             WC()->cart->get_cart_from_session();
//         }
//     }
// }
// add_action('wp_loaded', 'luther_blue_woocommerce_cart_persistence', 20);

/**
 * Prevent cart fragments from being cached
//  */
// function luther_blue_cart_fragments_no_cache($headers) {
//     $headers['Cache-Control'] = 'no-cache, no-store, must-revalidate, max-age=0';
//     return $headers;
// }
// add_filter('nocache_headers', 'luther_blue_cart_fragments_no_cache'); 

/**
 * Remove default WooCommerce hooks on single product page
 * to prevent duplication with our custom template
 */
function luther_blue_remove_single_product_hooks() {
    // Remove title
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
    
    // Remove price
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
    
    // Remove excerpt/short description
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
    
    // Remove rating
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
    
    // Keep add to cart form
    // remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
    
    // Remove meta information (categories, tags, SKU)
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    
    // Remove sharing
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
    
    // Remove product image/gallery as we have our own
    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    
    // Remove related products
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
    
    // Remove upsells
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
    
    // Remove tabs
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
}
add_action('init', 'luther_blue_remove_single_product_hooks');

/**
 * Enqueue scripts and styles for WooCommerce
 */
function luther_blue_woocommerce_scripts() {
    if (is_product()) {
        // Enqueue WooCommerce's accounting.js library
        wp_enqueue_script(
            'wc-accounting',
            WC()->plugin_url() . '/assets/js/accounting/accounting.min.js',
            array('jquery'),
            LUTHER_BLUE_VERSION,
            true
        );
        
        wp_enqueue_script(
            'luther-blue-variation-radios',
            get_template_directory_uri() . '/assets/js/woocommerce/variation-radios.js',
            array('jquery', 'wc-accounting'),
            LUTHER_BLUE_VERSION,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'luther_blue_woocommerce_scripts'); 