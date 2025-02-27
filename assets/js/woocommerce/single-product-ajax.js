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
            $(document).on('submit', 'form.cart', this.handleFormSubmit);
            
            // Handle quantity changes
            $(document).on('change', 'form.cart input.qty', this.updateQuantity);
        },
        
        handleFormSubmit: function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $submitButton = $form.find('button[type="submit"]');
            
            // Don't proceed if already loading or added
            if ($submitButton.hasClass('loading')) {
                return;
            }
            
            // Store original button text
            var originalText = $submitButton.html();
            $submitButton.data('original-text', originalText);
            
            // Add loading class
            $submitButton.addClass('loading').prop('disabled', true);
            $submitButton.html('<span class="loading-icon">...</span>');
            
            // Get form data
            var formData = new FormData($form[0]);
            formData.append('action', 'woocommerce_add_to_cart');
            
            // Use AJAX to add to cart
            $.ajax({
                type: 'POST',
                url: woocommerce_params.ajax_url,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Immediately update cart count
                    lutherBlueSingleProductAjax.updateCartCount();
                    
                    // Refresh fragments
                    $(document.body).trigger('wc_fragment_refresh');
                    
                    // Update button with success state
                    $submitButton.removeClass('loading').addClass('added');
                    $submitButton.html('<span class="added-checkmark">✓</span> Added to Cart');
                    
                    // Add a subtle pulse animation to the button
                    $submitButton.addClass('pulse-animation');
                    
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
                        $submitButton.removeClass('added pulse-animation').prop('disabled', false);
                        $submitButton.html(originalText);
                    }, 3000);
                },
                error: function(xhr, status, error) {
                    // Show error message
                    lutherBlueSingleProductAjax.showNotification('Error adding product to cart: ' + error, 'error');
                    
                    // Reset button
                    $submitButton.removeClass('loading').prop('disabled', false);
                    $submitButton.html(originalText);
                }
            });
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