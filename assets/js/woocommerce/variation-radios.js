jQuery(document).ready(function($) {
    // Function to convert dropdowns to radio buttons
    function variationsToRadios() {
        $('.variations select').each(function(index, select) {
            var $select = $(select);
            var attributeName = $select.attr('data-attribute_name') || $select.attr('name');
            
            // Check if radio buttons already exist for this select
            if ($select.next('.variation-radios').length) {
                // Radio buttons already exist, just update them
                updateRadioButtons($select);
                return;
            }
            
            // Create new radio buttons container
            var $radioDiv = $('<div class="variation-radios"></div>');
            
            // Get selected value
            var selectedValue = $select.val() || '';
            
            // For each option in the select, create a radio button
            $select.find('option').each(function(index, option) {
                var $option = $(option);
                var value = $option.val();
                var text = $option.text();
                var checked = selectedValue === value;
                
                // Skip if it's the default "Choose an option" text
                if (value === '') return;
                
                var $radio = $('<input type="radio">')
                    .attr('name', attributeName)
                    .attr('id', attributeName + '_' + value)
                    .attr('value', value)
                    .prop('checked', checked);
                    
                var $radioLabel = $('<label>')
                    .attr('for', attributeName + '_' + value)
                    .text(text);
                    
                $radioDiv.append($radio).append($radioLabel);
            });
            
            // Insert radio buttons after the select
            $select.after($radioDiv);
            
            // Update select value when radio is changed
            $radioDiv.on('change', 'input[type="radio"]', function() {
                var $radio = $(this);
                $select.val($radio.val()).trigger('change');
            });
        });
    }
    
    // Function to update existing radio buttons
    function updateRadioButtons($select) {
        var selectedValue = $select.val() || '';
        var $radioDiv = $select.next('.variation-radios');
        
        // Update checked state of radio buttons
        $radioDiv.find('input[type="radio"]').each(function() {
            var $radio = $(this);
            $radio.prop('checked', $radio.val() === selectedValue);
        });
    }
    
    // Update all selects when variation values are updated
    $(document).on('woocommerce_update_variation_values', function() {
        $('.variations select').each(function() {
            updateRadioButtons($(this));
        });
    });

    // Run on page load
    variationsToRadios();
    
    // Handle reset variations
    $(document).on('click', '.reset_variations', function(e) {
        $('.variation-radios input[type="radio"]').prop('checked', false);
        // Reset the add to cart button text to default without price
        $('.single_add_to_cart_button').text('Add to your cart');
    });
    
    // Update product price and add to cart button when variation is selected
    $(document).on('found_variation', function(event, variation) {
        // Update the add to cart button text with the variation price
        if (variation.display_price) {
            var formattedPrice = formatPrice(variation.display_price);
            $('.single_add_to_cart_button').text('Add to your cart - ' + formattedPrice);
        }
    });
    
    // Store the original price on page load and set initial button text
    $(document).ready(function() {
        // Check if this is a variable product
        var isVariableProduct = $('.variations_form').length > 0;
        
        if (isVariableProduct) {
            // For variable products, start without price
            $('.single_add_to_cart_button').text('Add to your cart');
            
            // Only set price in button if a variation is already selected
            if ($('.variations input[type="radio"]:checked').length > 0) {
                var $priceElement = $('.woocommerce-Price-amount').first();
                if ($priceElement.length) {
                    var initialPrice = $priceElement.text();
                    if (initialPrice) {
                        $('.single_add_to_cart_button').text('Add to your cart - ' + initialPrice);
                    }
                }
            }
        } else {
            // For simple products, always show the price
            var $priceElement = $('.woocommerce-Price-amount').first();
            if ($priceElement.length) {
                var productPrice = $priceElement.text();
                if (productPrice) {
                    $('.single_add_to_cart_button').text('Add to your cart - ' + productPrice);
                } else {
                    $('.single_add_to_cart_button').text('Add to your cart');
                }
            }
        }
    });
    
    // Helper function to format price
    function formatPrice(price) {
        return accounting.formatMoney(price, {
            symbol: '$',
            decimal: '.',
            thousand: ',',
            precision: 2,
            format: '%s%v'
        });
    }
}); 