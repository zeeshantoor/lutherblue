<?php
/**
 * Custom Login Page Functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

//Enqueue login styles
function luther_blue_login_styles() {
    wp_enqueue_style('luther-blue-fonts', LUTHER_BLUE_URI . '/assets/css/fonts.css', array(), LUTHER_BLUE_VERSION);
    wp_enqueue_style('luther-blue-login', LUTHER_BLUE_URI . '/assets/css/components/login.css', array(), LUTHER_BLUE_VERSION);
    
    // custom CSS variables
    ?>
    <style type="text/css">
        :root {
            --color-primary: #333333;
            --color-secondary: #555555;
            --color-accent: #c9a16e;
            --color-light: #f7f4ec;
            --color-accent-light: #E9E6DA;
            --color-dark: #222222;
            --color-text: #333333;
            --color-text-light: #777777;
            --color-white: #ffffff;
            --font-primary: 'Sohne', sans-serif;
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'luther_blue_login_styles');

//Modify login page logo URL
function luther_blue_login_logo_url() {
    return home_url();
}
add_filter('login_headerurl', 'luther_blue_login_logo_url');

//Modify login page logo title
function luther_blue_login_logo_title() {
    return get_bloginfo('name');
}
add_filter('login_headertext', 'luther_blue_login_logo_title');

//Modify login page logo
function luther_blue_login_logo() {
    // Get the custom logo URL from WordPress
    $custom_logo_id = get_theme_mod('custom_logo');
    $logo_url = '';
    
    if ($custom_logo_id) {
        $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
    } else {
        $logo_url = get_template_directory_uri() . '/assets/images/luther-logo.png';
    }
    
    ?>
    <style type="text/css">
        .login h1 a {
            background-image: url(<?php echo esc_url($logo_url); ?>) !important;
        }
    </style>
    <?php
}
add_action('login_head', 'luther_blue_login_logo');

// Custom body class to login page
function luther_blue_login_body_class($classes) {
    $classes[] = 'luther-login-page';
    return $classes;
}
add_filter('login_body_class', 'luther_blue_login_body_class'); 