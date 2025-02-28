/**
 * Luther Blue Theme - Single Product Mobile Layout
 * 
 * Handles mobile-specific behavior for the single product page
 */

(function($) {
    'use strict';
    
    // Initialize when the DOM is ready
    $(document).ready(function() {
        lutherBlueSingleProductMobile.init();
    });
    
    // Single Product Mobile Object
    var lutherBlueSingleProductMobile = {
        init: function() {
            this.setupMobileLayout();
            this.bindEvents();
            this.setupGalleryNavigation();
        },
        
        bindEvents: function() {
            // Handle window resize events
            $(window).on('resize', this.handleResize.bind(this));
            
            // Handle orientation change on mobile devices
            $(window).on('orientationchange', this.handleResize.bind(this));
            
            // Handle gallery navigation clicks
            $(document).on('click', '.gallery-nav-up', this.scrollGalleryLeft);
            $(document).on('click', '.gallery-nav-down', this.scrollGalleryRight);
        },
        
        setupMobileLayout: function() {
            // Check if we're on mobile view (up to 1024px, excluding iPad Pro)
            if (this.shouldApplyMobileLayout()) {
                this.applyMobileLayout();
            }
        },
        
        handleResize: function() {
            // Debounce the resize event
            clearTimeout(this.resizeTimer);
            this.resizeTimer = setTimeout(function() {
                // Check if we're on mobile view
                if (this.shouldApplyMobileLayout()) {
                    this.applyMobileLayout();
                    this.setupGalleryNavigation();
                } else {
                    this.resetToDesktopLayout();
                }
            }.bind(this), 250);
        },
        
        shouldApplyMobileLayout: function() {
            // Apply mobile layout to devices up to 1024px wide, but exclude iPad Pro
            var isIpadPro = window.navigator.userAgent.match(/iPad/) && 
                            window.devicePixelRatio >= 2 && 
                            (window.innerWidth >= 1024 && window.innerWidth <= 1366);
            
            return window.innerWidth <= 1024 && !isIpadPro;
        },
        
        applyMobileLayout: function() {
            // Move the main image to be the first element in the mobile order wrapper
            var $mainImage = $('.luther-blue-product-main-image');
            var $mobileWrapper = $('.luther-blue-mobile-order-wrapper');
            
            // Only move if not already moved
            if (!$mainImage.hasClass('mobile-moved')) {
                $mainImage.addClass('mobile-moved');
                $mainImage.prependTo($mobileWrapper);
            }
            
            // Move the gallery thumbs after the description
            var $galleryThumbs = $('.luther-blue-product-gallery-thumbs');
            var $description = $('.product-description');
            
            // Only move if not already moved
            if (!$galleryThumbs.hasClass('mobile-moved')) {
                $galleryThumbs.addClass('mobile-moved');
                $galleryThumbs.insertAfter($description);
                
                // Make gallery full width
                $galleryThumbs.css('width', '100%');
            }
            
            // For variable products, we need to ensure variations appear above add to cart button
            var $variationsForm = $('.variations_form.cart');
            var $variationsWrapper = $('.variations-wrapper');
            
            // If we have a variations wrapper, ensure it's properly ordered
            if ($variationsWrapper.length) {
                $variationsWrapper.css({
                    'order': '7',
                    'margin-bottom': '20px',
                    'padding': '0 15px'
                });
            }
            
            // If we have a variations form, ensure the internal elements are properly ordered
            if ($variationsForm.length) {
                // Find the variations section and the add to cart button within the form
                var $variations = $variationsForm.find('.variations');
                var $singleVariationWrap = $variationsForm.find('.single_variation_wrap');
                
                // If we have both elements, ensure variations are above the add to cart button
                if ($variations.length && $singleVariationWrap.length) {
                    // Apply CSS to ensure correct order
                    $variations.css({
                        'order': '1',
                        'margin-bottom': '20px'
                    });
                    
                    $singleVariationWrap.css({
                        'order': '2',
                        'margin-top': '0'
                    });
                }
            }
            
            // Ensure gallery navigation is visible
            this.setupGalleryNavigation();
        },
        
        resetToDesktopLayout: function() {
            // Move the main image back to its original position
            var $mainImage = $('.luther-blue-product-main-image');
            var $productLayout = $('.luther-blue-product-layout');
            
            // Only move back if it was moved for mobile
            if ($mainImage.hasClass('mobile-moved')) {
                $mainImage.removeClass('mobile-moved');
                $mainImage.insertAfter($('.luther-blue-product-gallery-thumbs'));
            }
            
            // Move the gallery thumbs back to its original position
            var $galleryThumbs = $('.luther-blue-product-gallery-thumbs');
            
            // Only move back if it was moved for mobile
            if ($galleryThumbs.hasClass('mobile-moved')) {
                $galleryThumbs.removeClass('mobile-moved');
                $galleryThumbs.prependTo($productLayout);
                
                // Reset gallery width
                $galleryThumbs.css('width', '');
            }
        },
        
        setupGalleryNavigation: function() {
            // Only show navigation if we're on mobile and there are enough thumbnails
            if (this.shouldApplyMobileLayout()) {
                var $container = $('.gallery-thumbs-container');
                var $thumbs = $container.find('.gallery-thumb');
                
                // Show navigation only if there are enough thumbnails to scroll
                if ($thumbs.length > 3) {
                    $('.gallery-nav').css('display', 'flex');
                } else {
                    $('.gallery-nav').css('display', 'none');
                }
            }
        },
        
        scrollGalleryLeft: function() {
            var $container = $('.gallery-thumbs-container');
            var scrollAmount = $container.scrollLeft() - 150;
            $container.animate({ scrollLeft: scrollAmount }, 300);
        },
        
        scrollGalleryRight: function() {
            var $container = $('.gallery-thumbs-container');
            var scrollAmount = $container.scrollLeft() + 150;
            $container.animate({ scrollLeft: scrollAmount }, 300);
        }
    };
    
})(jQuery); 