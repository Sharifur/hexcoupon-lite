(function($) {
	"use strict";
	$(document).ready(function(){

		/*
       ========================================
           General Tab
       ========================================
       */
		$(".hex__select2").select2({
			placeholder: function() {
				return $(this).data("placeholder");
			}
		});

		// Remove discounts type default select input field
		$(".discount_type_field").remove();

		// Place coupon type select input field before the coupon amount input field
		$(".coupon_type_field").insertBefore(".coupon_amount_field");

		// Place customer purchases radio buttons before the coupon type select input
		$(".customer_purchases").insertAfter(".coupon_type_field");

		// Place add a specific product to purchase select2 field after the customer purchases radio buttons
		$(".add_a_specific_product_to_purchase").insertAfter(".customer_purchases_field");
		$("#add_a_specific_product_to_purchase").insertAfter(".add_a_specific_product_to_purchase label");


		// Place the tooltip of add a specific product to purchase select2 field
		$(".add_a_specific_product_to_purchase_tooltip").insertAfter(".add_a_specific_product_to_purchase span.select2-container");

		// Place the add categories select2 input fields after the customer purchases field
		$(".add_categories_to_purchase").insertAfter(".customer_purchases_field");
		$(".add_categories_to_purchase_tooltip").insertAfter(".add_categories_to_purchase span.select2-container");

		//
		$(".customer_gets_as_free").insertAfter(".customer_purchases");

		$(".add_a_specific_product_for_free").insertAfter(".customer_gets_as_free_field");
		$("#add_a_specific_product_for_free").insertAfter(".add_a_specific_product_for_free label");

		$(".add_a_specific_product_for_free_tooltip").insertAfter(".add_a_specific_product_for_free span.select2-container");

		// Place the add categories select2 input fields after the customer gets as free field
		$(".add_categories_as_free").insertAfter(".customer_gets_as_free_field");
		$(".add_categories_as_free_tooltip").insertAfter(".add_categories_as_free span.select2-container");

		// Place the bogo use limit radibo buttons after the add specific product as free input select2 field
		$(".bogo_use_limit").insertAfter("div.add_a_specific_product_for_free");

		// Place the bogo deal checkboxes after the customer gets as free div
		$(".bogo_deal_checkboxes").insertAfter(".customer_gets_as_free");


		// Remove all other fields if BOGO is selected
		const discountTypeField = $("select[name^='discount_type']");

		// if ("buy_x_get_x_bogo" === $(discount_type_field).val()) {
		// 	$(".customer_purchases").show()
		// 	$(".customer_gets_as_free").show()
		// 	$(".bogo_deal_checkboxes").show()
		//
		// 	$(".coupon_amount_field").hide();
		// 	$(".free_shipping_field").hide();
		// 	$(".expiry_date_field").hide();
		// 	$(".message_for_coupon_expiry_date_field").hide();
		// 	$(".coupon_starting_date_field").hide();
		// 	$(".message_for_coupon_starting_date_field").hide();
		// 	$(".apply_days_hours_of_week_field").hide();
		// }

		discountTypeField.on("change",function (){
			if("buy_x_get_x_bogo" === this.value){
				$(".customer_purchases").show()
				$(".customer_gets_as_free").show()
				$(".bogo_deal_checkboxes").show()

				$(".coupon_amount_field").hide();
				$(".free_shipping_field").hide();
				$(".expiry_date_field").hide();
				$(".message_for_coupon_expiry_date_field").hide();
				$(".coupon_starting_date_field").hide();
				$(".message_for_coupon_starting_date_field").hide();
				$(".apply_days_hours_of_week_field").hide();
			}
			else {
				$(".customer_purchases").hide()
				$(".customer_gets_as_free").hide()
				$(".bogo_deal_checkboxes").hide()

				$(".coupon_amount_field").show();
				$(".free_shipping_field").show();
				$(".expiry_date_field").show();
				$(".message_for_coupon_expiry_date_field").show();
				$(".coupon_starting_date_field").show();
				$(".message_for_coupon_starting_date_field").show();
				$(".apply_days_hours_of_week_field").show();
			}
		});

		// perform on page load
		discountTypeField.trigger("change");

		// Don't allow more than one product selection on selecting a specific product from the customer purchases field
		const customerPurchases = $("input[name='customer_purchases']");

		// Show or hide product selection and category selection input fields if product categories type is selected
		customerPurchases.on("change",function(){
			if("product_categories" === $(this).val()){
				$(".add_categories_to_purchase").show();
				$(".add_a_specific_product_to_purchase").hide();
			}else {
				$(".add_categories_to_purchase").hide();
				$(".add_a_specific_product_to_purchase").show();
			}
		});

		// Control number of result selection if a specific product type is selected
		customerPurchases.on("change",function(){
			if("a_specific_product" === $(this).val()){
				$("#add_a_specific_product_to_purchase").select2({
					maximumSelectionLength: 1 // Set maximum selection to 1
				});
			}else {
				$("#add_a_specific_product_to_purchase").select2({
					maximumSelectionLength: 0 // Set maximum selection to unlimited
				});
			}
		});

		// Trigger the change on page load
		customerPurchases.trigger("change");

		// Don't allow more than one product selection on selecting a specific product from the customer gets as free field
		const customerGetsAsFree = $("input[name='customer_gets_as_free']");

		// Show or hide product selection and category selection input fields if product categories type is selected
		customerGetsAsFree.on("change",function(){
			if("product_categories" === $(this).val()){
				$(".add_categories_as_free").show();
				$(".add_a_specific_product_for_free").hide();
			}else {
				$(".add_categories_as_free").hide();
				$(".add_a_specific_product_for_free").show();
			}
		});

		// Control number of result selection if a specific product type is selected
		customerGetsAsFree.on("change",function(){
			if("a_specific_product" === $(this).val()){
				$("#add_a_specific_product_for_free").select2({
					maximumSelectionLength: 1 // Set maximum selection to 1
				});
			}else {
				$("#add_a_specific_product_for_free").select2({
					maximumSelectionLength: 0 // Set maximum selection to unlimited
				});
			}
		});

		customerGetsAsFree.on("change",function(){
			if("same_product_added_to_cart" === $(this).val()){
				$(".add_a_specific_product_for_free").hide();
			}
		});

		// Trigger the change on page load
		customerGetsAsFree.trigger("change");


		/*
       ========================================
           Usage Limits
       ========================================
       */

		// On page load
		var resetOptionValue = $("#reset_option_value").val(); // Get the value of 'reset_option_value' input

		// Find all p elements within the specified div
		var paragraphs = $(".reset_limit").find("p");

		// Loop through each p element and compare data-reset-value attribute
		paragraphs.each(function() {
			var dataResetValue = $(this).attr("data-reset-value");
			if (dataResetValue === resetOptionValue) {
				$(this).addClass("usage_limit_p_background");
			}
		});

		// On clicking the p element
		$(".reset_limit p").click(function (){
			$(".reset_limit p").removeClass("usage_limit_p_background");

			$(this).addClass("usage_limit_p_background");

			// Get the reset value from the data attribute
			const resetValue = $(this).data('reset-value');
			// Update the hidden input field value
			$('#reset_option_value').val(resetValue);
		});

		var resetUsageLimit = $("#reset_usage_limit");

		// On clicking the checkbox
		$(resetUsageLimit).on("change",function (){
			if($(this).is(":checked")){
				$(".reset_limit").show();
			}
			else {
				$(".reset_limit").hide();
			}
		});

		// On page load
		if($(resetUsageLimit).is(":checked")){
			$(".reset_limit").show();
		}
		else {
			$(".reset_limit").hide();
		}


		/*
       ========================================
           Usage Restriction
       ========================================
       */
		const cartConditionCB = $(".cart-condition").prop("outerHTML");
		$(".cart-condition").remove();
		$("select[name^='product_ids']").parent().after(cartConditionCB);

		$("select[name^='product_ids']").parent().remove();


		const cartConditionRadio = $(".apply_on_listed_product").prop("outerHTML");
		$(".apply_on_listed_product").remove();
		$("select[name^='exclude_product_ids']").parent().before(cartConditionRadio);

		$(".all_selected_products").insertAfter(".apply_on_listed_product");

		$(".all_selected_products_tooltip").insertAfter(".all_selected_products span.select2-container");




		const cartConditionCategory = $(".category-cart-condition").prop("outerHTML");
		$(".category-cart-condition").remove();
		$("select[name^='exclude_product_categories']").parent().before(cartConditionCategory);

		$("select[name^='product_categories']").parent().remove();

		$(".all_selected_categories").insertAfter(".category-cart-condition");

		$(".all_selected_categories_tooltip").insertAfter(".all_selected_categories span.select2-container");
		$(".selected_customer_group_tooltip").insertAfter(".selected_customer_group");
		$(".selected_individual_customer_tooltip").insertAfter(".selected_individual_customer");




		// function appendSelectedValue(selectedValue) {
		// 	$("#selectedValuesContainer").append("<span class='select2-selection__choice'>" + selectedValue + "</span><div class='product_min_max'><input placeholder='No minimum' type='number' style='float:left; width:50% !important;'><input style='width:50% !important;' placeholder='No maximum' type='number'><span>X</span></div>");
		// }

		// // Get the selected values from select2 input on page load
		// var initialSelectedValues = $(".all_selected_products").val();
		//
		// // Append each initial selected value to the container
		// if (initialSelectedValues) {
		// 	initialSelectedValues.forEach(function(value) {
		// 		appendSelectedValue(value);
		// 	});
		// }

		// Event handler for the "select2:select" event
		// $(".all_selected_products").on("select2:select", function(event) {
		// 	var selectedValue = $(".all_selected_products li.select2-selection__choice").text();
		// 	if (selectedValue) {
		// 		// Add the selected value to the container
		// 		$("#selectedValuesContainer").append("<span class='select2-selection__rendered'><span class='select2-selection__choice' name='all_selected_products[]'>" + selectedValue + "</span></span><div class='product_min_max'><input placeholder='No minimum' type='number' style='float:left; width:50% !important;'><input style='width:50% !important;' placeholder='No maximum' type='number'><span>X</span></div>");
		//
		// 		// Clear the selection from the input field
		// 		$(this).val(null).trigger("change");
		// 	}
		// });

		// working code
		$(".all_selected_products").on("select2:select", function(event) {
			var selectedValues = $(this).val(); // Get an array of selected values

			if (selectedValues) {
				// Clear the container before adding new selections
				$("#selectedValuesContainer").empty();

				// Loop through selected values and add them to the container
				selectedValues.forEach(function(value) {
					var text = $(".all_selected_products option[value='" + value + "']").text();

					$("#selectedValuesContainer").append("<div class='whole'><span class='select2-selection__choice'>" + text + "</span><div class='product_min_max'><input name='product_min_quantity[]' placeholder='No minimum' type='number' style='float:left; width:50% !important;'><input name='product_max_quantity[]' style='width:50% !important;' placeholder='No maximum' type='number'><a class='remove_product'>X</a></div></div>");
				});

				// Update the original input field value with the selected values
				$(this).val(selectedValues).trigger("change");
			}
		});

		// Add click event handler for the .remove_product class
		$(document).on("click", ".remove_product", function() {
			// Remove the parent .whole element when .remove_product is clicked
			$(this).closest(".whole").remove();
		});


		$(".all_selected_products ul.select2-selection__rendered").remove();


		// function displaySelectedValues() {
		// 	var selectedValues = $(".all_selected_products").val(); // Get an array of selected values
		//
		// 	if (selectedValues) {
		// 		// Clear the container before adding new selections
		// 		$("#selectedValuesContainer").empty();
		//
		// 		// Loop through selected values and add them to the container
		// 		selectedValues.forEach(function(value) {
		// 			var selectedText = $(".all_selected_products option[value='" + value + "']").text();
		// 			$("#selectedValuesContainer").append("<span class='select2-selection__rendered'><span class='select2-selection__choice' name='all_selected_products[]'>" + selectedText + "</span></span><div class='product_min_max'><input placeholder='No minimum' type='number' style='float:left; width:50% !important;'><input style='width:50% !important;' placeholder='No maximum' type='number'><span>X</span></div>");
		// 		});
		// 	}
		// }

		// Call the displaySelectedValues function on page load
		// displaySelectedValues();
		//
		// // Event handler to display selected values on select2:select
		// $(".all_selected_products").on("select2:select", function(event) {
		// 	displaySelectedValues();
		//
		// 	// Clear the selection from the input field
		// 	$(this).val(null).trigger("change");
		// });


		/*
       ========================================
           Geographic Restriction
       ========================================
       */
		var applyGeographicRestriction = $("input[name='apply_geographic_restriction']");

		applyGeographicRestriction.on("change", function() {
			if ($(this).is(":checked") && $(this).val() === 'restrict_by_shipping_zones') {
				$(".restricted_shipping_zones").show();
				$(".restricted_countries").hide();
			} else if ($(this).is(":checked") && $(this).val() === 'restrict_by_countries') {
				$(".restricted_shipping_zones").hide();
				$(".restricted_countries").show();
			}
		});

		// Trigger the change event initially to apply the appropriate visibility
		applyGeographicRestriction.trigger("change");


		$(".restricted_shipping_zones_tooltip").insertAfter(".restricted_shipping_zones span.select2-container");

		$(".restricted_countries_tooltip").insertAfter(".restricted_countries span.select2-container");

		/*
       ========================================
           Payment & Shipping method
       ========================================
       */
		// Place help tooltip after the select payment method input field
		$(".permitted_payment_methods_tooltip").insertAfter(".payment_and_shipping_method .options_group:first-child span.select2-container");

		// Place help tooltip after the select shipping method input field
		$(".permitted_shipping_methods_tooltip").insertAfter(".payment_and_shipping_method .options_group:last-child span.select2-container");

		/*
       ========================================
           Sharable URL Coupon
       ========================================
       */
		// Show or hide redirect link input field upon changing the radio button
		var applyRedirectSharableLink = $("input[name='apply_redirect_sharable_link']");

		applyRedirectSharableLink.on("change", function() {
			if ($(this).is(":checked") && $(this).val() === 'redirect_back_to_origin') {
				$(".redirect_link_field").hide();
			} else if ($(this).is(":checked") && $(this).val() === 'redirect_to_custom_link') {
				$(".redirect_link_field").show();
			}
		});

		// Trigger the change event initially to apply the appropriate visibility
		applyRedirectSharableLink.trigger("change");

		// Add readonly property to the sharable_url input field
		$('#sharable_url').prop('readonly', true);

		// Copy url on clicking the copy url button
		$(".copy-sharable-url").click(function() {
			var text = $(".output-url-text").text(); // Get the text from the <p> element
			var tempInput = $('<input>'); // Create a temporary input element
			$("body").append(tempInput); // Append it to the body

			tempInput.val(text).select(); // Set its value and select the text
			document.execCommand("copy"); // Copy the selected text

			tempInput.remove(); // Remove the temporary input element
			alert("URL copied to clipboard: " + text); // Show an alert
		});



		// Select hexcoupon shortcode field
		$(".shortcode_column").click(function(){
			this.select();
		});

		// Show hide days and hours on page load
		if($("#apply_days_hours_of_week").is(":checked")) {
			$(".day_time_hours_block").show();
		} else {
			$(".day_time_hours_block").hide();
			$("#total_hours_count_saturday").val('0');
			$("#total_hours_count_sunday").val('0');
			$("#total_hours_count_monday").val('0');
			$("#total_hours_count_tuesday").val('0');
			$("#total_hours_count_wednesday").val('0');
			$("#total_hours_count_thursday").val('0');
			$("#total_hours_count_friday").val('0');
		}

		// Show hide products cart condition on page load
		if ($("#apply_cart_condition_for_customer_on_products").is(":checked")) {
			$(".apply_on_listed_product").show();
		} else {
			$(".apply_on_listed_product").hide();
		}

		// Show hide categories cart condition on page load
		if ($("#apply_cart_condition_for_customer_on_categories").is(":checked")) {
			$(".all_selected_categories").show();
		} else {
			$(".all_selected_categories").hide();
		}

		// Show hide group of customer fields on page load
		if ($("#allowed_or_restricted_customer_group").is(":checked")) {
			$(".allowed_group_of_customer").show();
		} else {
			$(".allowed_group_of_customer").hide();
		}

		// Show hide individual of customer fields on page load
		if ($("#allowed_or_restricted_individual_customer").is(":checked")) {
			$(".allowed_individual_customer").show();
		} else {
			$(".allowed_individual_customer").hide();
		}

		// Show hide days and hours on the basis of clicking the checkbox
		$("#apply_days_hours_of_week").on("change", function () {
			if ($(this).is(":checked")) {
				$(".day_time_hours_block").show();
			} else {
				$(".day_time_hours_block").hide();
			}
		});

		// Show hide fields in cart product condition on the basis of clicking the checkbox
		$("#apply_cart_condition_for_customer_on_products").on("change", function () {
			if ($(this).is(":checked")) {
				$(".apply_on_listed_product").show();
			} else {
				$(".apply_on_listed_product").hide();
			}
		});

		// Show hide fields in cart category condition on the basis of clicking the checkbox
		$("#apply_cart_condition_for_customer_on_categories").on("change", function () {
			if ($(this).is(":checked")) {
				$(".all_selected_categories").show();
			} else {
				$(".all_selected_categories").hide();
			}
		});

		// Show hide all fields of allowed group of customer on the basis of clicking the checkbox
		$("#allowed_or_restricted_customer_group").on("change", function () {
			if ($(this).is(":checked")) {
				$(".allowed_group_of_customer").show();
			} else {
				$(".allowed_group_of_customer").hide();
			}
		});

		// Show hide all fields of allowed individual of customer on the basis of clicking the checkbox
		$("#allowed_or_restricted_individual_customer").on("change", function () {
			if ($(this).is(":checked")) {
				$(".allowed_individual_customer").show();
			} else {
				$(".allowed_individual_customer").hide();
			}
		});


		/*
	   ========================================
		   Days and Hours fields for Saturday
	   ========================================
	   */

		// Function for enabling flatpickr
		function flatPicker(day) {
			$(".time-picker-"+day).flatpickr({
				enableTime: true,
				noCalendar: true,
				dateFormat: "H:i",
			});
		}

		// Function to show hide different fields
		function showHideDayFields(dayFullName,dayShortName) {
			if($("#coupon_apply_on_"+dayFullName).is(":checked")) {
				$(".time-picker-"+dayFullName).show();
				$("#"+dayShortName+"_add_more_hours").show();
				$(".cross_hour_"+dayFullName).show();
				$(".input_separator_"+dayFullName).show();
				$("#"+dayShortName+"_deactivated_text").hide();
			} else {
				$(".time-picker-"+dayFullName).hide();
				$("#"+dayShortName+"_add_more_hours").hide();
				$(".cross_hour_"+dayFullName).hide();
				$(".input_separator_"+dayFullName).hide();
				$("#"+dayShortName+"_deactivated_text").show();
				$("#total_hours_count_"+dayFullName).val('0');
			}
		}

		// Enable flatpicker on saturday default input fields
		flatPicker('saturday');

		// Show hide saturday fields
		showHideDayFields('saturday','sat');

		// Show hide saturday hours on the basis of clicking the saturday checkbox
		$("#coupon_apply_on_saturday").on("change",function (){
			showHideDayFields('saturday','sat');
		});

		// Add input field dynamically for saturday.
		let totalHoursCountSaturday = $("#total_hours_count_saturday").val();
		$(document).on("click","#sat_add_more_hours",function(){
			totalHoursCountSaturday++;

			let appendedElementSaturday = "<span class='appededItem first-input'><input type='text' class='time-picker-saturday' name='sat_coupon_start_time_"+totalHoursCountSaturday+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_saturday'>-</span><input type='text' class='time-picker-saturday' name='sat_coupon_expiry_time_"+totalHoursCountSaturday+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_saturday cross-hour'>X</a></span>";


			$(".saturday").append(appendedElementSaturday);
			$("#total_hours_count_saturday").val(totalHoursCountSaturday);
			flatPicker('saturday');
		});

		// Remove each input item of saturday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_saturday", function (){
			let totalHoursCountSaturday = $("#total_hours_count_saturday").val();
			$(this).closest("span").remove();
			totalHoursCountSaturday -= 1;
			$("#total_hours_count_saturday").val(totalHoursCountSaturday);
		})

		/*
	   ========================================
		   Days and Hours fields for Sunday
	   ========================================
	   */

		// Enable flatpicker on sunday default input fields
		flatPicker('sunday');

		// Show hide sunday fields
		showHideDayFields('sunday','sun');

		// Show hide sunday hours on the basis of clicking the sunday checkbox
		$("#coupon_apply_on_sunday").on("change",function (){
			showHideDayFields('sunday','sun');
		});

		// Add input field dynamically for sunday.
		let totalHoursCountSunday = $("#total_hours_count_sunday").val();
		$(document).on("click","#sun_add_more_hours",function(){
			totalHoursCountSunday++;

			let appendedElementSunday = "<span class='appededItem first-input'><input type='text' class='time-picker-sunday' name='sun_coupon_start_time_"+totalHoursCountSunday+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_sunday'>-</span><input type='text' class='time-picker-sunday' name='sun_coupon_expiry_time_"+totalHoursCountSunday+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_sunday cross-hour'>X</a></span>";


			$(".sunday").append(appendedElementSunday);
			$("#total_hours_count_sunday").val(totalHoursCountSunday);
			flatPicker('sunday');
		});

		// Remove each input item of sunday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_sunday", function (){
			let totalHoursCountSunday = $("#total_hours_count_sunday").val();
			$(this).closest("span").remove();
			totalHoursCountSunday -= 1;
			$("#total_hours_count_sunday").val(totalHoursCountSunday);
		})

		/*
	   ========================================
		   Days and Hours fields for Monday
	   ========================================
	   */

		// Enable flatpicker on monday default input fields
		flatPicker('monday');

		// Show hide monday fields
		showHideDayFields('monday','mon');

		// Show hide monday hours on the basis of clicking the monday checkbox
		$("#coupon_apply_on_monday").on("change",function (){
			showHideDayFields('monday','mon');
		});

		// Add input field dynamically for monday.
		let totalHoursCountMonday = $("#total_hours_count_monday").val();
		$(document).on("click","#mon_add_more_hours",function(){
			totalHoursCountMonday++;

			let appendedElementMonday = "<span class='appededItem first-input'><input type='text' class='time-picker-monday' name='mon_coupon_start_time_"+totalHoursCountMonday+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_monday'>-</span><input type='text' class='time-picker-monday' name='mon_coupon_expiry_time_"+totalHoursCountMonday+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_monday cross-hour'>X</a></span>";


			$(".monday").append(appendedElementMonday);
			$("#total_hours_count_monday").val(totalHoursCountMonday);
			flatPicker('monday');
		});

		// Remove each input item of monday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_monday", function (){
			let totalHoursCountMonday = $("#total_hours_count_monday").val();
			$(this).closest("span").remove();
			totalHoursCountMonday -= 1;
			$("#total_hours_count_monday").val(totalHoursCountMonday);
		})

		/*
	   ========================================
		   Days and Hours fields for Tuesday
	   ========================================
	   */

		// Enable flatpicker on tuesday default input fields
		flatPicker('tuesday');

		// Show hide tuesday fields
		showHideDayFields('tuesday','tue');

		// Show hide tuesday hours on the basis of clicking the tuesday checkbox
		$("#coupon_apply_on_tuesday").on("change",function (){
			showHideDayFields('tuesday','tue');
		});

		// Add input field dynamically for tuesday.
		let totalHoursCountTuesday = $("#total_hours_count_tuesday").val();
		$(document).on("click","#tue_add_more_hours",function(){
			totalHoursCountTuesday++;

			let appendedElementTuesday = "<span class='appededItem first-input'><input type='text' class='time-picker-tuesday' name='tue_coupon_start_time_"+totalHoursCountTuesday+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_tuesday'>-</span><input type='text' class='time-picker-tuesday' name='tue_coupon_expiry_time_"+totalHoursCountTuesday+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_tuesday cross-hour'>X</a></span>";


			$(".tuesday").append(appendedElementTuesday);
			$("#total_hours_count_tuesday").val(totalHoursCountTuesday);
			flatPicker('tuesday');
		});

		// Remove each input item of tuesday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_tuesday", function (){
			let totalHoursCountTusday = $("#total_hours_count_tuesday").val();
			$(this).closest("span").remove();
			totalHoursCountTusday -= 1;
			$("#total_hours_count_tuesday").val(totalHoursCountTusday);
		})

		/*
	   ========================================
		   Days and Hours fields for Wednesday
	   ========================================
	   */

		// Enable flatpicker on wednesday default input fields
		flatPicker('wednesday');

		// Show hide wednesday fields
		showHideDayFields('wednesday','wed');

		// Show hide tuesday hours on the basis of clicking the tuesday checkbox
		$("#coupon_apply_on_wednesday").on("change",function (){
			showHideDayFields('wednesday','wed');
		});

		// Add input field dynamically for tuesday.
		let totalHoursCountWednesday = $("#total_hours_count_wednesday").val();
		$(document).on("click","#wed_add_more_hours",function(){
			totalHoursCountWednesday++;

			let appendedElementWednesday = "<span class='appededItem first-input'><input type='text' class='time-picker-wednesday' name='tue_coupon_start_time_"+totalHoursCountWednesday+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_wednesday'>-</span><input type='text' class='time-picker-wednesday' name='wed_coupon_expiry_time_"+totalHoursCountWednesday+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_wednesday cross-hour'>X</a></span>";


			$(".wednesday").append(appendedElementWednesday);
			$("#total_hours_count_wednesday").val(totalHoursCountWednesday);
			flatPicker('wednesday');
		});

		// Remove each input item of tuesday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_wednesday", function (){
			let totalHoursCountWednesday = $("#total_hours_count_wednesday").val();
			$(this).closest("span").remove();
			totalHoursCountWednesday -= 1;
			$("#total_hours_count_wednesday").val(totalHoursCountWednesday);
		})

		/*
	   ========================================
		   Days and Hours fields for Thursday
	   ========================================
	   */

		// Enable flatpicker on thursday default input fields
		flatPicker('thursday');

		// Show hide thursday fields
		showHideDayFields('thursday','thu');

		// Show hide thursday hours on the basis of clicking the thursday checkbox
		$("#coupon_apply_on_thursday").on("change",function (){
			showHideDayFields('thursday','thu');
		});

		// Add input field dynamically for thursday.
		let totalHoursCountThursday = $("#total_hours_count_thursday").val();
		$(document).on("click","#thu_add_more_hours",function(){
			totalHoursCountThursday++;

			let appendedElementThursday = "<span class='appededItem first-input'><input type='text' class='time-picker-thursday' name='tue_coupon_start_time_"+totalHoursCountThursday+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_thursday'>-</span><input type='text' class='time-picker-thursday' name='thu_coupon_expiry_time_"+totalHoursCountThursday+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_thursday cross-hour'>X</a></span>";


			$(".thursday").append(appendedElementThursday);
			$("#total_hours_count_thursday").val(totalHoursCountThursday);
			flatPicker('thursday');
		});

		// Remove each input item of thursday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_thursday", function (){
			let totalHoursCountThursday = $("#total_hours_count_thursday").val();
			$(this).closest("span").remove();
			totalHoursCountThursday -= 1;
			$("#total_hours_count_thursday").val(totalHoursCountThursday);
		})

		/*
	   ========================================
		   Days and Hours fields for Friday
	   ========================================
	   */

		// Enable flatpicker on friday default input fields
		flatPicker('friday');

		// Show hide friday fields
		showHideDayFields('friday','fri');

		// Show hide thursday hours on the basis of clicking the thursday checkbox
		$("#coupon_apply_on_friday").on("change",function (){
			showHideDayFields('friday','fri');
		});

		// Add input field dynamically for friday.
		let totalHoursCountFriday = $("#total_hours_count_friday").val();
		$(document).on("click","#fri_add_more_hours",function(){
			totalHoursCountFriday++;

			let appendedElementFriday = "<span class='appededItem first-input'><input type='text' class='time-picker-friday' name='fri_coupon_start_time_"+totalHoursCountFriday+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_friday'>-</span><input type='text' class='time-picker-friday' name='fri_coupon_expiry_time_"+totalHoursCountFriday+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_friday cross-hour'>X</a></span>";


			$(".friday").append(appendedElementFriday);
			$("#total_hours_count_friday").val(totalHoursCountFriday);
			flatPicker('friday');
		});

		// Remove each input item of thursday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_friday", function (){
			let totalHoursCountFriday = $("#total_hours_count_friday").val();
			$(this).closest("span").remove();
			totalHoursCountFriday -= 1;
			$("#total_hours_count_friday").val(totalHoursCountFriday);
		})

		$('.toggle-input').on('change', function() {
			// Set the value of the current checkbox to 'yes' if checked, or '' if unchecked
			$(this).val(this.checked ? 'yes' : '');
		});

	});
})(jQuery);

