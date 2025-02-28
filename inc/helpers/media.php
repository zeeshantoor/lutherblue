<?php
/**
 * Luther Blue Theme - Media handling functions
 */

if (!defined('ABSPATH')) {
    exit;
}

//Enable WebP and SVG support
function luther_blue_mime_types($mimes) {
    // Add WebP support
    $mimes['webp'] = 'image/webp';
    
    // Add SVG support
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    
    return $mimes;
}
add_filter('upload_mimes', 'luther_blue_mime_types');

//Fix SVG display in Media Library
function luther_blue_fix_svg_display() {
    echo '<style>
        .attachment-266x266, .thumbnail img {
            width: 100% !important;
            height: auto !important;
        }
    </style>';
}
add_action('admin_head', 'luther_blue_fix_svg_display');

//Enable SVG preview in Media Library
function luther_blue_svg_media_thumbnails($response, $attachment, $meta) {
    if ($response['type'] === 'image' && $response['subtype'] === 'svg+xml') {
        $attachment_url = $response['url'];
        $response['image'] = [
            'src' => $attachment_url
        ];
    }
    
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'luther_blue_svg_media_thumbnails', 10, 3);

//Add WebP to allowed image sizes for thumbnails
function luther_blue_file_is_displayable_image($result, $path) {
    if ($result === false) {
        $displayable_image_types = array(IMAGETYPE_WEBP);
        $info = @getimagesize($path);

        if (empty($info)) {
            $result = false;
        } elseif (!in_array($info[2], $displayable_image_types)) {
            $result = false;
        } else {
            $result = true;
        }
    }

    return $result;
}
add_filter('file_is_displayable_image', 'luther_blue_file_is_displayable_image', 10, 2); 