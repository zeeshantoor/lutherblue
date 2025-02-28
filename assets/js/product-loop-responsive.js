/**
 * Luther Blue Theme - Responsive Product Loop Handler
 * This script ensures that product grid layouts maintain proper proportions
 * and that categories don't get cut off when screen size changes.
 */

(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        lutherBlueResponsiveLayout.init();
    });
    
    // Handle window resize events
    $(window).on('resize', function() {
        lutherBlueResponsiveLayout.adjustLayout();
    });
    
    // Responsive Layout Object
    var lutherBlueResponsiveLayout = {
        init: function() {
            this.adjustLayout();
            this.bindEvents();
        },
        
        bindEvents: function() {
            // Re-adjust layout after images load to prevent layout shifts
            $('.luther-blue-product-thumbnail').on('load', function() {
                lutherBlueResponsiveLayout.adjustLayout();
            });
            
            // Handle any dynamic content changes
            $(document).on('ajaxComplete', function() {
                setTimeout(function() {
                    lutherBlueResponsiveLayout.adjustLayout();
                }, 100);
            });
        },
        
        adjustLayout: function() {
            // Ensure product categories have enough height
            $('.luther-blue-product-categories').each(function() {
                var $this = $(this);
                var contentHeight = $this[0].scrollHeight;
                
                // If content is taller than the container, increase the min-height
                if (contentHeight > $this.height()) {
                    $this.css('min-height', contentHeight + 'px');
                }
            });
            
            // Ensure product items in the same row have equal heights
            $('.luther-blue-products-row').each(function() {
                var $row = $(this);
                var $items = $row.find('.luther-blue-product-item');
                
                // Reset heights first
                $items.css('height', 'auto');
                
                // Skip if we're on mobile (single column)
                if (window.innerWidth <= 576) {
                    return;
                }
                
                // Find the tallest item
                var maxHeight = 0;
                $items.each(function() {
                    var height = $(this).outerHeight();
                    maxHeight = Math.max(maxHeight, height);
                });
                
                // Set all items to the same height
                if (maxHeight > 0) {
                    $items.css('height', maxHeight + 'px');
                }
            });
            
            // Ensure image containers maintain proper proportions
            $('.luther-blue-product-image').each(function() {
                var $image = $(this);
                var $img = $image.find('img');
                
                // If image has loaded
                if ($img.length && $img[0].complete) {
                    // Make sure image is centered and contained
                    $img.css({
                        'max-height': '100%',
                        'max-width': '100%',
                        'object-fit': 'contain'
                    });
                }
            });
        }
    };
    
})(jQuery); 