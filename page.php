<?php
/**
 * The template for displaying all pages
 */
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            
            get_template_part('parts/content', 'page');
            
        endwhile;
        ?>
    </div>
</main>

<?php
get_footer(); 