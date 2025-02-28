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
                array(
                    'key' => 'field_67c09c2a66a75',
                    'label' => '"The Benefits" Section',
                    'name' => 'the_benefits_section',
                    'aria-label' => '',
                    'type' => 'group',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_67c09cb966a76',
                            'label' => 'Add Image',
                            'name' => 'benefits_image',
                            'aria-label' => '',
                            'type' => 'image',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'return_format' => 'url',
                            'library' => 'all',
                            'min_width' => '',
                            'min_height' => '',
                            'min_size' => '',
                            'max_width' => '',
                            'max_height' => '',
                            'max_size' => '',
                            'mime_types' => '',
                            'allow_in_bindings' => 0,
                            'preview_size' => 'medium',
                        ),
                        array(
                            'key' => 'field_67c09cd166a77',
                            'label' => 'Add Content',
                            'name' => 'benefits_content',
                            'aria-label' => '',
                            'type' => 'textarea',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'maxlength' => '',
                            'allow_in_bindings' => 1,
                            'rows' => '',
                            'placeholder' => '',
                            'new_lines' => '',
                        ),
                    ),
                ),
                array(
                    'key' => 'field_67c09ce566a78',
                    'label' => '"How To Use" Section',
                    'name' => 'how_to_use_section',
                    'aria-label' => '',
                    'type' => 'group',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_67c09ce566a79',
                            'label' => 'Add Image',
                            'name' => 'how_to_image',
                            'aria-label' => '',
                            'type' => 'image',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'return_format' => 'url',
                            'library' => 'all',
                            'min_width' => '',
                            'min_height' => '',
                            'min_size' => '',
                            'max_width' => '',
                            'max_height' => '',
                            'max_size' => '',
                            'mime_types' => '',
                            'allow_in_bindings' => 0,
                            'preview_size' => 'medium',
                        ),
                        array(
                            'key' => 'field_67c09ce566a7a',
                            'label' => 'Add Content',
                            'name' => 'how_to_content',
                            'aria-label' => '',
                            'type' => 'textarea',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'maxlength' => '',
                            'allow_in_bindings' => 1,
                            'rows' => '',
                            'placeholder' => '',
                            'new_lines' => '',
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