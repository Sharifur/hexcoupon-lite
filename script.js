(function($) {
	"use strict";
	$(document).ready(function(){
		// Display the popup when the page loads
window.onload = function() {
    document.getElementById("popup").style.display = "flex";
};

// Close the popup when the close button is clicked
document.getElementById("closePopup").onclick = function() {
    document.getElementById("popup").style.display = "none";
};

// Spin the wheel when the spin button is clicked
document.getElementById("spinButton").onclick = function() {
    var wheel = document.getElementById("wheel");
    var randomDegree = Math.floor(Math.random() * 360) + 1440; // 1440 ensures multiple full rotations
    wheel.style.transform = "rotate(" + randomDegree + "deg)";
};
	});
})(jQuery);
