<?php
/**
 * Recent Products - You May Also Like
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product;

// Get current product ID to exclude
$current_product_id = $product->get_id();

// Query recent products
$args = array(
    'post_type' => 'product',
    'posts_per_page' => 6,
    'post__not_in' => array($current_product_id),
    'orderby' => 'date',
    'order' => 'DESC',
);

$products = new WP_Query($args);

if ($products->have_posts()) : ?>
    <section class="luther-blue-recent-products">
        <h2 class="recent-products-heading">You may also like</h2>
        
        <div class="luther-blue-products-grid recent-products-carousel">
            <div class="luther-blue-products-row carousel-track">
                <?php while ($products->have_posts()) : $products->the_post(); ?>
                    <?php 
                    global $product;
                    $categories = get_the_terms($product->get_id(), 'product_cat');
                    ?>
                    
                    <div class="luther-blue-product-item recent-product-item">
                        <div class="luther-blue-product-image">
                            <a href="<?php echo esc_url(get_permalink()); ?>">
                                <?php echo get_the_post_thumbnail($product->get_id(), 'medium', array('class' => 'luther-blue-product-thumbnail')); ?>
                            </a>
                        </div>

                        <?php if ($categories && !is_wp_error($categories)) : ?>
                            <div class="luther-blue-product-categories">
                                <?php foreach ($categories as $category) : ?>
                                    <span class="category-item"><?php echo esc_html($category->name); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="luther-blue-product-divider"></div>

                        <div class="luther-blue-product-details-container">
                            <div class="luther-blue-product-details-row">
                                <div class="luther-blue-product-title-column">
                                    <h2 class="luther-blue-product-title">
                                        <a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a>
                                    </h2>
                                </div>
                                <div class="luther-blue-product-price-column">
                                    <div class="luther-blue-product-price">
                                        <?php echo $product->get_price_html(); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Add Excerpt Section -->
                            <div class="luther-blue-product-details-row">
                                <div class="luther-blue-product-excerpt-column">
                                    <?php
                                    $excerpt = $product->get_short_description();
                                    if (empty($excerpt)) {
                                        $excerpt = wp_trim_words(get_the_content(), 15, '...');
                                    } else {
                                        $excerpt = wp_trim_words($excerpt, 15, '...');
                                    }
                                    ?>
                                    <div class="luther-blue-product-excerpt"><?php echo $excerpt; ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="luther-blue-product-actions">
                            <?php if ($product->is_type('variable')) : ?>
                                <a href="<?php echo esc_url(get_permalink()); ?>" class="luther-blue-select-product-btn">Select Product</a>
                            <?php else : ?>
                                <a href="?add-to-cart=<?php echo esc_attr($product->get_id()); ?>" 
                                   class="luther-blue-add-to-cart-btn" 
                                   data-product_id="<?php echo esc_attr($product->get_id()); ?>">
                                    Add to Cart
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
<?php endif; 

wp_reset_postdata(); 