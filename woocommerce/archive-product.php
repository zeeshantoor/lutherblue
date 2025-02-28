<?php
/**
 * The Template for displaying product archives, including the main shop page
 *
 * This template completely overrides the default WooCommerce template
 * to use our custom product loop and layout.
 *
 * @package Luther Blue
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header(); ?>

<div class="shop-wrapper">
    <div class="shop-container">
        <?php
        // Display shop hero section
        if (function_exists('luther_blue_shop_hero')) {
            luther_blue_shop_hero();
        }
        
        // Get products per row from theme settings or default to 3
        $products_per_row = 3;
        
        // Call our custom loop function
        if (function_exists('luther_blue_custom_product_loop')) {
            luther_blue_custom_product_loop($products_per_row);
        } else {
            // Fallback to standard WooCommerce loop if our function doesn't exist
            if (have_posts()) {
                echo '<div class="products-container">';
                woocommerce_product_loop_start();
                while (have_posts()) {
                    the_post();
                    wc_get_template_part('content', 'product');
                }
                woocommerce_product_loop_end();
                echo '</div>';
                
                /**
                 * Hook: woocommerce_after_shop_loop.
                 *
                 * @hooked woocommerce_pagination - 10
                 */
                do_action('woocommerce_after_shop_loop');
            } else {
                /**
                 * Hook: woocommerce_no_products_found.
                 *
                 * @hooked wc_no_products_found - 10
                 */
                do_action('woocommerce_no_products_found');
            }
        }
        ?>
    </div>
</div>

<?php get_footer(); 