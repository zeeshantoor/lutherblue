(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Mobile menu toggle
        $('.menu-toggle').on('click', function() {
            $(this).toggleClass('active');
            $('#mobile-menu-panel').toggleClass('active');
            $('body').toggleClass('menu-open');
        });

        // Mobile menu close button
        $('.mobile-menu-close').on('click', function() {
            $('#mobile-menu-panel').removeClass('active');
            $('body').removeClass('menu-open');
            $('.menu-toggle').removeClass('active');
        });

        // Desktop cart toggle
        $('.cart-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#cart-panel').addClass('active');
            $('body').addClass('cart-open');
        });

        // Mobile cart toggle
        $('.mobile-cart-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#cart-panel').addClass('active');
            $('body').addClass('cart-open');
        });

        // Close cart buttons
        $('.close-cart, .cart-close').on('click', function() {
            $('#cart-panel').removeClass('active');
            $('body').removeClass('cart-open');
        });

        // Close cart when clicking on overlay
        $('.site-overlay').on('click', function() {
            $('#cart-panel').removeClass('active');
            $('body').removeClass('cart-open');
            $('#mobile-menu-panel').removeClass('active');
            $('body').removeClass('menu-open');
            $('.menu-toggle').removeClass('active');
        });

        // Update cart count via AJAX
        $(document.body).on('added_to_cart removed_from_cart', function(e, fragments) {
            if (fragments && fragments['div.widget_shopping_cart_content']) {
                $('.cart-count').text(fragments.cart_count);
            }
        });

        // Close notification banner
        $('#close-notification').on('click', function() {
            $('#top-notification').slideUp();
        });

        // Preserve shipping notice when cart is refreshed
        $(document.body).on('wc_fragments_refreshed', function() {
            // If the shipping notice is missing after refresh, re-add it
            if ($('.cart-footer-container').length && !$('.shipping-notice').length) {
                $.ajax({
                    url: luther_blue_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'luther_blue_get_shipping_notice',
                        nonce: luther_blue_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.cart-footer').append('<div class="shipping-notice">' + response.data.notice + '</div>');
                        }
                    }
                });
            }
        });

        // Modify cart links to open AJAX cart
        $('body').on('click', 'a[href*="cart"]', function(e) {
            // Don't interfere with checkout links
            if ($(this).attr('href').indexOf('checkout') === -1) {
                e.preventDefault();
                $('#cart-panel').addClass('active');
                $('body').addClass('cart-open');
            }
        });

        // Update add to cart behavior
        $('body').on('added_to_cart', function(e, fragments, cart_hash) {
            // Open cart panel when item is added
            // $('#cart-panel').addClass('active');
            // $('body').addClass('cart-open');
            
            // Update cart count
            if (fragments && fragments['span.cart-count']) {
                $('.cart-count').html(fragments['span.cart-count']);
            }
        });
    });
    
    // Handle responsive behavior
    $(window).on('resize', function() {
        if ($(window).width() > 768) {
            $('#primary-menu').removeClass('active');
            $('.menu-toggle').removeClass('active');
        }
    });
    
})(jQuery);
