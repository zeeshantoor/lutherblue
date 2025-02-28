/**
 * Luther Blue Theme - Single Product Gallery
 * 
 * Handles the product gallery functionality for the single product page.
 */

(function($) {
    'use strict';
    
    // Initialize the gallery when the DOM is ready
    $(document).ready(function() {
        initProductGallery();
        initIngredientsToggle();
    });
    
    /**
     * Initialize the ingredients toggle functionality
     */
    function initIngredientsToggle() {
        const $toggle = $('.ingredients-toggle');
        const $ingredientsList = $('#ingredients-list');
        const $preview = $('.ingredients-preview');
        
        if ($toggle.length === 0 || $ingredientsList.length === 0) {
            return;
        }
        
        $toggle.on('click', function() {
            const $this = $(this);
            const isExpanded = $this.attr('aria-expanded') === 'true';
            
            // Toggle aria-expanded attribute
            $this.attr('aria-expanded', !isExpanded);
            
            // Toggle visibility of ingredients list and preview
            if (isExpanded) {
                $ingredientsList.slideUp(300);
                $preview.slideDown(300);
                $this.find('.toggle-icon').text('+');
            } else {
                $ingredientsList.slideDown(300);
                $preview.slideUp(300);
                $this.find('.toggle-icon').text('-');
            }
        });
    }
    
    /**
     * Initialize the product gallery
     */
    function initProductGallery() {
        const $galleryThumbs = $('.gallery-thumb');
        const $mainImage = $('#main-product-image');
        const $thumbsContainer = $('.gallery-thumbs-container');
        const $navUp = $('.gallery-nav-up');
        const $navDown = $('.gallery-nav-down');
        
        // If no gallery elements found, exit early
        if ($galleryThumbs.length === 0 || $mainImage.length === 0) {
            console.error('Product gallery elements not found');
            return;
        }
        
        // Function to update main image with full-size image
        function updateMainImage($thumb) {
            // Get the full-size image URL from the thumbnail
            const $img = $thumb.find('img');
            const fullSizeUrl = $img.data('full-src');
            
            if (!fullSizeUrl) {
                console.error('Full size image URL not found');
                return;
            }
            
            // Create a new image to preload
            const newImg = new Image();
            
            // Show loading state
            $mainImage.css('opacity', '0.5');
            
            // When the new image is loaded
            newImg.onload = function() {
                // Update the main image source
                $mainImage.attr('src', fullSizeUrl);
                $mainImage.css('opacity', '1');
            };
            
            // Handle loading errors
            newImg.onerror = function() {
                console.error('Error loading image:', fullSizeUrl);
                $mainImage.css('opacity', '1');
            };
            
            // Start loading the new image
            newImg.src = fullSizeUrl;
        }
        
        // Handle thumbnail click
        $galleryThumbs.on('click', function() {
            // Remove active class from all thumbnails
            $galleryThumbs.removeClass('active');
            
            // Add active class to clicked thumbnail
            $(this).addClass('active');
            
            // Update main image
            updateMainImage($(this));
            
            // Scroll the thumbnail into view (for mobile)
            scrollThumbIntoView($(this));
        });
        
        // Function to scroll thumbnail into view
        function scrollThumbIntoView($thumb) {
            const isMobile = window.innerWidth <= 991;
            
            if (isMobile) {
                // For mobile (horizontal scrolling)
                const containerLeft = $thumbsContainer.offset().left;
                const thumbLeft = $thumb.offset().left;
                const containerWidth = $thumbsContainer.width();
                
                // Calculate the scroll position to center the thumbnail
                const scrollLeft = thumbLeft - containerLeft - (containerWidth / 2) + ($thumb.width() / 2);
                
                // Smooth scroll to the position
                $thumbsContainer.animate({
                    scrollLeft: $thumbsContainer.scrollLeft() + scrollLeft
                }, 300);
            } else {
                // For desktop (vertical scrolling)
                const containerTop = $thumbsContainer.offset().top;
                const thumbTop = $thumb.offset().top;
                const containerHeight = $thumbsContainer.height();
                
                // Calculate the scroll position to center the thumbnail
                const scrollTop = thumbTop - containerTop - (containerHeight / 2) + ($thumb.height() / 2);
                
                // Smooth scroll to the position
                $thumbsContainer.animate({
                    scrollTop: $thumbsContainer.scrollTop() + scrollTop
                }, 300);
            }
        }
        
        // Initialize with the first thumbnail (if it exists)
        if ($galleryThumbs.length > 0) {
            // Find the active thumbnail or use the first one
            const $activeThumb = $galleryThumbs.filter('.active').length > 0 
                ? $galleryThumbs.filter('.active') 
                : $galleryThumbs.first();
            
            // Make sure it's marked as active
            $activeThumb.addClass('active');
            
            // Update main image with the active thumbnail
            updateMainImage($activeThumb);
        }
        
        // Handle navigation arrows
        function updateNavArrows() {
            const isMobile = window.innerWidth <= 991;
            
            if (isMobile) {
                // Mobile: horizontal scrolling
                const canScrollLeft = $thumbsContainer.scrollLeft() > 0;
                const canScrollRight = $thumbsContainer.scrollLeft() + $thumbsContainer.width() < $thumbsContainer[0].scrollWidth;
                
                $navUp.toggle(canScrollLeft);
                $navDown.toggle(canScrollRight);
            } else {
                // Desktop: vertical scrolling
                const canScrollUp = $thumbsContainer.scrollTop() > 0;
                const canScrollDown = $thumbsContainer.scrollTop() + $thumbsContainer.height() < $thumbsContainer[0].scrollHeight;
                
                $navUp.toggle(canScrollUp);
                $navDown.toggle(canScrollDown);
            }
        }
        
        // Initial check for navigation arrows
        updateNavArrows();
        
        // Update arrows on scroll
        $thumbsContainer.on('scroll', updateNavArrows);
        
        // Update arrows on window resize
        $(window).on('resize', updateNavArrows);
        
        // Handle up/left arrow click
        $navUp.on('click', function() {
            const isMobile = window.innerWidth <= 991;
            
            if (isMobile) {
                // Scroll left on mobile
                $thumbsContainer.animate({
                    scrollLeft: $thumbsContainer.scrollLeft() - 200
                }, 300);
            } else {
                // Scroll up on desktop
                $thumbsContainer.animate({
                    scrollTop: $thumbsContainer.scrollTop() - 200
                }, 300);
            }
        });
        
        // Handle down/right arrow click
        $navDown.on('click', function() {
            const isMobile = window.innerWidth <= 991;
            
            if (isMobile) {
                // Scroll right on mobile
                $thumbsContainer.animate({
                    scrollLeft: $thumbsContainer.scrollLeft() + 200
                }, 300);
            } else {
                // Scroll down on desktop
                $thumbsContainer.animate({
                    scrollTop: $thumbsContainer.scrollTop() + 200
                }, 300);
            }
        });
        
        // Add touch swipe support for mobile
        let touchStartX = 0;
        let touchEndX = 0;
        
        $thumbsContainer.on('touchstart', function(e) {
            touchStartX = e.originalEvent.touches[0].clientX;
        });
        
        $thumbsContainer.on('touchend', function(e) {
            touchEndX = e.originalEvent.changedTouches[0].clientX;
            handleSwipe();
        });
        
        function handleSwipe() {
            const swipeDistance = touchEndX - touchStartX;
            const threshold = 50; // Minimum distance to register as a swipe
            
            if (Math.abs(swipeDistance) < threshold) {
                return; // Not a significant swipe
            }
            
            if (swipeDistance > 0) {
                // Swipe right - scroll left
                $thumbsContainer.animate({
                    scrollLeft: $thumbsContainer.scrollLeft() - 200
                }, 300);
            } else {
                // Swipe left - scroll right
                $thumbsContainer.animate({
                    scrollLeft: $thumbsContainer.scrollLeft() + 200
                }, 300);
            }
        }
        
        // Add keyboard navigation for accessibility
        $(document).on('keydown', function(e) {
            // Only handle keys if gallery is in viewport
            if (!isElementInViewport($thumbsContainer[0])) {
                return;
            }
            
            const $activeThumb = $galleryThumbs.filter('.active');
            let $targetThumb;
            
            switch (e.key) {
                case 'ArrowLeft':
                    $targetThumb = $activeThumb.prev('.gallery-thumb');
                    if ($targetThumb.length) {
                        $targetThumb.click();
                    }
                    break;
                case 'ArrowRight':
                    $targetThumb = $activeThumb.next('.gallery-thumb');
                    if ($targetThumb.length) {
                        $targetThumb.click();
                    }
                    break;
                case 'ArrowUp':
                    if (!isMobile()) {
                        $targetThumb = $activeThumb.prev('.gallery-thumb');
                        if ($targetThumb.length) {
                            $targetThumb.click();
                        }
                    }
                    break;
                case 'ArrowDown':
                    if (!isMobile()) {
                        $targetThumb = $activeThumb.next('.gallery-thumb');
                        if ($targetThumb.length) {
                            $targetThumb.click();
                        }
                    }
                    break;
            }
        });
        
        // Helper function to check if element is in viewport
        function isElementInViewport(el) {
            const rect = el.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }
        
        // Helper function to check if mobile view
        function isMobile() {
            return window.innerWidth <= 991;
        }
    }
    
})(jQuery); 