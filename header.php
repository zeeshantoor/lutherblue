<?php
/**
 * The header for our theme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php 
    if (function_exists('get_field') && get_field('enable_notification_banner', 'option')) {
        wp_enqueue_style('notifications', get_template_directory_uri() . '/assets/css/components/notifications.css');
    }
    wp_head(); 
    ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Site Overlay -->
<div class="site-overlay"></div>

<div id="page" class="site">
    <?php
    // Top notification banner
    if (function_exists('get_field') && get_field('enable_notification_banner', 'option')) {
        $notification_text = get_field('notification_text', 'option');
        $bg_color = get_field('notification_bg_color', 'option');
        $text_color = get_field('notification_text_color', 'option');
        
        if (!empty($notification_text)) {
            ?>
            <div id="top-notification" style="background-color: <?php echo esc_attr($bg_color); ?>; color: <?php echo esc_attr($text_color); ?>;">
                <div class="container">
                    <div class="notification-content">
                        <?php echo esc_html($notification_text); ?>
                    </div>
                    <button id="close-notification" aria-label="Close notification">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <?php
        }
    }
    ?>

    <header id="masthead" class="site-header">
        <div class="container">
            <!-- Desktop Header -->
            <div class="desktop-header">
                <!-- Left Column - Primary Menu -->
                <nav class="primary-navigation">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'container'      => false,
                        'fallback_cb'    => false,
                        'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        'depth'          => 1,
                    ));
                    ?>
                </nav>

                <!-- Middle Column - Logo -->
                <div class="site-branding">
                    <?php
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        ?>
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php
                    }
                    ?>
                </div>

                <!-- Right Column - Shop/Login/Cart -->
                <div class="header-actions">
                    <ul>
                        <li><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Shop All</a></li>
                        <li><a href="<?php echo esc_url(wp_login_url()); ?>">Login</a></li>
                        <li class="cart-item">
                            <button class="cart-toggle">
                                Cart (<span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>)
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Mobile Header -->
            <div class="mobile-header">
                <!-- Left Column - Menu Toggle -->
                <button class="menu-toggle" aria-controls="mobile-menu" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <!-- Middle Column - Logo -->
                <div class="site-branding">
                    <?php
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        ?>
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php
                    }
                    ?>
                </div>

                <!-- Right Column - Cart -->
                <button class="mobile-cart-toggle">
                    Cart (<span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>)
                </button>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div id="mobile-menu-panel" class="mobile-menu-panel">
            <div class="mobile-menu-close">
                <span class="close-text">TAP TO CLOSE THE MENU</span>
                <span class="close-icon">&times;</span>
            </div>
            <div class="mobile-menu-inner">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'mobile-menu',
                    'container'      => false,
                    'fallback_cb'    => false,
                ));
                ?>
                <ul class="mobile-secondary-menu">
                    <li><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Shop All</a></li>
                    <li><a href="<?php echo esc_url(wp_login_url()); ?>">Login</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div id="content" class="site-content">
