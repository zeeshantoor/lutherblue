/**
 * Luther Blue Theme - Custom WooCommerce Product Loop JS
 */

(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        lutherBlueProductLoop.init();
    });
    
    // Product Loop Object
    var lutherBlueProductLoop = {
        init: function() {
            this.bindEvents();
        },
        
        bindEvents: function() {
            // Add to cart button click
            $(document).on('click', '.luther-blue-add-to-cart-btn', this.addToCart);
            
            // Handle added_to_cart event
            $(document.body).on('added_to_cart', this.updateButton);
        },
        
        addToCart: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var productId = $button.data('product_id');
            
            // Don't proceed if already loading or added
            if ($button.hasClass('loading')) {
                return;
            }
            
            // Store original button text
            var originalText = $button.html();
            $button.data('original-text', originalText);
            
            // Add loading class
            $button.addClass('loading');
            
            // Use WooCommerce's built-in AJAX functionality
            $.ajax({
                type: 'POST',
                url: woocommerce_params.ajax_url,
                data: {
                    action: 'woocommerce_add_to_cart',
                    product_id: productId,
                    quantity: 1
                },
                success: function(response) {
                    // Immediately update cart count without waiting for fragment refresh
                    lutherBlueProductLoop.updateCartCount();
                    
                    // Refresh fragments (this is still needed for other elements)
                    $(document.body).trigger('wc_fragment_refresh');
                    
                    // Create a luxurious animation sequence
                    
                    // 1. First, update button with elegant checkmark animation
                    $button.removeClass('loading').addClass('added');
                    
                    // Get price part from the original button text
                    var pricePart = '';
                    if (originalText.indexOf('<span class="product-price-dot">') !== -1) {
                        pricePart = originalText.substring(originalText.indexOf('<span class="product-price-dot">'));
                    }
                    
                    // Change text to elegant "Added" with checkmark icon and keep the price part
                    $button.html('<span class="added-checkmark">✓</span> Added ' + pricePart);
                    
                    // Add a subtle pulse animation to the button
                    $button.addClass('pulse-animation');
                    
                    // 2. After a shorter delay (500ms instead of 1000ms), open the cart with a smooth animation
                    setTimeout(function() {
                        // Preload cart content before opening panel to make it faster
                        lutherBlueProductLoop.preloadCartContent(function() {
                            // First, ensure any existing loaders are removed
                            if (window.LuxuryLoaders) {
                                window.LuxuryLoaders.hide('.cart-content');
                            }
                            
                            // Remove any loading classes
                            $('#cart-panel').removeClass('loading');
                            
                            // First prepare the cart panel with a subtle glow
                            $('#cart-panel').addClass('preparing');
                            
                            // Then after a tiny delay for visual effect, open it
                            setTimeout(function() {
                                $('#cart-panel').removeClass('preparing').addClass('active elegant-entry');
                                $('body').addClass('cart-open');
                                
                                // Remove the elegant entry class after animation completes
                                setTimeout(function() {
                                    $('#cart-panel').removeClass('elegant-entry');
                                }, 800);
                            }, 50); // Reduced from 100ms to 50ms
                        });
                    }, 500); // Reduced from 1000ms to 500ms
                    
                    // 3. Reset button after the sequence completes
                    setTimeout(function() {
                        $button.removeClass('added pulse-animation');
                        $button.html(originalText);
                    }, 1500);
                },
                error: function() {
                    // Show error message
                    lutherBlueProductLoop.showNotification('Error adding product to cart', 'error');
                    // Remove loading class
                    $button.removeClass('loading');
                },
                complete: function() {
                    // Remove loading class if not already removed in success callback
                    if (!$button.hasClass('added')) {
                        $button.removeClass('loading');
                    }
                }
            });
        },
        
        // New method to update cart count immediately without waiting for fragment refresh
        updateCartCount: function() {
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
                        // Update cart header count
                        $('.cart-header h2').text('Your Cart (' + response.data.count + ')');
                    }
                }
            });
        },
        
        // New method to preload cart content before opening panel
        preloadCartContent: function(callback) {
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
                        
                        // Execute callback when everything is ready
                        if (typeof callback === 'function') {
                            callback();
                        }
                    }
                }
            });
        },
        
        updateButton: function(event, fragments, cart_hash, $button) {
            // Add 'added' class to button
            $button.addClass('added');
            
            // Get price part from the original button text
            var originalText = $button.data('original-text') || $button.html();
            var pricePart = '';
            
            if (originalText.indexOf('<span class="product-price-dot">') !== -1) {
                pricePart = originalText.substring(originalText.indexOf('<span class="product-price-dot">'));
            }
            
            // Change text to elegant "Added" with checkmark and keep the price part
            $button.html('<span class="added-checkmark">✓</span> Added ' + pricePart);
            
            // Add a subtle pulse animation to the button
            $button.addClass('pulse-animation');
            
            // Reset button after animation sequence
            setTimeout(function() {
                $button.removeClass('added pulse-animation');
                $button.html(originalText);
            }, 3000);
        },
        
        showNotification: function(message, type) {
            // Check if notification container exists
            var $container = $('.luther-blue-notifications');
            if ($container.length === 0) {
                // Create container if it doesn't exist
                $container = $('<div class="luther-blue-notifications"></div>');
                $('body').append($container);
            }
            
            // Create notification
            var $notification = $('<div class="luther-blue-notification luther-blue-notification-' + type + '">' + message + '</div>');
            
            // Add to container
            $container.append($notification);
            
            // Show notification
            setTimeout(function() {
                $notification.addClass('show');
            }, 10);
            
            // Remove notification after 3 seconds
            setTimeout(function() {
                $notification.removeClass('show');
                setTimeout(function() {
                    $notification.remove();
                }, 300);
            }, 3000);
        }
    };
    
})(jQuery); 