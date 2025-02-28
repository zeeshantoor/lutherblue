/**
 * Luther Blue Theme - Single Product AJAX Add to Cart
 * 
 * Handles AJAX add to cart functionality for the single product page.
 */

(function($) {
    'use strict';
    
    // Initialize when the DOM is ready
    $(document).ready(function() {
        lutherBlueSingleProductAjax.init();
    });
    
    // Single Product AJAX Object
    var lutherBlueSingleProductAjax = {
        init: function() {
            this.bindEvents();
        },
        
        bindEvents: function() {
            // Override the add to cart form submission
            // Use direct binding instead of event delegation to prevent multiple triggers
            $(document).on('click', 'form.cart button[type="submit"]', this.handleAddToCart);
            
            // Handle quantity changes
            $(document).on('change', 'form.cart input.qty', this.updateQuantity);
        },
        
        // Handle add to cart button click instead of form submission
        handleAddToCart: function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $button = $(this);
            var $form = $button.closest('form.cart');
            
            // Don't proceed if already loading or added
            if ($button.hasClass('loading')) {
                return;
            }
            
            // Store original button text
            var originalText = $button.html();
            $button.data('original-text', originalText);
            
            // Add loading class
            $button.addClass('loading').prop('disabled', true);
            $button.html('<span class="loading-icon">...</span>');
            
            // Serialize form data
            var serializedData = $form.serialize();
            
            // Get product ID
            var product_id = $form.find('input[name="add-to-cart"]').val();
            // If not found in input, check button value
            if (!product_id) {
                product_id = $button.val();
            }
            
            // For variation products
            var variation_id = $form.find('input[name="variation_id"]').val();
            
            // Build data object
            var data = serializedData;
            
            // Add action for WooCommerce
            data += '&action=woocommerce_ajax_add_to_cart';
            
            // Ensure product_id is included for simple products
            if (!data.includes('add-to-cart=') && product_id) {
                data += '&add-to-cart=' + product_id;
            }
            
            // If no quantity is specified, add it
            if (!data.includes('quantity=')) {
                data += '&quantity=1';
            }
            
            // Use AJAX to add to cart
            $.ajax({
                type: 'POST',
                url: woocommerce_params.ajax_url,
                data: data,
                success: function(response) {
                    // Try to parse response if it's a string
                    if (typeof response === 'string') {
                        try {
                            response = JSON.parse(response);
                        } catch(e) {
                            // If parsing fails, continue anyway as it might be HTML
                            console.log('Response parsing error:', e);
                        }
                    }
                    
                    // Handle error response
                    if (response && response.error) {
                        lutherBlueSingleProductAjax.showNotification(response.message || 'Error adding product to cart', 'error');
                        
                        // Reset button
                        $button.removeClass('loading').prop('disabled', false);
                        $button.html(originalText);
                        return;
                    }
                    
                    // Even if we get a malformed response, if we get a 200 status, assume it worked
                    // and continue with cart updates
                    
                    // Immediately update cart count
                    lutherBlueSingleProductAjax.updateCartCount();
                    
                    // Refresh fragments
                    $(document.body).trigger('wc_fragment_refresh');
                    
                    // Update button with success state
                    $button.removeClass('loading').addClass('added');
                    $button.html('<span class="added-checkmark">✓</span> Added to Cart');
                    
                    // Add a subtle pulse animation to the button
                    $button.addClass('pulse-animation');
                    
                    // Open the cart panel with a smooth animation
                    setTimeout(function() {
                        // Preload cart content before opening panel
                        lutherBlueSingleProductAjax.preloadCartContent(function() {
                            // Remove any loading classes
                            $('#cart-panel').removeClass('loading');
                            
                            // Prepare the cart panel with a subtle glow
                            $('#cart-panel').addClass('preparing');
                            
                            // Open the cart panel after a tiny delay
                            setTimeout(function() {
                                $('#cart-panel').removeClass('preparing').addClass('active elegant-entry');
                                $('body').addClass('cart-open');
                                
                                // Remove the elegant entry class after animation completes
                                setTimeout(function() {
                                    $('#cart-panel').removeClass('elegant-entry');
                                }, 800);
                            }, 50);
                        });
                    }, 500);
                    
                    // Reset button after a delay
                    setTimeout(function() {
                        $button.removeClass('added pulse-animation').prop('disabled', false);
                        $button.html(originalText);
                    }, 3000);
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:', xhr.responseText);
                    
                    // Check if product was actually added despite error
                    // This is a fallback for when the server returns an error but the product was actually added
                    lutherBlueSingleProductAjax.checkIfProductWasAdded(function(wasAdded) {
                        if (wasAdded) {
                            // Product was added despite error, so proceed as if successful
                            // Update button with success state
                            $button.removeClass('loading').addClass('added');
                            $button.html('<span class="added-checkmark">✓</span> Added to Cart');
                            
                            // Add a subtle pulse animation to the button
                            $button.addClass('pulse-animation');
                            
                            // Immediately update cart count
                            lutherBlueSingleProductAjax.updateCartCount();
                            
                            // Refresh fragments
                            $(document.body).trigger('wc_fragment_refresh');
                            
                            // Open the cart panel with a smooth animation
                            setTimeout(function() {
                                // Preload cart content before opening panel
                                lutherBlueSingleProductAjax.preloadCartContent(function() {
                                    // Remove any loading classes
                                    $('#cart-panel').removeClass('loading');
                                    
                                    // Prepare the cart panel with a subtle glow
                                    $('#cart-panel').addClass('preparing');
                                    
                                    // Open the cart panel after a tiny delay
                                    setTimeout(function() {
                                        $('#cart-panel').removeClass('preparing').addClass('active elegant-entry');
                                        $('body').addClass('cart-open');
                                        
                                        // Remove the elegant entry class after animation completes
                                        setTimeout(function() {
                                            $('#cart-panel').removeClass('elegant-entry');
                                        }, 800);
                                    }, 50);
                                });
                            }, 500);
                            
                            // Reset button after a delay
                            setTimeout(function() {
                                $button.removeClass('added pulse-animation').prop('disabled', false);
                                $button.html(originalText);
                            }, 3000);
                        } else {
                            // Show error message
                            lutherBlueSingleProductAjax.showNotification('Error adding product to cart: ' + error, 'error');
                            
                            // Reset button
                            $button.removeClass('loading').prop('disabled', false);
                            $button.html(originalText);
                        }
                    });
                }
            });
        },
        
        // Check if a product was actually added despite AJAX error
        checkIfProductWasAdded: function(callback) {
            // Get current cart count and check again in a moment
            var currentCount = $('.cart-count').first().text();
            currentCount = parseInt(currentCount) || 0;
            
            // Check cart count after a short delay
            setTimeout(function() {
                $.ajax({
                    type: 'POST',
                    url: ajax_object.ajax_url,
                    data: {
                        action: 'luther_blue_get_cart_count',
                        nonce: ajax_object.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            var newCount = parseInt(response.data.count) || 0;
                            // If cart count increased, the product was successfully added
                            callback(newCount > currentCount);
                        } else {
                            callback(false);
                        }
                    },
                    error: function() {
                        callback(false);
                    }
                });
            }, 500);
        },
        
        updateQuantity: function() {
            // This function can be used to update price or other elements when quantity changes
            // For now, we'll just ensure the button is reset if it was in an "added" state
            var $form = $(this).closest('form.cart');
            var $submitButton = $form.find('button[type="submit"]');
            
            if ($submitButton.hasClass('added')) {
                $submitButton.removeClass('added pulse-animation');
                $submitButton.html($submitButton.data('original-text') || 'Add to Cart');
            }
        },
        
        // Update cart count immediately without waiting for fragment refresh
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
        
        // Preload cart content before opening panel
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
        
        // Show notification
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
