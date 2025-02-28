<?php
/**
 * The Template for displaying all single products
 *
 * @package Luther Blue
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('shop');

/**
 * Hook: woocommerce_before_main_content
 */
do_action('woocommerce_before_main_content');
?>

<div class="luther-blue-single-product">
    <div class="luther-blue-product-container">
        <?php while (have_posts()) : ?>
            <?php the_post(); ?>
            <?php global $product; ?>
            
            <div class="luther-blue-product-layout">
                <!-- Left Column: Product Gallery Thumbnails (Desktop) -->
                <div class="luther-blue-product-gallery-thumbs">
                    <!-- Down Arrow -->
                    <div class="gallery-nav gallery-nav-down">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/>
                        </svg>
                    </div>
                    
                    <!-- Thumbnails Container -->
                    <div class="gallery-thumbs-container">
                        <?php
                        // Get product gallery image IDs
                        $attachment_ids = $product->get_gallery_image_ids();
                        
                        // Add main product image to the beginning of gallery
                        $main_image_id = $product->get_image_id();
                        if ($main_image_id) {
                            array_unshift($attachment_ids, $main_image_id);
                        }
                        
                        // Display gallery thumbnails
                        if (!empty($attachment_ids)) {
                            foreach ($attachment_ids as $index => $attachment_id) {
                                // Get full size image URL
                                $full_size_image = wp_get_attachment_image_src($attachment_id, 'full');
                                $thumbnail = wp_get_attachment_image_src($attachment_id, 'thumbnail');
                                
                                if ($full_size_image && $thumbnail) {
                                    // Create thumbnail with data attributes for full size image
                                    echo '<div class="gallery-thumb' . ($index === 0 ? ' active' : '') . '" data-image-id="' . esc_attr($attachment_id) . '">';
                                    echo '<img 
                                        src="' . esc_url($thumbnail[0]) . '" 
                                        data-full-src="' . esc_url($full_size_image[0]) . '" 
                                        width="' . esc_attr($thumbnail[1]) . '" 
                                        height="' . esc_attr($thumbnail[2]) . '" 
                                        alt="' . esc_attr(get_post_meta($attachment_id, '_wp_attachment_image_alt', true)) . '" 
                                        loading="lazy">';
                                    echo '</div>';
                                }
                            }
                        } else {
                            // If no gallery images, show placeholder
                            echo '<div class="gallery-thumb active">';
                            echo '<img src="' . wc_placeholder_img_src() . '" alt="' . esc_attr__('Placeholder', 'luther-blue') . '">';
                            echo '</div>';
                        }
                        ?>
                    </div>
                    
                    <!-- Up Arrow -->
                    <div class="gallery-nav gallery-nav-up">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                        </svg>
                    </div>
                </div>

                <!-- Middle Column: Main Product Image (Desktop) -->
                <div class="luther-blue-product-main-image">
                    <?php
                    if ($main_image_id) {
                        // Get full size image URL
                        $full_size_image = wp_get_attachment_image_src($main_image_id, 'full');
                        if ($full_size_image) {
                            echo '<img 
                                src="' . esc_url($full_size_image[0]) . '" 
                                width="' . esc_attr($full_size_image[1]) . '" 
                                height="' . esc_attr($full_size_image[2]) . '" 
                                alt="' . esc_attr(get_post_meta($main_image_id, '_wp_attachment_image_alt', true)) . '" 
                                class="main-image" 
                                id="main-product-image">';
                        }
                    } else {
                        // If no main image, show placeholder
                        echo '<img src="' . wc_placeholder_img_src() . '" alt="' . esc_attr__('Placeholder', 'luther-blue') . '" class="main-image" id="main-product-image">';
                    }
                    ?>
                </div>

                <!-- Right Column: Product Details with Mobile Order Wrapper -->
                <div class="luther-blue-product-details">
                    <!-- Mobile Order Wrapper - This div will control the order of elements on mobile -->
                    <div class="luther-blue-mobile-order-wrapper">
                        <?php
                        // Product Categories
                        $categories = get_the_terms($product->get_id(), 'product_cat');
                        if ($categories && !is_wp_error($categories)) {
                            echo '<div class="product-categories">';
                            foreach ($categories as $category) {
                                echo '<span class="category-name">' . esc_html($category->name) . '</span>';
                            }
                            echo '</div>';
                        }

                        // Product Title
                        echo '<h1 class="product-title">' . get_the_title() . '</h1>';

                        // Product Description (replacing price and short description)
                        echo '<div class="product-description">' . apply_filters('the_content', $product->get_description()) . '</div>';
                        
                        // Add separator after description
                        echo '<div class="product-separator"></div>';

                        // ACF Fields - Features
                        if (function_exists('get_field') && get_field('features')) {
                            echo '<div class="product-features">';
                            echo '<h3 class="product-section-title">Features</h3>';
                            echo '<div class="product-section-content">' . get_field('features') . '</div>';
                            echo '</div>';
                        }
                        
                        // ACF Fields - Aroma
                        if (function_exists('get_field') && get_field('aroma')) {
                            echo '<div class="product-aroma">';
                            echo '<h3 class="product-section-title">Aroma</h3>';
                            echo '<div class="product-section-content">' . get_field('aroma') . '</div>';
                            echo '</div>';
                        }
                        
                        // ACF Fields - Key Ingredients
                        if (function_exists('get_field') && have_rows('key_ingredients')) {
                            // Get all ingredients to create a preview
                            $all_ingredients = array();
                            $ingredients_preview = '';
                            
                            // Reset the repeater pointer to the beginning
                            while (have_rows('key_ingredients')) {
                                the_row();
                                $ingredient = get_sub_field('ingredients');
                                if ($ingredient) {
                                    $all_ingredients[] = $ingredient;
                                }
                            }
                            
                            // Create a preview string (comma-separated list limited to 50 chars)
                            if (!empty($all_ingredients)) {
                                $ingredients_preview = implode(', ', $all_ingredients);
                                if (strlen($ingredients_preview) > 50) {
                                    $ingredients_preview = substr($ingredients_preview, 0, 50) . '...';
                                }
                            }
                            
                            echo '<div class="product-ingredients">';
                            echo '<div class="product-section-header">';
                            echo '<h3 class="product-section-title">Key Ingredients</h3>';
                            echo '<button class="ingredients-toggle" aria-expanded="false" aria-controls="ingredients-list">';
                            echo '<span class="toggle-icon">+</span>';
                            echo '</button>';
                            echo '</div>';
                            
                            // Preview text (shown when collapsed)
                            echo '<div class="ingredients-preview">' . esc_html($ingredients_preview) . '</div>';
                            
                            // Full ingredients list (hidden by default)
                            echo '<ul id="ingredients-list" class="product-ingredients-list" style="display: none;">';
                            
                            // Reset the repeater pointer again to list all ingredients
                            reset_rows();
                            while (have_rows('key_ingredients')) {
                                the_row();
                                $ingredient = get_sub_field('ingredients');
                                if ($ingredient) {
                                    echo '<li class="ingredient-item">' . $ingredient . '</li>';
                                }
                            }
                            
                            echo '</ul>';
                            echo '</div>';
                        }

                        // For variable products, we need to ensure variations appear before add to cart
                        if ($product->is_type('variable')) {
                            // This will output the variations form
                            echo '<div class="variations-wrapper">';
                            do_action('woocommerce_before_add_to_cart_form');
                            do_action('woocommerce_variable_add_to_cart');
                            do_action('woocommerce_after_add_to_cart_form');
                            echo '</div>';
                        } else {
                            // Add to Cart Form for simple products
                            echo '<div class="product-add-to-cart">';
                            do_action('woocommerce_before_add_to_cart_form');
                            // For simple products
                            if ($product->is_type('simple')) {
                                do_action('woocommerce_simple_add_to_cart');
                            } else {
                                // For other product types (grouped, external, etc.)
                                do_action('woocommerce_' . $product->get_type() . '_add_to_cart');
                            }
                            // do_action('woocommerce_' . $product->get_type() . '_add_to_cart');
                            do_action('woocommerce_after_add_to_cart_form');
                            echo '</div>';
                        }
                        
                        // Product Meta (SKU, categories, tags)
                        do_action('woocommerce_product_meta_start');
                        do_action('woocommerce_product_meta_end');
                        ?>
                    </div><!-- End Mobile Order Wrapper -->
                </div>
            </div>

            <?php
            // "The Benefits" Section - Image Left, Text Right
            if (function_exists('get_field') && get_field('the_benefits_section')) {
                $benefits_section = get_field('the_benefits_section');
                $benefits_image = isset($benefits_section['benefits_image']) ? $benefits_section['benefits_image'] : '';
                $benefits_content = isset($benefits_section['benefits_content']) ? $benefits_section['benefits_content'] : '';
                
                if ($benefits_image || $benefits_content) {
                    ?>
                    <div class="luther-blue-product-benefits-section">
                        <div class="luther-blue-section-container benefits-container">
                            <div class="luther-blue-section-row benefits-row">
                                <div class="luther-blue-section-image-col benefits-image-col">
                                    <?php if ($benefits_image) : ?>
                                        <!-- Desktop image (background) -->
                                        <div class="benefits-image-wrapper desktop-only">
                                            <div class="luther-blue-section-image benefits-image" style="background-image: url('<?php echo esc_url($benefits_image); ?>');"></div>
                                        </div>
                                        <!-- Mobile image (simple img tag) -->
                                        <img src="<?php echo esc_url($benefits_image); ?>" alt="The Benefits" class="mobile-only">
                                    <?php endif; ?>
                                </div>
                                <div class="luther-blue-section-content-col benefits-content-col">
                                    <?php if ($benefits_content) : ?>
                                        <div class="luther-blue-section-content benefits-content">
                                            <h3 class="section-heading benefits-heading">The Benefits</h3>
                                            <div class="section-content benefits-text">
                                                <?php echo wpautop($benefits_content); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            
            // "How To Use" Section - Text Left, Image Right
            if (function_exists('get_field') && get_field('how_to_use_section')) {
                $how_to_section = get_field('how_to_use_section');
                $how_to_image = isset($how_to_section['how_to_image']) ? $how_to_section['how_to_image'] : '';
                $how_to_content = isset($how_to_section['how_to_content']) ? $how_to_section['how_to_content'] : '';
                
                if ($how_to_image || $how_to_content) {
                    ?>
                    <div class="luther-blue-product-how-to-section">
                        <div class="luther-blue-section-container how-to-container">
                            <div class="luther-blue-section-row how-to-row">
                                <div class="luther-blue-section-content-col how-to-content-col">
                                    <?php if ($how_to_content) : ?>
                                        <div class="luther-blue-section-content how-to-content">
                                            <h3 class="section-heading how-to-heading">How To Use</h3>
                                            <div class="section-content how-to-text">
                                                <?php echo wpautop($how_to_content); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="luther-blue-section-image-col how-to-image-col">
                                    <?php if ($how_to_image) : ?>
                                        <!-- Desktop image (background) -->
                                        <div class="how-to-image-wrapper desktop-only">
                                            <div class="luther-blue-section-image how-to-image" style="background-image: url('<?php echo esc_url($how_to_image); ?>');"></div>
                                        </div>
                                        <!-- Mobile image (simple img tag) -->
                                        <img src="<?php echo esc_url($how_to_image); ?>" alt="How To Use" class="mobile-only">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>

        <?php endwhile; ?>

        <!-- Add You May Also Like Section -->
        <?php 
        if (file_exists(get_template_directory() . '/woocommerce/single-product/recent-products.php')) {
            include get_template_directory() . '/woocommerce/single-product/recent-products.php';
        }
        ?>
        
    </div>
</div>

<?php
do_action('woocommerce_after_main_content');
get_footer('shop');
?> 