(function ($) {
	"use strict";
	$(document).ready(function () {
		if ($('.wp-block-woocommerce-cart-order-summary-subtotal-block').length) {
			// Add your custom text
			var additionalText = 'Additional text goes here.';

			// Create a new element with the custom text
			var newTextElement = $('<p>').text(additionalText).addClass('additional-text');

			// Append the new element after the Subtotal
			$('.wp-block-woocommerce-cart-order-summary-subtotal-block').append(newTextElement);
		}
	}); // <-- Closing parenthesis for the ready function
})(jQuery); // <-- Closing parenthesis for the IIFE
