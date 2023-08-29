(function($) {
	"use strict";
	$(document).ready(function(){

		/*
       ========================================
           Mixed Code
       ========================================
       */

		// Select hexcoupon shortcode field
		$(".shortcode_column").click(function(){
			this.select();
		});

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

		const couponTypeField = $(".coupon_type_field");
		const couponAmountField = $(".coupon_amount_field");

		// Place coupon type select input field before the coupon amount input field
		couponTypeField.insertBefore(couponAmountField);

		// Place customer purchases radio buttons before the coupon type select input

		const customerPurchasesDiv = $(".customer_purchases");

		const addSpecificProductToPurchaseClass = $(".add_specific_product_to_purchase");

		const addSpecificProductToPurchaseId = $("#add_specific_product_to_purchase");

		customerPurchasesDiv.insertAfter(couponTypeField);

		const customerPurchasesField = $(".customer_purchases_field");

		// Place add a specific product to purchase select2 field after the customer purchases radio buttons
		addSpecificProductToPurchaseClass.insertAfter(customerPurchasesField);
		addSpecificProductToPurchaseId.insertAfter(".add_specific_product_to_purchase label");


		// Place the tooltip of add a specific product to purchase select2 field
		$(".add_specific_product_to_purchase_tooltip").insertAfter(".add_specific_product_to_purchase span.select2-container");

		const addCategoriesToPurchase = $(".add_categories_to_purchase");

		// Place the add categories select2 input fields after the customer purchases field
		addCategoriesToPurchase.insertAfter(customerPurchasesField);
		$(".add_categories_to_purchase_tooltip").insertAfter(".add_categories_to_purchase span.select2-container");

		const customerGetsAsFreeClass = $(".customer_gets_as_free");

		// Place the customer gets as free after the customer purchases
		customerGetsAsFreeClass.insertAfter(customerPurchasesDiv);

		const addSpecificProductForFreeClass = $(".add_specific_product_for_free");
		const addSpecificProductForFreeID = $("#add_specific_product_for_free");

		addSpecificProductForFreeClass.insertAfter(".customer_gets_as_free_field");
		addSpecificProductForFreeID.insertAfter(".add_specific_product_for_free label");

		$(".add_specific_product_for_free_tooltip").insertAfter(".add_specific_product_for_free span.select2-container");

		const bogoDealCheckboxes = $(".bogo_deal_checkboxes");
		// Place the bogo deal checkboxes after the customer gets as free div
		bogoDealCheckboxes.insertAfter(customerGetsAsFreeClass);


		// Remove all other fields if BOGO is selected
		const discountTypeField = $("select[name^='discount_type']");

		const freeShippingField = $(".free_shipping_field");
		const expiryDateField = $(".expiry_date_field");
		const messageForCouponExpiryDateField = $(".message_for_coupon_expiry_date_field");
		const couponStartingDateField = $(".coupon_starting_date_field");
		const messageForCouponStartingDateField = $(".message_for_coupon_starting_date_field");
		const applyDaysHoursOfWeekField = $(".apply_days_hours_of_week_field");

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
				customerPurchasesDiv.show()
				customerGetsAsFreeClass.show()
				bogoDealCheckboxes.show()

				couponAmountField.hide();
				freeShippingField.hide();
				expiryDateField.hide();
				messageForCouponExpiryDateField.hide();
				couponStartingDateField.hide();
				messageForCouponStartingDateField.hide();
				applyDaysHoursOfWeekField.hide();
			}
			else {
				customerPurchasesDiv.hide()
				customerGetsAsFreeClass.hide()
				bogoDealCheckboxes.hide()

				couponAmountField.show();
				freeShippingField.show();
				expiryDateField.show();
				messageForCouponExpiryDateField.show();
				couponStartingDateField.show();
				messageForCouponStartingDateField.show();
				applyDaysHoursOfWeekField.show();
			}
		});

		// perform on page load
		discountTypeField.trigger("change");

		// Don't allow more than one product selection on selecting a specific product from the customer purchases field
		const customerPurchases = $("input[name='customer_purchases']");

		// Show or hide product selection and category selection input fields if product categories type is selected
		customerPurchases.on("change",function(){
			if("product_categories" === $(this).val()){
				addCategoriesToPurchase.show();
				addSpecificProductToPurchaseClass.hide();
			}else {
				addCategoriesToPurchase.hide();
				addSpecificProductToPurchaseClass.show();
			}
		});

		// Control number of result selection if a specific product type is selected
		customerPurchases.on("change",function(){
			if("a_specific_product" === $(this).val()){
				addSpecificProductToPurchaseId.select2({
					maximumSelectionLength: 1 // Set maximum selection to 1
				});
			}else {
				addSpecificProductToPurchaseId.select2({
					maximumSelectionLength: 0 // Set maximum selection to unlimited
				});
			}
		});

		// Trigger the change on page load
		customerPurchases.trigger("change");

		// Remove all li of select2 button except the first one on selecting the 'a_specific_product' radio button
		$('input[name="customer_purchases"]').on('change', function() {
			// Check if the radio button is checked and its value is 'a_specific_product'
			if ($(this).is(':checked') && $(this).val() === 'a_specific_product') {
				// Remove all 'select2-selection__choice' elements except the first one inside '.add_specific_product_to_purchase'
				$('.add_specific_product_to_purchase .select2-selection__choice').slice(1).remove();

				// $('select[name="add_specific_product_to_purchase"] option:selected').slice(1).remove();

				var selectedOption = $('select[name="add_specific_product_to_purchase"] option:selected:first');

				// Remove the selected attribute from all options except the first selected one
				$('select[name="add_specific_product_to_purchase"] option:selected:not(:first)').removeAttr('selected');

				// Reset the selected option back to the first one
				selectedOption.prop('selected', true);
			}
		});

		// Don't allow more than one product selection on selecting a specific product from the customer gets as free field
		const customerGetsAsFree = $("input[name='customer_gets_as_free']");

		// Show or hide product selection and category selection input fields if product categories type is selected
		customerGetsAsFree.on("change",function(){
			if($(this).is(":checked") && "same_product_added_to_cart" === $(this).val()){
				addSpecificProductForFreeClass.hide();
			}else{
				addSpecificProductForFreeClass.show();
			}
		});

		customerGetsAsFree.trigger("change");

		// Control number of result selection if a specific product type is selected
		customerGetsAsFree.on("change",function(){
			if("a_specific_product" === $(this).val()){
				addSpecificProductForFreeID.select2({
					maximumSelectionLength: 1 // Set maximum selection to 1
				});
			}else {
				addSpecificProductForFreeID.select2({
					maximumSelectionLength: 0 // Set maximum selection to unlimited
				});
			}
		});

		customerGetsAsFree.trigger("change");

		// Remove all li of select2 button except the first one on selecting the 'a_specific_product' radio button
		$('input[name="customer_gets_as_free"]').on('change', function() {
			// Check if the radio button is checked and its value is 'a_specific_product'
			if ($(this).is(':checked') && $(this).val() === 'a_specific_product') {
				// Remove all 'select2-selection__choice' elements except the first one inside '.add_specific_product_to_purchase'
				$('.customer_gets_as_free .select2-selection__choice').slice(1).remove();
			}
		});


		// customerGetsAsFree.on("change",function(){
		// 	if("same_product_added_to_cart" === $(this).val()){
		// 		$(".add_specific_product_for_free").hide();
		// 	}
		// });


		// Trigger the change on page load
		// customerGetsAsFree.trigger("change");

		// customerGetsAsFree.on("change",function(){
		// 	if("same_product_added_to_cart" === $(this).val()){
		// 		$(".add_specific_product_for_free").hide();
		// 	}
		// });

		// customerGetsAsFree.trigger("change");

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

		// $(".add_categories_to_purchase_tooltip").insertAfter(".add_categories_to_purchase span.select2-container");
		// $(".add_categories_to_purchase").insertAfter(".customer_purchases");


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

		// working code, code add min and max item all selected product
		// $(".all_selected_products").on("select2:select", function(event) {
		// 	var selectedValues = $(this).val(); // Get an array of selected values
		//
		// 	if (selectedValues) {
		// 		// Clear the container before adding new selections
		// 		$("#selectedValuesContainer").empty();
		//
		// 		// Loop through selected values and add them to the container
		// 		selectedValues.forEach(function(value) {
		// 			var text = $(".all_selected_products option[value='" + value + "']").text();
		//
		// 			$("#selectedValuesContainer").append("<div class='whole'><span class='select2-selection__choice'>" + text + "</span><div class='product_min_max'><input name='product_min_quantity[]' placeholder='No minimum' type='number' style='float:left; width:50% !important;'><input name='product_max_quantity[]' style='width:50% !important;' placeholder='No maximum' type='number'><a class='remove_product'>X</a></div></div>");
		// 		});
		//
		// 		// Update the original input field value with the selected values
		// 		$(this).val(selectedValues).trigger("change");
		// 	}
		// });
		//
		// // Add click event handler for the .remove_product class
		// $(document).on("click", ".remove_product", function() {
		// 	// Remove the parent .whole element when .remove_product is clicked
		// 	$(this).closest(".whole").remove();
		// });
		//
		//
		// $(".all_selected_products ul.select2-selection__rendered").remove();


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
           Usage Limits
       ========================================
       */

		// On page load
		const resetOptionValue = $("#reset_option_value").val(); // Get the value of 'reset_option_value' input

		// Find all p elements within the specified div
		const paragraphs = $(".reset_limit").find("p");

		// Loop through each p element and compare data-reset-value attribute
		paragraphs.each(function() {
			var dataResetValue = $(this).attr("data-reset-value");
			if (dataResetValue === resetOptionValue) {
				$(this).addClass("usage_limit_p_background");
			}
		});

		const resetLimitP = $(".reset_limit p");

		// On clicking the p element
		resetLimitP.click(function (){
			resetLimitP.removeClass("usage_limit_p_background");

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
				paragraphs.show();
			}
			else {
				paragraphs.hide();
			}
		});

		// On page load
		if($(resetUsageLimit).is(":checked")){
			paragraphs.show();
		}
		else {
			paragraphs.hide();
		}


		/*
       ========================================
           Geographic Restriction
       ========================================
       */
		const applyGeographicRestriction = $("input[name='apply_geographic_restriction']");
		const restrictedShippingZones = $(".restricted_shipping_zones");
		const restrictedCountries = $(".restricted_countries");

		applyGeographicRestriction.on("change", function() {
			if ($(this).is(":checked") && $(this).val() === 'restrict_by_shipping_zones') {
				restrictedShippingZones.show();
				restrictedCountries.hide();
			} else if ($(this).is(":checked") && $(this).val() === 'restrict_by_countries') {
				restrictedShippingZones.hide();
				restrictedCountries.show();
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
		const applyRedirectSharableLink = $("input[name='apply_redirect_sharable_link']");
		const redirectLinkField = $(".redirect_link_field");

		applyRedirectSharableLink.on("change", function() {
			if ($(this).is(":checked") && $(this).val() === 'redirect_back_to_origin') {
				redirectLinkField.hide();
			} else if ($(this).is(":checked") && $(this).val() === 'redirect_to_custom_link') {
				redirectLinkField.show();
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


		/*
       ========================================
           	Days & Hours in General Tab
       ========================================
       */

		const applyDaysHoursOfWeek = $("#apply_days_hours_of_week");
		const dayTimeHoursBlock = $(".day_time_hours_block");
		const totalHoursCountSaturday = $("#total_hours_count_saturday");
		const totalHoursCountSunday = $("#total_hours_count_sunday");
		const totalHoursCountMonday = $("#total_hours_count_monday");
		const totalHoursCountTuesday = $("#total_hours_count_tuesday");
		const totalHoursCountWednesday = $("#total_hours_count_wednesday");
		const totalHoursCountThursday = $("#total_hours_count_thursday");
		const totalHoursCountFriday = $("#total_hours_count_friday");

		// Show hide days and hours on page load
		if(applyDaysHoursOfWeek.is(":checked")) {
			dayTimeHoursBlock.show();
		} else {
			dayTimeHoursBlock.hide();
			totalHoursCountSaturday.val('0');
			totalHoursCountSunday.val('0');
			totalHoursCountMonday.val('0');
			totalHoursCountTuesday.val('0');
			totalHoursCountWednesday.val('0');
			totalHoursCountThursday.val('0');
			totalHoursCountFriday.val('0');
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

		/*
	   ========================================
		   Days and Hours fields for Saturday
	   ========================================
	   */

		// Enable flatpicker on saturday default input fields
		flatPicker('saturday');

		// Show hide saturday fields
		showHideDayFields('saturday','sat');

		// Show hide saturday hours on the basis of clicking the saturday checkbox
		$("#coupon_apply_on_saturday").on("change",function (){
			showHideDayFields('saturday','sat');
		});

		// Add input field dynamically for saturday.
		let totalHoursCountSaturdayVal = totalHoursCountSaturday.val();
		$(document).on("click","#sat_add_more_hours",function(){
			totalHoursCountSaturdayVal++;

			let appendedElementSaturday = "<span class='appededItem first-input'><input type='text' class='time-picker-saturday' name='sat_coupon_start_time_"+totalHoursCountSaturdayVal+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_saturday'>-</span><input type='text' class='time-picker-saturday' name='sat_coupon_expiry_time_"+totalHoursCountSaturdayVal+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_saturday cross-hour'>X</a></span>";


			$(".saturday").append(appendedElementSaturday);
			totalHoursCountSaturday.val(totalHoursCountSaturdayVal);
			flatPicker('saturday');
		});

		// Remove each input item of saturday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_saturday", function (){
			let totalHoursCountSaturdayVal = totalHoursCountSaturday.val();
			$(this).closest("span").remove();
			totalHoursCountSaturdayVal -= 1;
			totalHoursCountSaturday.val(totalHoursCountSaturdayVal);
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
		let totalHoursCountSundayVal = totalHoursCountSunday.val();
		$(document).on("click","#sun_add_more_hours",function(){
			totalHoursCountSundayVal++;

			let appendedElementSunday = "<span class='appededItem first-input'><input type='text' class='time-picker-sunday' name='sun_coupon_start_time_"+totalHoursCountSundayVal+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_sunday'>-</span><input type='text' class='time-picker-sunday' name='sun_coupon_expiry_time_"+totalHoursCountSundayVal+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_sunday cross-hour'>X</a></span>";


			$(".sunday").append(appendedElementSunday);
			totalHoursCountSunday.val(totalHoursCountSundayVal);
			flatPicker('sunday');
		});

		// Remove each input item of sunday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_sunday", function (){
			let totalHoursCountSundayVal = totalHoursCountSunday.val();
			$(this).closest("span").remove();
			totalHoursCountSundayVal -= 1;
			totalHoursCountSunday.val(totalHoursCountSundayVal);
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
		let totalHoursCountMondayVal = totalHoursCountMonday.val();
		$(document).on("click","#mon_add_more_hours",function(){
			totalHoursCountMondayVal++;

			let appendedElementMonday = "<span class='appededItem first-input'><input type='text' class='time-picker-monday' name='mon_coupon_start_time_"+totalHoursCountMondayVal+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_monday'>-</span><input type='text' class='time-picker-monday' name='mon_coupon_expiry_time_"+totalHoursCountMondayVal+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_monday cross-hour'>X</a></span>";


			$(".monday").append(appendedElementMonday);
			totalHoursCountMonday.val(totalHoursCountMondayVal);
			flatPicker('monday');
		});

		// Remove each input item of monday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_monday", function (){
			let totalHoursCountMondayVal = totalHoursCountMonday.val();
			$(this).closest("span").remove();
			totalHoursCountMondayVal -= 1;
			totalHoursCountMonday.val(totalHoursCountMondayVal);
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
		let totalHoursCountTuesdayVal = $("#total_hours_count_tuesday").val();
		$(document).on("click","#tue_add_more_hours",function(){
			totalHoursCountTuesdayVal++;

			let appendedElementTuesday = "<span class='appededItem first-input'><input type='text' class='time-picker-tuesday' name='tue_coupon_start_time_"+totalHoursCountTuesdayVal+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_tuesday'>-</span><input type='text' class='time-picker-tuesday' name='tue_coupon_expiry_time_"+totalHoursCountTuesdayVal+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_tuesday cross-hour'>X</a></span>";


			$(".tuesday").append(appendedElementTuesday);
			totalHoursCountTuesday.val(totalHoursCountTuesdayVal);
			flatPicker('tuesday');
		});

		// Remove each input item of tuesday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_tuesday", function (){
			let totalHoursCountTuesdayVal = totalHoursCountTuesday.val();
			$(this).closest("span").remove();
			totalHoursCountTuesdayVal -= 1;
			totalHoursCountTuesday.val(totalHoursCountTuesdayVal);
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
		let totalHoursCountWednesdayVal = $("#total_hours_count_wednesday").val();
		$(document).on("click","#wed_add_more_hours",function(){
			totalHoursCountWednesdayVal++;

			let appendedElementWednesday = "<span class='appededItem first-input'><input type='text' class='time-picker-wednesday' name='tue_coupon_start_time_"+totalHoursCountWednesdayVal+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_wednesday'>-</span><input type='text' class='time-picker-wednesday' name='wed_coupon_expiry_time_"+totalHoursCountWednesdayVal+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_wednesday cross-hour'>X</a></span>";


			$(".wednesday").append(appendedElementWednesday);
			totalHoursCountWednesday.val(totalHoursCountWednesdayVal);
			flatPicker('wednesday');
		});

		// Remove each input item of tuesday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_wednesday", function (){
			let totalHoursCountWednesdayVal = totalHoursCountWednesday.val();
			$(this).closest("span").remove();
			totalHoursCountWednesdayVal -= 1;
			totalHoursCountWednesday.val(totalHoursCountWednesdayVal);
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
		let totalHoursCountThursdayVal = totalHoursCountThursday.val();
		$(document).on("click","#thu_add_more_hours",function(){
			totalHoursCountThursdayVal++;

			let appendedElementThursday = "<span class='appededItem first-input'><input type='text' class='time-picker-thursday' name='tue_coupon_start_time_"+totalHoursCountThursdayVal+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_thursday'>-</span><input type='text' class='time-picker-thursday' name='thu_coupon_expiry_time_"+totalHoursCountThursdayVal+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_thursday cross-hour'>X</a></span>";


			$(".thursday").append(appendedElementThursday);
			totalHoursCountThursday.val(totalHoursCountThursdayVal);
			flatPicker('thursday');
		});

		// Remove each input item of thursday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_thursday", function (){
			let totalHoursCountThursdayVal = totalHoursCountThursday.val();
			$(this).closest("span").remove();
			totalHoursCountThursdayVal -= 1;
			totalHoursCountThursday.val(totalHoursCountThursdayVal);
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
		let totalHoursCountFridayVal = totalHoursCountFriday.val();
		$(document).on("click","#fri_add_more_hours",function(){
			totalHoursCountFridayVal++;

			let appendedElementFriday = "<span class='appededItem first-input'><input type='text' class='time-picker-friday' name='fri_coupon_start_time_"+totalHoursCountFridayVal+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_friday'>-</span><input type='text' class='time-picker-friday' name='fri_coupon_expiry_time_"+totalHoursCountFridayVal+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_friday cross-hour'>X</a></span>";


			$(".friday").append(appendedElementFriday);
			totalHoursCountFriday.val(totalHoursCountFridayVal);
			flatPicker('friday');
		});

		// Remove each input item of thursday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_friday", function (){
			let totalHoursCountFridayVal = totalHoursCountFriday.val();
			$(this).closest("span").remove();
			totalHoursCountFridayVal -= 1;
			totalHoursCountFriday.val(totalHoursCountFridayVal);
		})

		$('.toggle-input').on('change', function() {
			// Set the value of the current checkbox to 'yes' if checked, or '' if unchecked
			$(this).val(this.checked ? 'yes' : '');
		});

	});
})(jQuery);
