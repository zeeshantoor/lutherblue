<?php
/**
 * Luther Blue Theme - Custom WooCommerce Product Loop
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom product loop template
 */
function luther_blue_custom_product_loop($products_per_row = 3) {
    // Get global WooCommerce query
    global $product;
    
    // Start the loop
    if (wc_get_loop_prop('total')) {
        echo '<div class="luther-blue-products-grid">';
        
        $counter = 0;
        $products_per_row = intval($products_per_row);
        
        while (have_posts()) {
            the_post();
            
            // Start a new row if needed
            if ($counter % $products_per_row === 0) {
                echo '<div class="luther-blue-products-row">';
            }
            
            // Get the product
            global $product;
            
            // Output the product
            echo '<div class="luther-blue-product-item">';
            
            // 1st row: Product image with alpha background
            echo '<div class="luther-blue-product-image">';
            echo '<a href="' . esc_url(get_permalink()) . '">';
            // Use a specific image size for consistency
            echo get_the_post_thumbnail($product->get_id(), 'medium', array('class' => 'luther-blue-product-thumbnail'));
            echo '</a>';
            echo '</div>';
            
            // Get product categories
            $categories = get_the_terms($product->get_id(), 'product_cat');
            if ($categories && !is_wp_error($categories)) {
                $main_category = null;
                $subcategory = null;
                
                // Sort categories by hierarchy
                foreach ($categories as $category) {
                    if ($category->parent == 0) {
                        $main_category = $category;
                    } else {
                        $subcategory = $category;
                    }
                }
                
                // If we only found a subcategory, get its parent
                if (!$main_category && $subcategory) {
                    $main_category = get_term($subcategory->parent, 'product_cat');
                }
                
                if ($main_category) {
                    echo '<div class="luther-blue-product-categories">';
                    echo '<span class="category-dot">•</span>';
                    echo '<span class="main-category">' . esc_html($main_category->name) . '</span>';
                    if ($subcategory) {
                        echo '<span class="category-dot">•</span>';
                        echo '<span class="sub-category">' . esc_html($subcategory->name) . '</span>';
                    }
                    echo '</div>';
                } else {
                    // If no categories found, add an empty div to maintain layout
                    echo '<div class="luther-blue-product-categories"></div>';
                }
            } else {
                // If no categories found, add an empty div to maintain layout
                echo '<div class="luther-blue-product-categories"></div>';
            }
            
            // Divider
            echo '<div class="luther-blue-product-divider"></div>';
            
            // Product details container with flexbox layout
            echo '<div class="luther-blue-product-details-container">';
            
            // First row: Title and Price with flexbox
            echo '<div class="luther-blue-product-details-row">';
            
            // Title column
            echo '<div class="luther-blue-product-title-column">';
            echo '<h2 class="luther-blue-product-title"><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h2>';
            echo '</div>';
            
            // Price column
            echo '<div class="luther-blue-product-price-column">';
            
            // Check if product has variations
            if ($product->is_type('variable')) {
                // Display price range
                $prices = $product->get_variation_prices();
                
                if (!empty($prices['price'])) {
                    $min_price = current($prices['price']);
                    $max_price = end($prices['price']);
                    
                    if ($min_price !== $max_price) {
                        echo '<div class="luther-blue-product-price-range">';
                        echo '<span class="luther-blue-product-price-main">' . wc_price($min_price) . '</span>';
                        echo '<span class="luther-blue-product-price-secondary"> - ' . wc_price($max_price) . '</span>';
                        echo '</div>';
                    } else {
                        echo '<div class="luther-blue-product-price">' . wc_price($min_price) . '</div>';
                    }
                }
            } else {
                // Regular product price
                echo '<div class="luther-blue-product-price">' . $product->get_price_html() . '</div>';
            }
            
            echo '</div>'; // End price column
            
            echo '</div>'; // End first row
            
            // Second row: Excerpt only
            echo '<div class="luther-blue-product-details-row">';
            
            // Excerpt column
            echo '<div class="luther-blue-product-excerpt-column">';
            
            // Get product excerpt
            $excerpt = $product->get_short_description();
            if (empty($excerpt)) {
                $excerpt = wp_trim_words(get_the_content(), 15, '...');
            } else {
                $excerpt = wp_trim_words($excerpt, 15, '...');
            }
            
            echo '<div class="luther-blue-product-excerpt">' . $excerpt . '</div>';
            echo '</div>'; // End excerpt column
            
            echo '</div>'; // End second row
            
            echo '</div>'; // End product details container
            
            // Add to cart button
            echo '<div class="luther-blue-product-actions">';
            
            // Get product price for button
            $price_html = '';
            if ($product->is_type('variable')) {
                $prices = $product->get_variation_prices();
                if (!empty($prices['price'])) {
                    $min_price = current($prices['price']);
                    $price_html = wc_price($min_price);
                }
                echo '<a href="' . esc_url(get_permalink()) . '" class="luther-blue-select-product-btn">Select Product</a>';
            } else {
                $price_html = wc_price($product->get_price());
                echo '<a href="javascript:void(0);" 
                    data-product_id="' . esc_attr($product->get_id()) . '" 
                    data-product_sku="' . esc_attr($product->get_sku()) . '" 
                    class="luther-blue-add-to-cart-btn ajax_add_to_cart">Add to Cart <span class="product-price-dot">•</span> ' . $price_html . '</a>';
            }
            
            echo '</div>'; // End product actions
            
            echo '</div>'; // End product item
            
            $counter++;
            
            // Close the row if needed
            if ($counter % $products_per_row === 0 || $counter === wc_get_loop_prop('total')) {
                echo '</div>'; // End row
            }
        }
        
        echo '</div>'; // End grid
    }
    
    // Reset post data
    wp_reset_postdata();
}

/**
 * Custom template for product loop
 * 
 * This function is hooked into 'woocommerce_before_main_content' with priority 20
 * to ensure it runs after the default WooCommerce hooks are removed.
 */
function luther_blue_custom_product_loop_template() {
    // Only on shop and archive pages
    if (is_shop() || is_product_category() || is_product_tag()) {
        // Remove the default loop
        remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
        
        // Remove the default loop content
        remove_action('woocommerce_shop_loop', 'WC_Structured_Data::generate_product_data', 10);
        
        // Use our custom loop
        add_action('woocommerce_before_shop_loop', function() {
            // Get products per row from theme settings or default to 3
            $products_per_row = 3;
            
            // Call our custom loop function
            luther_blue_custom_product_loop($products_per_row);
            
            // Prevent the default loop from running
            add_filter('woocommerce_is_filtered', '__return_true');
        }, 20);
        
        // Remove the default shop loop
        add_filter('woocommerce_locate_template', function($template, $template_name) {
            if ($template_name === 'loop/loop-start.php' || 
                $template_name === 'loop/loop-end.php' || 
                $template_name === 'content-product.php') {
                return get_template_directory() . '/woocommerce/empty-template.php';
            }
            return $template;
        }, 10, 2);
        
        // Remove the woocommerce-products-header
        remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
        remove_action('woocommerce_archive_description', 'woocommerce_product_archive_description', 10);
        add_filter('woocommerce_show_page_title', '__return_false');
        
        // Completely replace the shop loop
        add_action('woocommerce_after_main_content', function() {
            // Remove all default WooCommerce content
            remove_all_actions('woocommerce_shop_loop');
            remove_all_actions('woocommerce_after_shop_loop');
            remove_all_actions('woocommerce_no_products_found');
        }, 5);
    }
}
add_action('woocommerce_before_main_content', 'luther_blue_custom_product_loop_template', 5);

/**
 * Add custom body class for our product loop
 */
function luther_blue_product_loop_body_class($classes) {
    if (is_shop() || is_product_category() || is_product_tag()) {
        $classes[] = 'luther-blue-custom-product-loop';
    }
    return $classes;
}
add_filter('body_class', 'luther_blue_product_loop_body_class'); 