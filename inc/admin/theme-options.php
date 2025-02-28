<?php
/**
 * Luther Blue Theme - Admin theme options
 */

if (!defined('ABSPATH')) {
    exit;
}

//Create ACF Options Page
function luther_blue_acf_options_page() {
    if (function_exists('acf_add_options_page')) {
        // Add parent options page
        $parent = acf_add_options_page(array(
            'page_title'    => 'Luther Options',
            'menu_title'    => 'Luther Options',
            'menu_slug'     => 'luther-options',
            'capability'    => 'manage_options',
            'redirect'      => false,
            'icon_url'      => 'dashicons-admin-customizer',
            'position'      => 55,
        ));
    }
}
add_action('acf/init', 'luther_blue_acf_options_page');

//Add ACF fields for theme options
function luther_blue_register_acf_fields() {
    if (function_exists('acf_add_local_field_group')) {
        // Notification Banner Fields
        acf_add_local_field_group(array(
            'key' => 'group_notification_banner',
            'title' => 'Notification Banner',
            'fields' => array(
                array(
                    'key' => 'field_enable_notification_banner',
                    'label' => 'Enable Notification Banner',
                    'name' => 'enable_notification_banner',
                    'type' => 'true_false',
                    'instructions' => 'Toggle the notification banner on/off',
                    'default_value' => 0,
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_notification_text',
                    'label' => 'Notification Text',
                    'name' => 'notification_text',
                    'type' => 'text',
                    'instructions' => 'Text to display in the notification banner',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_enable_notification_banner',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_notification_bg_color',
                    'label' => 'Background Color',
                    'name' => 'notification_bg_color',
                    'type' => 'color_picker',
                    'instructions' => 'Choose a background color for the notification banner',
                    'default_value' => '#f7f4ec',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_enable_notification_banner',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_notification_text_color',
                    'label' => 'Text Color',
                    'name' => 'notification_text_color',
                    'type' => 'color_picker',
                    'instructions' => 'Choose a text color for the notification banner',
                    'default_value' => '#333333',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_enable_notification_banner',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
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
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
        ));
        
        // Cart Settings Fields
        acf_add_local_field_group(array(
            'key' => 'group_cart_settings',
            'title' => 'Cart Settings',
            'fields' => array(
                array(
                    'key' => 'field_shipping_notice',
                    'label' => 'Shipping Notice',
                    'name' => 'shipping_notice',
                    'type' => 'text',
                    'instructions' => 'Text to display in the cart footer (e.g., Free shipping information)',
                    'default_value' => 'Free standard shipping Worldwide with orders over $80.',
                    'placeholder' => 'Enter shipping notice text',
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
            'menu_order' => 1,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
        ));
    }
}
add_action('acf/init', 'luther_blue_register_acf_fields');


//Add logo to options page and remove screen options
function luther_blue_options_page_html() {
    // Only on our options page
    $screen = get_current_screen();
    if (strpos($screen->id, 'luther-options') === false) {
        return;
    }
    
    // Remove screen options tab
    add_filter('screen_options_show_screen', '__return_false');
}
add_action('acf/input/admin_head', 'luther_blue_options_page_html');
