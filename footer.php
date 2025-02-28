<?php
/**
 * The template for displaying the footer
 */

 if (!defined('ABSPATH')) {
    exit;
}
?>

    </main><!-- #main -->

    <footer id="colophon" class="site-footer">
        <div class="container">
            
            <div class="footer-bottom">
                <div class="copyright">
                    &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.
                </div>
                <?php if (has_nav_menu('footer')) : ?>
                    <nav class="footer-navigation">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'menu_class'     => 'footer-menu',
                            'depth'          => 1,
                        ));
                        ?>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
