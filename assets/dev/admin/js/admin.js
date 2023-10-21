(function($) {
	"use strict";
	$(document).ready(function(){

		// destructuring internationalization functions for making text translatable
		const { __, _x, _n, _nx } = wp.i18n;

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
				// applyDaysHoursOfWeekField.hide();
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
				// applyDaysHoursOfWeekField.show();
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

		// place the 'selectedValuesContainer' container after the all_selected_products option grp input field
		$("#selectedValuesContainer").insertAfter(".all_selected_products .options_group");

		// show premium feature text clicking on min max input field
		$(document).on('click','.product-quantity-input', function (){
			$('body').focusout().removeClass('show');
			$(this).closest('.product-wrap').find('.product-wrap-pro').addClass('show');
		});
		$(document).on('focusout', '.product-quantity-input', function (){
			$('.product-wrap-pro').removeClass('show');
		});

		$(document).on("click",".remove_product", function (){
			// get value from remove product element
			let value = $(this).attr("data-value");
			// now find this title inside select box element and option
			$(`#all_selected_products option[value=${value}]`).removeAttr("selected");
			$(`#all_selected_products`).trigger('change');

			// call this function for handlingAllProductSection task
			removeProductItemCard($(this));
		});

		$(document).on("change","#all_selected_products", function (){
			// call this function for handlingAllProductSection task
			handleSelectProductChange();
		});

		function removeProductItemCard(element){
			element.closest(".product-item-whole").remove();
		}

		function handleSelectProductChange(){
			// get all selected products and store those data inside a temp variable
			let selectedProducts = $("#all_selected_products option:selected");
			// run a loop for doing all necessary action that will be needed.
			// define a temp variable for storing html
			let productItems = "";
			selectedProducts.each(function (){
				productItems += addProductItem($(this));
			})

			$("#selectedValuesContainer").html(productItems);
		}

		function convertTitleToName(title){
			return title.replace(" ","_").replace("-","_").toLowerCase();
		}

		function addProductItem(element, min = null, max = null){
			const title = element.attr("title");
			const value = element.attr("value");

			return `
				<div class="product-item-whole">
					<div class="product_title">${title}</div>
						<div class="product_min_max_main">
							<div class="product_min product-wrap">
								<span class="product-wrap-pro">${__( "This feature is only available on Pro", "hex-coupon-for-woocommerce" )}</span>
								<div class="product-wrap-inner">
									<p class="product-wrap-para">${__("min quantity", "hex-coupon-for-woocommerce")}</p>
									<input name="product_min[${convertTitleToName(title)}]" class="product-quantity-input" placeholder="No minimum" type="number" readonly="">
								</div>
							</div>
							<div class="product_max product-wrap">
								<span class="product-wrap-pro">${__( "This feature is only available on Pro" , "hex-coupon-for-woocommerce" )}</span>
								<div class="product-wrap-inner">
								<p class="product-wrap-para">${__("max quantity", "hex-coupon-for-woocommerce")}</p>
								<input name="product_max[${convertTitleToName(title)}]" class="product-quantity-input" placeholder="No maximum" type="number" readonly="">
							</div>
							<a href="javascript:void(0)" class="dashicons dashicons-no-alt remove_product" data-value="${value}" data-title="${title}"></a>
						</div>
					</div>
				</div>
			`;
		}

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
		const applyRedirectSharableLink = $("input[name='sharable_url_coupon[apply_redirect_sharable_link]']");
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
			const alertText = __( "URL copied to clipboard:", "hex-coupon-for-woocommerce" );
			alert(alertText + text); // Show an alert
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
			$(".all_selected_products").show();
		} else {
			$(".apply_on_listed_product").hide();
			$(".all_selected_products").hide();
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
		applyDaysHoursOfWeek.on("change", function () {
			if ($(this).is(":checked")) {
				dayTimeHoursBlock.show();
			} else {
				dayTimeHoursBlock.hide();
			}
		});

		// Show hide days and hours and apply days & hour on the basis of clicking the discount_type select input
		// Get the currently selected option's value
		var selectedValue = $(discountTypeField).val();

		// Show hide fields in cart product condition on the basis of clicking the checkbox
		$("#apply_cart_condition_for_customer_on_products").on("change", function () {
			if ($(this).is(":checked")) {
				$(".apply_on_listed_product").show();
				$(".all_selected_products").show();
			} else {
				$(".apply_on_listed_product").hide();
				$(".all_selected_products").hide();
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
				$(".add_more_hours_"+dayShortName+"_pro_text").hide();
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
		$("#coupon_apply_on_saturday").on("change", function () {
			showHideDayFields('saturday', 'sat');
		});

		// Function to add input field dynamically for saturday
		function addSaturdayInputField(){
			let totalHoursCountSaturdayVal = totalHoursCountSaturday.val();

			if (totalHoursCountSaturdayVal < 2) {
				totalHoursCountSaturdayVal++;

				let appendedElementSaturday = "<span class='appededItem first-input'><input type='text' class='time-picker-saturday' name='sat_coupon_start_time_" + totalHoursCountSaturdayVal + "' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_saturday'>-</span><input type='text' class='time-picker-saturday' name='sat_coupon_expiry_time_" + totalHoursCountSaturdayVal + "'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='dashicons dashicons-no-alt cross_hour_saturday cross-hour'></a></span>";

				$(".saturday").append(appendedElementSaturday);
				totalHoursCountSaturday.val(totalHoursCountSaturdayVal);
				flatPicker('saturday');

				if(totalHoursCountSaturdayVal >= 2){
					// Show the additional hours div when adding hours
					$(".add_more_hours_sat_pro_text").show();
				}
				else if(totalHoursCountSaturdayVal < 2) {
					// Show the additional hours div when adding hours
					$(".add_more_hours_sat_pro_text").hide();
				}

			}
		}

		// Add input field dynamically for saturday when the button is clicked
		$(document).on("click", "#sat_add_more_hours", addSaturdayInputField);

		// Remove each input item of saturday after clicking the cross icon.
		$(document).on("click", ".cross_hour_saturday", function () {
			let totalHoursCountSaturdayVal = totalHoursCountSaturday.val();
			$(this).closest("span").remove();

			if (totalHoursCountSaturdayVal > 0) {
				totalHoursCountSaturdayVal--;
			}

			totalHoursCountSaturday.val(totalHoursCountSaturdayVal);

			// Enable the "Add More Hours" button since an item was removed
			$("#sat_add_more_hours").prop("disabled", false);

			// If no additional hours are present, hide the additional hours div
			if (totalHoursCountSaturdayVal < 2) {
				$(".add_more_hours_sat_pro_text").hide();
			}
		});

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
		$("#coupon_apply_on_sunday").on("change", function () {
			showHideDayFields('sunday', 'sun');
		});

		// Function to add input field dynamically for sunday
		function addSundayInputField(){
			let totalHoursCountSundayVal = totalHoursCountSunday.val();

			if (totalHoursCountSundayVal < 2) {
				totalHoursCountSundayVal++;

				let appendedElementSunday = "<span class='appededItem first-input'><input type='text' class='time-picker-sunday' name='sun_coupon_start_time_" + totalHoursCountSundayVal + "' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_sunday'>-</span><input type='text' class='time-picker-sunday' name='sun_coupon_expiry_time_" + totalHoursCountSundayVal + "'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='dashicons dashicons-no-alt cross_hour_sunday cross-hour'></a></span>";

				$(".sunday").append(appendedElementSunday);
				totalHoursCountSunday.val(totalHoursCountSundayVal);
				flatPicker('sunday');

				if(totalHoursCountSundayVal >= 2){
					// Show the additional hours div when adding hours
					$(".add_more_hours_sun_pro_text").show();
				}
				else if(totalHoursCountSundayVal < 2){
					// Show the additional hours div when adding hours
					$(".add_more_hours_sun_pro_text").hide();
				}
			}
		}

		// Add input field dynamically for sunday when the button is clicked
		$(document).on("click", "#sun_add_more_hours", addSundayInputField);

		// Remove each input item of sunday after clicking the cross icon.
		$(document).on("click", ".cross_hour_sunday", function () {
			let totalHoursCountSundayVal = totalHoursCountSunday.val();
			$(this).closest("span").remove();

			if (totalHoursCountSundayVal > 0) {
				totalHoursCountSundayVal--;
			}

			totalHoursCountSunday.val(totalHoursCountSundayVal);

			// Enable the "Add More Hours" button since an item was removed
			$("#sun_add_more_hours").prop("disabled", false);

			// If no additional hours are present, hide the additional hours div
			if (totalHoursCountSundayVal < 2) {
				$(".add_more_hours_sun_pro_text").hide();
			}
		});

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
		$("#coupon_apply_on_monday").on("change", function () {
			showHideDayFields('monday', 'mon');
		});

		// Function to add input field dynamically for monday
		function addMondayInputField(){
			let totalHoursCountMondayVal = totalHoursCountMonday.val();

			if (totalHoursCountMondayVal < 2) {
				totalHoursCountMondayVal++;

				let appendedElementMonday = "<span class='appededItem first-input'><input type='text' class='time-picker-monday' name='mon_coupon_start_time_" + totalHoursCountMondayVal + "' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_monday'>-</span><input type='text' class='time-picker-monday' name='mon_coupon_expiry_time_" + totalHoursCountMondayVal + "'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='dashicons dashicons-no-alt cross_hour_monday cross-hour'></a></span>";

				$(".monday").append(appendedElementMonday);
				totalHoursCountMonday.val(totalHoursCountMondayVal);
				flatPicker('monday');

				if(totalHoursCountMondayVal >= 2){
					// Show the additional hours div when adding hours
					$(".add_more_hours_mon_pro_text").show();
				}
				else if(totalHoursCountMondayVal < 2) {
					// Show the additional hours div when adding hours
					$(".add_more_hours_mon_pro_text").hide();
				}

			}
		}

		// Add input field dynamically for monday when the button is clicked
		$(document).on("click", "#mon_add_more_hours", addMondayInputField);

		// Remove each input item of monday after clicking the cross icon.
		$(document).on("click", ".cross_hour_monday", function () {
			let totalHoursCountMondayVal = totalHoursCountMonday.val();
			$(this).closest("span").remove();

			if (totalHoursCountMondayVal > 0) {
				totalHoursCountMondayVal--;
			}

			totalHoursCountMonday.val(totalHoursCountMondayVal);

			// Enable the "Add More Hours" button since an item was removed
			$("#mon_add_more_hours").prop("disabled", false);

			// If no additional hours are present, hide the additional hours div
			if (totalHoursCountMondayVal < 2) {
				$(".add_more_hours_mon_pro_text").hide();
			}
		});

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
		$("#coupon_apply_on_tuesday").on("change", function () {
			showHideDayFields('tuesday', 'tue');
		});

		// Function to add input field dynamically for tuesday
		function addTuesdayInputField(){
			let totalHoursCountTuesdayVal = totalHoursCountTuesday.val();

			if (totalHoursCountTuesdayVal < 2) {
				totalHoursCountTuesdayVal++;

				let appendedElementTuesday = "<span class='appededItem first-input'><input type='text' class='time-picker-tuesday' name='tue_coupon_start_time_" + totalHoursCountTuesdayVal + "' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_tuesday'>-</span><input type='text' class='time-picker-tuesday' name='tue_coupon_expiry_time_" + totalHoursCountTuesdayVal + "'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='dashicons dashicons-no-alt cross_hour_tuesday cross-hour'></a></span>";

				$(".tuesday").append(appendedElementTuesday);
				totalHoursCountTuesday.val(totalHoursCountTuesdayVal);
				flatPicker('tuesday');

				if(totalHoursCountTuesdayVal >= 2){
					// Show the additional hours div when adding hours
					$(".add_more_hours_tue_pro_text").show();
				}
				else if(totalHoursCountTuesdayVal < 2) {
					// Show the additional hours div when adding hours
					$(".add_more_hours_tue_pro_text").hide();
				}

			}
		}

		// Add input field dynamically for tuesday when the button is clicked
		$(document).on("click", "#tue_add_more_hours", addTuesdayInputField);

		// Remove each input item of tuesday after clicking the cross icon.
		$(document).on("click", ".cross_hour_tuesday", function () {
			let totalHoursCountTuesdayVal = totalHoursCountTuesday.val();
			$(this).closest("span").remove();

			if (totalHoursCountTuesdayVal > 0) {
				totalHoursCountTuesdayVal--;
			}

			totalHoursCountTuesday.val(totalHoursCountTuesdayVal);

			// Enable the "Add More Hours" button since an item was removed
			$("#tue_add_more_hours").prop("disabled", false);

			// If no additional hours are present, hide the additional hours div
			if (totalHoursCountTuesdayVal < 2) {
				$(".add_more_hours_tue_pro_text").hide();
			}
		});

		/*
	   ========================================
		   Days and Hours fields for Wednesday
	   ========================================
	   */

		// Enable flatpicker on wednesday default input fields
		flatPicker('wednesday');

		// Show hide wednesday fields
		showHideDayFields('wednesday','wed');

		// Show hide wednesday hours on the basis of clicking the wednesday checkbox
		$("#coupon_apply_on_wednesday").on("change", function () {
			showHideDayFields('wednesday', 'wed');
		});

		// Function to add input field dynamically for wednesday
		function addWednesdayInputField(){
			let totalHoursCountWednesdayVal = totalHoursCountWednesday.val();

			if (totalHoursCountWednesdayVal < 2) {
				totalHoursCountWednesdayVal++;

				let appendedElementWednesday = "<span class='appededItem first-input'><input type='text' class='time-picker-wednesday' name='wed_coupon_start_time_" + totalHoursCountWednesdayVal + "' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_wednesday'>-</span><input type='text' class='time-picker-wednesday' name='wed_coupon_expiry_time_" + totalHoursCountWednesdayVal + "'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='dashicons dashicons-no-alt cross_hour_wednesday cross-hour'></a></span>";

				$(".wednesday").append(appendedElementWednesday);
				totalHoursCountWednesday.val(totalHoursCountWednesdayVal);
				flatPicker('wednesday');

				if(totalHoursCountWednesdayVal >= 2){
					// Show the additional hours div when adding hours
					$(".add_more_hours_wed_pro_text").show();
				}
				else if(totalHoursCountWednesdayVal < 2) {
					// Show the additional hours div when adding hours
					$(".add_more_hours_wed_pro_text").hide();
				}

			}
		}

		// Add input field dynamically for wednesday when the button is clicked
		$(document).on("click", "#wed_add_more_hours", addWednesdayInputField);

		// Remove each input item of wednesday after clicking the cross icon.
		$(document).on("click", ".cross_hour_wednesday", function () {
			let totalHoursCountWednesdayVal = totalHoursCountWednesday.val();
			$(this).closest("span").remove();

			if (totalHoursCountWednesdayVal > 0) {
				totalHoursCountWednesdayVal--;
			}

			totalHoursCountWednesday.val(totalHoursCountWednesdayVal);

			// Enable the "Add More Hours" button since an item was removed
			$("#wed_add_more_hours").prop("disabled", false);

			// If no additional hours are present, hide the additional hours div
			if (totalHoursCountWednesdayVal < 2) {
				$(".add_more_hours_wed_pro_text").hide();
			}
		});

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
		$("#coupon_apply_on_thursday").on("change", function () {
			showHideDayFields('thursday', 'thu');
		});

		// Function to add input field dynamically for thursday
		function addThursdayInputField(){
			let totalHoursCountThursdayVal = totalHoursCountThursday.val();

			if (totalHoursCountThursdayVal < 2) {
				totalHoursCountThursdayVal++;

				let appendedElementThursday = "<span class='appededItem first-input'><input type='text' class='time-picker-thursday' name='thu_coupon_start_time_" + totalHoursCountThursdayVal + "' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_thursday'>-</span><input type='text' class='time-picker-thursday' name='thu_coupon_expiry_time_" + totalHoursCountThursdayVal + "'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='dashicons dashicons-no-alt cross_hour_thursday cross-hour'></a></span>";

				$(".thursday").append(appendedElementThursday);
				totalHoursCountThursday.val(totalHoursCountThursdayVal);
				flatPicker('thursday');

				if(totalHoursCountThursdayVal >= 2){
					// Show the additional hours div when adding hours
					$(".add_more_hours_thu_pro_text").show();
				}
				else if(totalHoursCountThursdayVal < 2) {
					// Show the additional hours div when adding hours
					$(".add_more_hours_thu_pro_text").hide();
				}
			}
		}

		// Add input field dynamically for thursday when the button is clicked
		$(document).on("click", "#thu_add_more_hours", addThursdayInputField);

		// Remove each input item of thursday after clicking the cross icon.
		$(document).on("click", ".cross_hour_thursday", function () {
			let totalHoursCountThursdayVal = totalHoursCountThursday.val();
			$(this).closest("span").remove();

			if (totalHoursCountThursdayVal > 0) {
				totalHoursCountThursdayVal--;
			}

			totalHoursCountThursday.val(totalHoursCountThursdayVal);

			// Enable the "Add More Hours" button since an item was removed
			$("#thu_add_more_hours").prop("disabled", false);

			// If no additional hours are present, hide the additional hours div
			if (totalHoursCountThursdayVal < 2) {
				$(".add_more_hours_thu_pro_text").hide();
			}
		});

		/*
	   ========================================
		   Days and Hours fields for Friday
	   ========================================
	   */

		// Enable flatpicker on friday default input fields
		flatPicker('friday');

		// Show hide friday fields
		showHideDayFields('friday','fri');

		// Show hide friday hours on the basis of clicking the friday checkbox
		$("#coupon_apply_on_friday").on("change", function () {
			showHideDayFields('friday', 'fri');
		});

		// Function to add input field dynamically for friday
		function addFridayInputField(){
			let totalHoursCountFridayVal = totalHoursCountFriday.val();

			if (totalHoursCountFridayVal < 2) {
				totalHoursCountFridayVal++;

				let appendedElementFriday = "<span class='appededItem first-input'><input type='text' class='time-picker-friday' name='fri_coupon_start_time_" + totalHoursCountFridayVal + "' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_friday'>-</span><input type='text' class='time-picker-friday' name='fri_coupon_expiry_time_" + totalHoursCountFridayVal + "'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='dashicons dashicons-no-alt cross_hour_friday cross-hour'></a></span>";

				$(".friday").append(appendedElementFriday);
				totalHoursCountFriday.val(totalHoursCountFridayVal);
				flatPicker('friday');

				if(totalHoursCountFridayVal >= 2){
					// Show the additional hours div when adding hours
					$(".add_more_hours_fri_pro_text").show();
				}
				else if(totalHoursCountFridayVal < 2) {
					// Show the additional hours div when adding hours
					$(".add_more_hours_fri_pro_text").hide();
				}
			}
		}

		// Add input field dynamically for friday when the button is clicked
		$(document).on("click", "#fri_add_more_hours", addFridayInputField);

		// Remove each input item of thursday after clicking the cross icon.
		$(document).on("click", ".cross_hour_friday", function () {
			let totalHoursCountFridayVal = totalHoursCountFriday.val();
			$(this).closest("span").remove();

			if (totalHoursCountFridayVal > 0) {
				totalHoursCountFridayVal--;
			}

			totalHoursCountFriday.val(totalHoursCountFridayVal);

			// Enable the "Add More Hours" button since an item was removed
			$("#fri_add_more_hours").prop("disabled", false);

			// If no additional hours are present, hide the additional hours div
			if (totalHoursCountFridayVal < 2) {
				$(".add_more_hours_fri_pro_text").hide();
			}
		});

		$('.toggle-input').on('change', function() {
			// Set the value of the current checkbox to 'yes' if checked, or '' if unchecked
			$(this).val(this.checked ? 'yes' : '');
		});

	});
})(jQuery);
