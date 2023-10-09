(function($) {
	"use strict";
	$(document).ready(function(){
		var escapedText1 = escapedData.escapedPlaceholderText1;
		var escapedText2 = escapedData.escapedPlaceholderText2;
		var escapedText3 = escapedData.escapedPlaceholderText3;
		var elements = $(".hex__select2");

		if (elements.length >= 3) {
			var placeholderText1 = escapedText1;
			var placeholderText2 = escapedText2;
			var placeholderText3 = escapedText3;

			elements.eq(0).data("placeholder", placeholderText1);
			elements.eq(1).data("placeholder", placeholderText2);
			elements.eq(2).data("placeholder", placeholderText3);
		}

		elements.select2({
			placeholder: function() {
				return $(this).data("placeholder");
			}
		});
	});
})(jQuery);

