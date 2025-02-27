/**
 * Luxury Loaders
 */
(function($) {
    'use strict';
    
    // Create loader element
    function createLoader(type = 'luxury-loader') {
        // Create the loader container
        var $container = $('<div class="loader-container"></div>');
        
        // Create the loader element
        var $loader = $('<div class="' + type + '"></div>');
        
        // Append loader to container
        $container.append($loader);
        
        return $container;
    }
    
    // Show loader
    function showLoader(element, type = 'luxury-loader') {
        var $element = $(element);
        
        // First, remove any existing loaders to prevent duplicates
        $element.find('.loader-container').remove();
        
        // Create a new loader
        var $loader = createLoader(type);
        $element.append($loader);
        
        // Show loader with a slight delay to ensure proper rendering
        setTimeout(function() {
            $loader.addClass('active');
        }, 10);
        
        return $loader;
    }
    
    // Hide loader
    function hideLoader(element) {
        var $element = $(element);
        var $loader = $element.find('.loader-container');
        
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