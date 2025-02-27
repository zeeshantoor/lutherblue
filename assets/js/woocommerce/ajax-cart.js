(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Initialize ajax cart
        initAjaxCart();
    });
    
    function initAjaxCart() {
        // Listen for the add-to-cart button click
        $(document).on('click', '.add_to_cart_button', function(e) {
            // If button has data-product_id attribute, it's an AJAX add to cart button
            if ($(this).hasClass('ajax_add_to_cart')) {
                // Show loading state
                $(this).addClass('loading');
            }
        });
        
        // Listen for the added_to_cart event
        $(document).on('added_to_cart', function(event, fragments, cart_hash, $button) {
            if ($button) {
                // Remove loading state
                $button.removeClass('loading');
                
                // Add 'added' class for styling
                $button.addClass('added');
                
                // Update the cart count display in header
                updateCartCountDisplay(fragments);
                
                // After a short delay, remove the 'added' class
                setTimeout(function() {
                    $button.removeClass('added');
                }, 2000);
            }
        });
        
        // Handle cart quantity changes
        $(document).on('change', '.woocommerce-cart-form .qty', function() {
            var $form = $(this).closest('form');
            var $updateButton = $form.find('button[name="update_cart"]');
            
            // Enable update button
            $updateButton.prop('disabled', false);
            
            // Trigger update button
            $updateButton.trigger('click');
        });
        
        // Refresh fragments when cart is updated
        $(document).on('updated_cart_totals', function() {
            $(document.body).trigger('wc_fragment_refresh');
        });
        
        // Listen for removed_from_cart events
        $(document).on('removed_from_cart', function(event, fragments, cart_hash, $button) {
            // Update the cart count display
            updateCartCountDisplay(fragments);
        });
    }
    
    // Update cart count in header
    function updateCartCountDisplay(fragments) {
        // If fragments are passed
        if (fragments) {
            // Find the cart count element in header
            var $cartCount = $('.cart-count');
            
            // Get the count from fragments
            var count = 0;
            if (fragments['cart-count']) {
                count = parseInt(fragments['cart-count']);
            }
            
            // Update the count display
            if ($cartCount.length) {
                $cartCount.text(count);
                
                // Show/hide based on count
                if (count > 0) {
                    $cartCount.addClass('has-items');
                } else {
                    $cartCount.removeClass('has-items');
                }
            }
        }
    }
    
})(jQuery); 