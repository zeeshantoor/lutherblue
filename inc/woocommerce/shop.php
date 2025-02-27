<?php
/**
 * Luther Blue Theme - WooCommerce shop page customizations
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Hero section
 */
function luther_blue_shop_hero() {
    if (!is_shop() && !is_product_category() && !is_product_tag()) {
        return;
    }
    
    // Get shop page title
    $shop_title = woocommerce_page_title(false);
    
    // Get shop description
    $shop_description = '';
    if (is_shop()) {
        $shop_page_id = wc_get_page_id('shop');
        if ($shop_page_id) {
            $shop_description = get_post_meta($shop_page_id, '_shop_description', true);
            if (empty($shop_description)) {
                $shop_description = get_the_content(null, false, $shop_page_id);
            }
        }
    } elseif (is_product_category()) {
        $category = get_queried_object();
        $shop_description = $category->description;
    }
    ?>
    <div class="shop-hero">
        <div class="container">
            <div class="shop-hero-content">
                <h1 class="shop-hero-title"><?php echo esc_html($shop_title); ?></h1>
                <?php if (!empty($shop_description)) : ?>
                    <div class="shop-hero-description">
                        <?php echo wp_kses_post($shop_description); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Custom shop filters and sorting
 */
function luther_blue_shop_controls() {
    if (!is_shop() && !is_product_category() && !is_product_tag()) {
        return;
    }
    
    ?>
    <div class="shop-controls">
        <div class="container">
            <div class="shop-controls-inner">
                <div class="shop-filter-toggle-container">
                    <button class="filter-button" id="shop-filter-toggle">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 6H21M6 12H18M10 18H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Filter & Sort
                    </button>
                </div>
                
                <div class="shop-filter-panel" id="shop-filter-panel">
                    <div class="shop-filter-header">
                        <h3>Filter & Sort</h3>
                        <button class="close-filter" aria-label="Close filter panel"></button>
                    </div>
                    
                    <div class="shop-filter-content">
                        <?php if (is_active_sidebar('shop-filters')) : ?>
                            <div class="shop-widget-area">
                                <?php dynamic_sidebar('shop-filters'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="shop-sorting">
                            <h4>Sort By</h4>
                            <?php woocommerce_catalog_ordering(); ?>
                        </div>
                    </div>
                </div>
                
                <div class="shop-result-count">
                    <?php woocommerce_result_count(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Custom shop layout
 */
function luther_blue_shop_layout() {
    // Remove default WooCommerce hooks
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
    
    // Add custom hooks
    add_action('woocommerce_before_main_content', 'luther_blue_shop_wrapper_start', 10);
    add_action('woocommerce_after_main_content', 'luther_blue_shop_wrapper_end', 10);
    add_action('woocommerce_before_main_content', 'luther_blue_shop_hero', 20);
    add_action('woocommerce_before_shop_loop', 'luther_blue_shop_controls', 20);
    add_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 30);
    
    // Set products per row
    add_filter('loop_shop_columns', function() { return 3; });
    
    // Set products per page
    add_filter('loop_shop_per_page', function() { return 12; });
}
add_action('init', 'luther_blue_shop_layout');

/**
 * Shop wrapper start
 */
function luther_blue_shop_wrapper_start() {
    ?>
    <div class="shop-wrapper">
    <?php
}

/**
 * Shop wrapper end
 */
function luther_blue_shop_wrapper_end() {
    ?>
    </div>
    <?php
}

/**
 * Add CSS class to body for shop pages
 */
function luther_blue_shop_body_class($classes) {
    if (is_shop() || is_product_category() || is_product_tag()) {
        $classes[] = 'luther-shop-page';
    }
    return $classes;
}
add_filter('body_class', 'luther_blue_shop_body_class');

/**
 * Add JavaScript for shop filter toggle
 */
function luther_blue_shop_scripts() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        wp_add_inline_script('luther-blue-main', '
            jQuery(document).ready(function($) {
                // Toggle filter panel
                $("#shop-filter-toggle").on("click", function() {
                    $("#shop-filter-panel").toggleClass("active");
                    $("body").toggleClass("filter-open");
                });
                
                // Close filter panel
                $(".close-filter").on("click", function() {
                    $("#shop-filter-panel").removeClass("active");
                    $("body").removeClass("filter-open");
                });
                
                // Close filter panel when clicking on overlay
                $(".site-overlay").on("click", function() {
                    $("#shop-filter-panel").removeClass("active");
                    $("body").removeClass("filter-open");
                });
            });
        ');
    }
}
add_action('wp_enqueue_scripts', 'luther_blue_shop_scripts');

/**
 * Add CSS for shop page
 */
function luther_blue_shop_styles() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        wp_enqueue_style('luther-blue-shop', get_template_directory_uri() . '/assets/css/woocommerce/shop.css', array('luther-blue-main'), LUTHER_BLUE_VERSION);
    }
}
add_action('wp_enqueue_scripts', 'luther_blue_shop_styles');

/**
 * Add ACF fields for shop page customization
 */
function luther_blue_shop_acf_fields() {
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_shop_settings',
            'title' => 'Shop Page Settings',
            'fields' => array(
                array(
                    'key' => 'field_shop_products_per_page',
                    'label' => 'Products Per Page',
                    'name' => 'shop_products_per_page',
                    'type' => 'number',
                    'instructions' => 'Set the number of products to display per page',
                    'default_value' => 12,
                    'min' => 3,
                    'max' => 48,
                    'step' => 3,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'luther-options',
                    ),
                ),
            ),
            'menu_order' => 2,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
        ));
    }
}
add_action('acf/init', 'luther_blue_shop_acf_fields');

/**
 * Custom shop meta field for shop description
 */
function luther_blue_shop_meta_fields() {
    add_meta_box(
        'luther_blue_shop_description',
        __('Shop Description', 'luther-blue'),
        'luther_blue_shop_description_callback',
        'page',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'luther_blue_shop_meta_fields');

/**
 * Shop description meta box callback
 */
function luther_blue_shop_description_callback($post) {
    // Only show on shop page
    if ($post->ID !== wc_get_page_id('shop')) {
        return;
    }
    
    wp_nonce_field('luther_blue_shop_description_nonce', 'luther_blue_shop_description_nonce');
    $value = get_post_meta($post->ID, '_shop_description', true);
    ?>
    <p><?php _e('Enter a description for the shop page hero section.', 'luther-blue'); ?></p>
    <textarea style="width:100%; height:150px;" name="shop_description"><?php echo esc_textarea($value); ?></textarea>
    <?php
}

/**
 * Save shop description meta
 */
function luther_blue_save_shop_description($post_id) {
    if (!isset($_POST['luther_blue_shop_description_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['luther_blue_shop_description_nonce'], 'luther_blue_shop_description_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['shop_description'])) {
        update_post_meta($post_id, '_shop_description', wp_kses_post($_POST['shop_description']));
    }
}
add_action('save_post', 'luther_blue_save_shop_description');

/**
 * Dynamic products per page based on ACF option
 */
function luther_blue_products_per_page($products) {
    if (function_exists('get_field')) {
        $products_per_page = get_field('shop_products_per_page', 'option');
        if ($products_per_page) {
            return $products_per_page;
        }
    }
    return 12;
}
add_filter('loop_shop_per_page', 'luther_blue_products_per_page', 20);
