(function($) {
	"use strict";
	$(document).ready(function(){
		$('.facebook-share-button a').on('click', function() {
			var shareUrl = $(this).attr('href');
			var productId = $(this).data('product-id');

			window.open(shareUrl, 'fbShareWindow', 'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');

			// Wait for a short period to give Facebook share a chance to process
			setTimeout(function() {
				$.ajax({
					type: 'POST',
					url: ajax_object.ajax_url,
					data: {
						action: 'award_points_for_share',
						product_id: productId
					},
					success: function(response) {
						alert('You have been awarded 10 points for sharing this product on Facebook!');
					}
				});
			}, 2000);

			return false;
		});
	});
})(jQuery);
