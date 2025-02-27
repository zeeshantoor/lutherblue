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
    
    // Remove archive description and title
    remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
    remove_action('woocommerce_archive_description', 'woocommerce_product_archive_description', 10);
    add_filter('woocommerce_show_page_title', '__return_false');
    
    // Add custom hooks
    add_action('woocommerce_before_main_content', 'luther_blue_shop_wrapper_start', 10);
    add_action('woocommerce_after_main_content', 'luther_blue_shop_wrapper_end', 10);
    add_action('woocommerce_before_main_content', 'luther_blue_shop_hero', 20);
    
    // Remove shop controls/filters
    remove_action('woocommerce_before_shop_loop', 'luther_blue_shop_controls', 20);
    
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
        <div class="shop-container">
    <?php
}

/**
 * Shop wrapper end
 */
function luther_blue_shop_wrapper_end() {
    ?>
        </div>
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
 * Add CSS for shop page
 */
function luther_blue_shop_styles() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        wp_enqueue_style('luther-blue-shop', get_template_directory_uri() . '/assets/css/woocommerce/shop.css', array('luther-blue-main'), LUTHER_BLUE_VERSION);
        wp_enqueue_style('luther-blue-product-loop', get_template_directory_uri() . '/assets/css/woocommerce/product-loop.css', array('luther-blue-main'), LUTHER_BLUE_VERSION);
        wp_enqueue_style('luther-blue-custom-product-loop', get_template_directory_uri() . '/assets/css/woocommerce/custom-product-loop.css', array('luther-blue-main'), LUTHER_BLUE_VERSION);
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
