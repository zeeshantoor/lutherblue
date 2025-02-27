/**
 * Luxury Loaders
 */
(function($) {
    'use strict';
    
    // Create loader element
    function createLoader(type = 'luxury-loader') {
        return $('<div class="loader-container"><div class="' + type + '"></div></div>');
    }
    
    // Show loader
    function showLoader(element, type = 'luxury-loader') {
        var $element = $(element);
        var $loader = $element.find('.loader-container');
        
        // Create loader if it doesn't exist
        if ($loader.length === 0) {
            $loader = createLoader(type);
            $element.append($loader);
        }
        
        // Show loader
        setTimeout(function() {
            $loader.addClass('active');
        }, 10);
        
        return $loader;
    }
    
    // Hide loader
    function hideLoader(element) {
        var $loader = $(element).find('.loader-container');
        
        if ($loader.length) {
            $loader.removeClass('active');
            
            // Remove loader after transition
            setTimeout(function() {
                $loader.remove();
            }, 300);
        }
    }
    
    // Expose functions globally
    window.LuxuryLoaders = {
        create: createLoader,
        show: showLoader,
        hide: hideLoader
    };
    
})(jQuery); 