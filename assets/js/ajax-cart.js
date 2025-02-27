/**
 * AJAX Cart Functionality
 */
(function($) {
    'use strict';
    
    // Cart panel toggle
    $('.cart-toggle, .mobile-cart-toggle').on('click', function(e) {
        e.preventDefault();
        
        // Preload cart content before showing panel
        refreshCart(function() {
            $('#cart-panel').addClass('active');
            $('body').addClass('cart-open');
        });
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
        // First, ensure any existing loaders are removed
        $('.cart-item-loader').remove();
        
        // Remove any existing loaders before showing a new one
        if (window.LuxuryLoaders) {
            window.LuxuryLoaders.hide('.cart-content');
            // Show single main loader
            window.LuxuryLoaders.show('.cart-content');
        }
        
        // Immediately update the UI for better responsiveness
        if (quantity === 0) {
            // If removing item, hide it immediately for better UX
            $('.cart-item[data-key="' + key + '"]').fadeOut(200);
        } else {
            // If changing quantity, update the display immediately
            $('.cart-item[data-key="' + key + '"] .cart-item-quantity-value').text(quantity);
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
                // Add loading class to cart panel
                $('#cart-panel').addClass('loading');
            },
            success: function(response) {
                refreshCart();
            }
        });
    }
    
    // Refresh cart contents
    function refreshCart(callback) {
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
                    
                    // Execute callback if provided
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            }
        });
    }
    
    // Listen for WooCommerce fragment refresh events
    $(document.body).on('wc_fragments_refreshed', function() {
        // Update our cart count when fragments are refreshed
        updateCartCount();
    });
    
    // Function to update cart count only (lightweight)
    function updateCartCount() {
        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'luther_blue_get_cart_count',
                nonce: ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Update all cart count elements
                    $('.cart-count').text(response.data.count);
                    // Update cart header count if visible
                    $('.cart-header h2').text('Your Cart (' + response.data.count + ')');
                }
            }
        });
    }
    
})(jQuery); 