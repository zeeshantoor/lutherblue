<?php
/**
 * Luther Blue Theme - AJAX cart functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get mini cart HTML via AJAX
 */
function luther_blue_get_mini_cart() {
    check_ajax_referer('luther-blue-ajax-nonce', 'nonce');
    
    ob_start();
    luther_blue_mini_cart_content();
    $cart_html = ob_get_clean();
    
    wp_send_json_success(array(
        'cart_html' => $cart_html,
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'cart_subtotal' => WC()->cart->get_cart_subtotal()
    ));
}
add_action('wp_ajax_luther_blue_get_mini_cart', 'luther_blue_get_mini_cart');
add_action('wp_ajax_nopriv_luther_blue_get_mini_cart', 'luther_blue_get_mini_cart');

/**
 * Update cart item quantity via AJAX
 */
function luther_blue_update_cart_item() {
    check_ajax_referer('luther-blue-ajax-nonce', 'nonce');
    
    $cart_item_key = sanitize_text_field($_POST['key']);
    $quantity = intval($_POST['quantity']);
    
    if ($cart_item_key && isset(WC()->cart->get_cart()[$cart_item_key])) {
        if ($quantity > 0) {
            WC()->cart->set_quantity($cart_item_key, $quantity);
        } else {
            WC()->cart->remove_cart_item($cart_item_key);
        }
    }
    
    WC()->cart->calculate_totals();
    
    wp_send_json_success();
}
add_action('wp_ajax_luther_blue_update_cart_item', 'luther_blue_update_cart_item');
add_action('wp_ajax_nopriv_luther_blue_update_cart_item', 'luther_blue_update_cart_item');

/**
 * Update cart fragments
 */
function luther_blue_cart_fragments($fragments) {
    $fragments['span.cart-count'] = '<span class="cart-count">' . WC()->cart->get_cart_contents_count() . '</span>';
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'luther_blue_cart_fragments');

/**
 * Output cart panel HTML
 */
function luther_blue_cart_panel() {
    if (!class_exists('WooCommerce')) {
        return;
    }
    ?>
    <div id="cart-panel" class="cart-panel">
        <div class="cart-panel-inner">
            <div class="cart-header">
                <h2>Your Cart (<?php echo WC()->cart->get_cart_contents_count(); ?>)</h2>
                <button class="close-cart" aria-label="Close cart"></button>
            </div>
            <div class="cart-content">
                <?php luther_blue_mini_cart_content(); ?>
            </div>
            <div class="cart-footer-container">
                <div class="cart-footer">
                    <div class="cart-subtotal">
                        <span class="subtotal-label"><?php esc_html_e('TOTAL', 'luther-blue'); ?></span>
                        <span class="subtotal-value"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
                    </div>
                    
                    <div class="cart-buttons">
                        <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="checkout-btn">
                            <?php esc_html_e('CHECKOUT NOW', 'luther-blue'); ?> <span class="separator">•</span> <?php echo WC()->cart->get_cart_subtotal(); ?>
                        </a>
                    </div>
                    
                    <div class="shipping-notice">
                        <?php 
                        // Get shipping notice from ACF options or use default
                        if (function_exists('get_field')) {
                            $shipping_notice = get_field('shipping_notice', 'option');
                            echo esc_html($shipping_notice ?: 'Free standard shipping Worldwide with orders over $80.');
                        } else {
                            esc_html_e('Free standard shipping Worldwide with orders over $80.', 'luther-blue');
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="site-overlay"></div>
    <?php
}

/**
 * Hook the cart panel to wp_footer
 */
function luther_blue_add_cart_panel() {
    luther_blue_cart_panel();
}
add_action('wp_footer', 'luther_blue_add_cart_panel');

/**
 * Get shipping notice via AJAX
 */
function luther_blue_get_shipping_notice() {
    check_ajax_referer('luther-blue-ajax-nonce', 'nonce');
    
    $shipping_notice = 'Free standard shipping Worldwide with orders over $80.';
    
    if (function_exists('get_field')) {
        $custom_notice = get_field('shipping_notice', 'option');
        if (!empty($custom_notice)) {
            $shipping_notice = $custom_notice;
        }
    }
    
    wp_send_json_success(array(
        'notice' => esc_html($shipping_notice)
    ));
}
add_action('wp_ajax_luther_blue_get_shipping_notice', 'luther_blue_get_shipping_notice');
add_action('wp_ajax_nopriv_luther_blue_get_shipping_notice', 'luther_blue_get_shipping_notice');

/**
 * AJAX add to cart functionality
 */
function luther_blue_ajax_add_to_cart() {
    check_ajax_referer('luther-blue-ajax-nonce', 'nonce');
    
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
    
    if ($product_id > 0) {
        $added = WC()->cart->add_to_cart($product_id, $quantity);
        
        if ($added) {
            WC_AJAX::get_refreshed_fragments();
        } else {
            wp_send_json_error(array(
                'message' => 'Error adding product to cart.'
            ));
        }
    } else {
        wp_send_json_error(array(
            'message' => 'Invalid product ID.'
        ));
    }
    
    wp_die();
}
add_action('wp_ajax_luther_blue_ajax_add_to_cart', 'luther_blue_ajax_add_to_cart');
add_action('wp_ajax_nopriv_luther_blue_ajax_add_to_cart', 'luther_blue_ajax_add_to_cart'); 