/**
 * AJAX Cart Functionality
 */
(function($) {
    'use strict';
    
    // Cart panel toggle
    $('.cart-toggle, .mobile-cart-toggle').on('click', function(e) {
        e.preventDefault();
        $('#cart-panel').addClass('active');
        $('body').addClass('cart-open');
    });
    
    // Close cart panel
    $('.close-cart, .site-overlay').on('click', function() {
        $('#cart-panel').removeClass('active');
        $('body').removeClass('cart-open');
    });
    
    // Escape key to close cart
    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            $('#cart-panel').removeClass('active');
            $('body').removeClass('cart-open');
        }
    });
    
    // Handle quantity changes
    $(document).on('click', '.quantity-btn', function() {
        var $this = $(this);
        var key = $this.data('key');
        var $quantityValue = $this.siblings('.cart-item-quantity-value');
        var currentQty = parseInt($quantityValue.text());
        var newQty;
        
        if ($this.hasClass('plus')) {
            newQty = currentQty + 1;
        } else if ($this.hasClass('minus')) {
            newQty = Math.max(0, currentQty - 1);
        }
        
        updateCartItem(key, newQty);
    });
    
    // Handle remove item
    $(document).on('click', '.remove-item', function() {
        var key = $(this).data('key');
        updateCartItem(key, 0);
    });
    
    // Update cart item function
    function updateCartItem(key, quantity) {
        // Remove any existing item loaders
        $('.cart-item-loader').remove();
        
        // Show single main loader
        if (window.LuxuryLoaders) {
            window.LuxuryLoaders.show('.cart-content');
        }
        
        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'luther_blue_update_cart_item',
                key: key,
                quantity: quantity,
                nonce: ajax_object.nonce
            },
            beforeSend: function() {
                $('#cart-panel').addClass('loading');
            },
            success: function(response) {
                refreshCart();
            }
        });
    }
    
    // Refresh cart contents
    function refreshCart() {
        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'luther_blue_get_mini_cart',
                nonce: ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Update cart content
                    $('.cart-content').html(response.data.cart_html);
                    
                    // Update cart count
                    $('.cart-count').text(response.data.cart_count);
                    
                    // Update cart header count
                    $('.cart-header h2').text('Your Cart (' + response.data.cart_count + ')');
                    
                    // Update cart subtotal
                    $('.subtotal-value').html(response.data.cart_subtotal);
                    
                    // Update checkout button text with new subtotal
                    var checkoutText = $('.checkout-btn').text().split('•')[0];
                    $('.checkout-btn').html(checkoutText + '<span class="separator">•</span> ' + response.data.cart_subtotal);
                    
                    // Remove loading state
                    $('#cart-panel').removeClass('loading');
                    
                    // Hide loader
                    if (window.LuxuryLoaders) {
                        window.LuxuryLoaders.hide('.cart-content');
                    }
                }
            }
        });
    }
    
})(jQuery); 