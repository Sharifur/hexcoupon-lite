(function($) {
	"use strict";
	$(document).ready(function(){
		$(".product_addition_notice span.dashicons-dismiss").on("click",function(){
			$(".product_addition_notice").hide();
		});

		$('#store_credit_filter').on('change', function() {
			// Get the selected option value
			var filterValue = $(this).val();

			// Get all table rows
			var rows = $('#data-table tbody tr');

			// Loop through each row and toggle its visibility based on the filter value
			rows.each(function() {
				var row = $(this);
				if (filterValue === 'all') {
					row.css('display', 'table-row');
				} else if (filterValue === 'in' && row.hasClass('in')) {
					row.css('display', 'table-row');
				} else if (filterValue === 'out' && row.hasClass('out')) {
					row.css('display', 'table-row');
				} else {
					row.css('display', 'none');
				}
			});
		});

		// copy referral link after clicking the copy button
		$('.copy-referral-link').on('click',function(){
			// Get the input field
			var referralLink = $('#referral-link');

			// Select the input field text
			referralLink.select();
			referralLink[0].setSelectionRange(0, 99999); // For mobile devices

			// Copy the text inside the input field
			navigator.clipboard.writeText(referralLink.val()).then(function() {
				// Alert the copied text
				alert('Referral link copied to clipboard!');
			}, function(err) {
				// If something goes wrong
				alert('Failed to copy the referral link: ' + err);
			});
		});
	});
})(jQuery);
