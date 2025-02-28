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

        // Close notification banner
        $('#close-notification').on('click', function() {
            $('#top-notification').slideUp();
        });

        // Site overlay click handler
        $('.site-overlay').on('click', function() {
            $('#mobile-menu-panel').removeClass('active');
            $('body').removeClass('menu-open');
            $('.menu-toggle').removeClass('active');
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
