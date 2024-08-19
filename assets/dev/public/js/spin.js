(function($) {
    "use strict";
    $(document).ready(function(){
        let count = document.querySelectorAll(".wheel .slice");
        let pointer_btn = document.querySelector(".try-your-luck");
        let circle = document.querySelector(".wheel");
        let spinCount = 0; // Initialize spin counter
        let maxSpins = spinToWinData.delayBetweenSpin; // Maximum number of allowed spins
        let delayTime = spinToWinData.delayTime * 1000; // converting it to secnonds format

        // Ensure that the button exists before adding an event listener
        if (pointer_btn) {
            let innerTextParent = document.querySelectorAll(".wheel .slice .value");
            let innerTexts = [];
            innerTextParent.forEach((e) => {
                let text = e.innerText;
                innerTexts.unshift(text);
            });

            pointer_btn.addEventListener("click", function(){
                $.ajax({
                    url: spinToWinData.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'update_spin_count' // Action to trigger the PHP function that updates user_meta
                    },
                    success: function(response) {
                        if (response.success) {
                            let newSpinCount = response.data;
                
                            console.log("Spin count successfully updated to: " + newSpinCount);
                        } else {
                            console.log("Failed to update spin count.");
                        }
                    },
                    error: function() {
                        console.log("An error occurred while updating the spin count.");
                    }
                });
                

                if (spinCount < maxSpins) { // Check if the spin count is less than the maximum allowed
                    // Disable the button to prevent immediate re-spinning
                    pointer_btn.disabled = true;
                    pointer_btn.style.cursor = "not-allowed";

                    let sliceNum = count.length;
                    let sliceDeg = 360 / sliceNum;
                    let rnum = Math.floor(Math.random() * (84 - 36 + 1)); // Generate a random number between 36 and 84
                    let deg = rnum * sliceDeg;
                    circle.style.rotate = deg + "deg";

                    let offernum = (((deg) % 360) / sliceDeg) - 1;

                    setTimeout(function() {
                        alert("Hurry you get " + innerTexts[offernum]);

                        spinCount++; // Increment the spin counter

                        if (spinCount < maxSpins) {
                            // Re-enable the button after the dynamic delay time
                            setTimeout(function() {
                                pointer_btn.disabled = false;
                                pointer_btn.style.cursor = "pointer";
                            }, delayTime); // Use the dynamic delay time here
                        } else {
                            alert("You have reached the maximum number of spins!");
                            pointer_btn.style.cursor = "not-allowed";
                        }
                    }, 4000); // Delay for the spin animation (adjust as necessary)
                } else {
                    alert("You have already used all your spins!");
                }
            });
        } else {
            console.error("The 'TRY YOUR LUCK' button was not found.");
        }
    });
})(jQuery);
