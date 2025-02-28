<?php
/**
 * Luther Blue Theme - WooCommerce template functions
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom mini cart content
 */
function luther_blue_mini_cart_content() {
    if (WC()->cart->is_empty()) {
        ?>
        <div class="cart-empty">
            <p>Your cart is currently empty.</p>
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="button">Continue Shopping</a>
        </div>
        <?php
    } else {
        ?>
        <ul class="woocommerce-mini-cart cart_list product_list_widget">
            <?php
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                
                if ($_product && $_product->exists() && $cart_item['quantity'] > 0) {
                    $product_name = $_product->get_name();
                    $thumbnail = $_product->get_image();
                    $product_price = WC()->cart->get_product_price($_product);
                    $product_permalink = $_product->is_visible() ? $_product->get_permalink($cart_item) : '';
                    
                    // Get product short description or excerpt
                    $product_excerpt = $_product->get_short_description();
                    if (empty($product_excerpt)) {
                        $product_excerpt = $_product->get_description();
                    }
                    // Limit to 10 words
                    $product_excerpt = wp_trim_words($product_excerpt, 10, '...');
                    ?>
                    <li class="woocommerce-mini-cart-item">
                        <div class="cart-item-image">
                            <?php if ($product_permalink) : ?>
                                <a href="<?php echo esc_url($product_permalink); ?>">
                                    <?php echo $thumbnail; ?>
                                </a>
                            <?php else : ?>
                                <?php echo $thumbnail; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="cart-item-details">
                            <div class="cart-item-header">
                                <div class="cart-item-title">
                                    <?php if ($product_permalink) : ?>
                                        <a href="<?php echo esc_url($product_permalink); ?>">
                                            <?php echo esc_html($product_name); ?>
                                        </a>
                                    <?php else : ?>
                                        <?php echo esc_html($product_name); ?>
                                    <?php endif; ?>
                                </div>
                                <button class="remove-item" data-key="<?php echo esc_attr($cart_item_key); ?>" aria-label="Remove this item"></button>
                            </div>
                            
                            <?php if (!empty($product_excerpt)) : ?>
                                <div class="cart-item-excerpt">
                                    <?php echo esc_html($product_excerpt); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="cart-item-price-quantity">
                                <div class="cart-item-price">
                                    <?php echo $product_price; ?>
                                </div>
                                <div class="cart-item-quantity">
                                    <button class="quantity-btn minus" data-key="<?php echo esc_attr($cart_item_key); ?>">-</button>
                                    <span class="cart-item-quantity-value"><?php echo esc_html($cart_item['quantity']); ?></span>
                                    <button class="quantity-btn plus" data-key="<?php echo esc_attr($cart_item_key); ?>">+</button>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
        <?php
    }
}

/**
 * Modify WooCommerce product loop item structure
 */
function luther_blue_woocommerce_template_loop_product_link_open() {
    echo '<div class="product-inner"><a href="' . get_the_permalink() . '" class="woocommerce-LoopProduct-link">';
}

function luther_blue_woocommerce_template_loop_product_link_close() {
    echo '</a></div>';
}

function luther_blue_woocommerce_template_loop_product_thumbnail() {
    echo '<div class="product-image-wrapper">';
    echo woocommerce_get_product_thumbnail();
    echo '</div>';
}

function luther_blue_woocommerce_template_loop_product_title() {
    global $product;
    $categories = get_the_terms($product->get_id(), 'product_cat');
    $category_name = '';
    
    if ($categories && !is_wp_error($categories)) {
        $category_name = $categories[0]->name;
    }
    
    echo '<div class="product-info">';
    if ($category_name) {
        echo '<div class="product-category"><span class="category-bullet"></span>' . esc_html($category_name) . '</div>';
    }
    echo '<hr class="product-divider">';
    echo '<h2 class="woocommerce-loop-product__title">' . get_the_title() . '</h2>';
    
    // Get short description or excerpt
    $short_description = $product->get_short_description();
    if (!empty($short_description)) {
        echo '<div class="product-description">' . wp_kses_post($short_description) . '</div>';
    }
    
    // Price
    echo '<div class="price-container">';
    echo wc_get_template_part('loop/price');
    echo '</div>';
}

function luther_blue_woocommerce_template_loop_add_to_cart() {
    global $product;
    echo '<div class="add-to-cart-container">';
    $args = array(
        'quantity' => 1,
        'class' => 'button add_to_cart_button ajax_add_to_cart',
        'attributes' => array(
            'data-product_id' => $product->get_id(),
            'aria-label' => $product->add_to_cart_description(),
        ),
    );
    
    echo sprintf('<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
        esc_url($product->add_to_cart_url()),
        esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
        esc_attr(isset($args['class']) ? $args['class'] : 'button'),
        isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
        'Add to your cart - ' . wc_price($product->get_price())
    );
    echo '</div></div>';
}

/**
 * Setup WooCommerce hooks
 */
function luther_blue_woocommerce_hooks() {
    // Remove default WooCommerce hooks
    remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
    remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    
    // Add custom hooks
    add_action('woocommerce_before_shop_loop_item', 'luther_blue_woocommerce_template_loop_product_link_open', 10);
    add_action('woocommerce_before_shop_loop_item_title', 'luther_blue_woocommerce_template_loop_product_thumbnail', 10);
    add_action('woocommerce_shop_loop_item_title', 'luther_blue_woocommerce_template_loop_product_title', 10);
    add_action('woocommerce_after_shop_loop_item', 'luther_blue_woocommerce_template_loop_add_to_cart', 10);
    add_action('woocommerce_after_shop_loop_item', 'luther_blue_woocommerce_template_loop_product_link_close', 15);
    
    // Set products per row
    add_filter('loop_shop_columns', function() { return 3; });
    
    // Remove breadcrumbs
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
}
add_action('init', 'luther_blue_woocommerce_hooks');

/**
 * Modify the "You may also like" heading on related products
 */
function luther_blue_related_products_heading() {
    return 'You may also like';
}
add_filter('woocommerce_product_related_products_heading', 'luther_blue_related_products_heading');