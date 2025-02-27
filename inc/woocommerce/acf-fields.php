<?php
/**
 * Luther Blue Theme - ACF Fields for Product Details
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register ACF fields for product details
 */
function luther_blue_register_product_acf_fields() {
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_product_details',
            'title' => 'Product Details',
            'fields' => array(
                array(
                    'key' => 'field_product_features',
                    'label' => 'Features',
                    'name' => 'features',
                    'type' => 'textarea',
                    'instructions' => 'Enter product features',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_product_aroma',
                    'label' => 'Aroma',
                    'name' => 'aroma',
                    'type' => 'textarea',
                    'instructions' => 'Enter product aroma details',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_product_key_ingredients',
                    'label' => 'Key Ingredients',
                    'name' => 'key_ingredients',
                    'type' => 'repeater',
                    'instructions' => 'Add key ingredients',
                    'required' => 0,
                    'min' => 0,
                    'max' => 0,
                    'layout' => 'table',
                    'button_label' => 'Add Ingredient',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_product_ingredients',
                            'label' => 'Ingredient',
                            'name' => 'ingredients',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 0,
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'product',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));
    }
}
add_action('acf/init', 'luther_blue_register_product_acf_fields'); 