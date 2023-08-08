(function($) {
	"use strict";
	$(document).ready(function(){

		$(".hex__select2").select2({
			placeholder: function() {
				return $(this).data("placeholder");
			}
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
		}

		// Show hide days and hours on the basis of clicking the checkbox
		$("#apply_days_hours_of_week").on("change", function () {
			if ($(this).is(":checked")) {
				$(".day_time_hours_block").show();
			} else {
				$(".day_time_hours_block").hide();
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
				$("#total_hours_count").val('0');
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
		let totalHoursCount = $("#total_hours_count_saturday").val();
		$(document).on("click","#sat_add_more_hours",function(){
			totalHoursCount++;

			let appendedElement = "<span class='appededItem first-input'><input type='text' class='time-picker-saturday' name='sat_coupon_start_time_"+totalHoursCount+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_saturday'>-</span><input type='text' class='time-picker-saturday' name='sat_coupon_expiry_time_"+totalHoursCount+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_saturday'>X</a></span>";

			$(".saturday").append(appendedElement);
			$("#total_hours_count_saturday").val(totalHoursCount);
			flatPicker('saturday');
		});
		// Remove each input item of saturday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_saturday", function (){
			let totalHoursCountSunday = $("#total_hours_count_saturday").val();
			$(this).closest("div").remove();
			totalHoursCountSunday -= 1;
			$("#total_hours_count_saturday").val(totalHoursCountSunday);
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

			let appendedElementSunday = "<div class='appededItem'><input type='text' class='time-picker-sunday' name='sun_coupon_start_time_"+totalHoursCountSunday+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_sunday'>-</span><input type='text' class='time-picker-sunday' name='sun_coupon_expiry_time_"+totalHoursCountSunday+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_sunday'>X</a></div>";


			$(".sunday").append(appendedElementSunday);
			$("#total_hours_count_sunday").val(totalHoursCountSunday);
			flatPicker('sunday');
		});

		// Remove each input item of sunday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_sunday", function (){
			let totalHoursCountSunday = $("#total_hours_count_sunday").val();
			$(this).closest("div").remove();
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

			let appendedElementMonday = "<div class='appededItem'><input type='text' class='time-picker-monday' name='mon_coupon_start_time_"+totalHoursCountMonday+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_monday'>-</span><input type='text' class='time-picker-monday' name='mon_coupon_expiry_time_"+totalHoursCountMonday+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_monday'>X</a></div>";


			$(".monday").append(appendedElementMonday);
			$("#total_hours_count_monday").val(totalHoursCountMonday);
			flatPicker('monday');
		});

		// Remove each input item of monday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_monday", function (){
			let totalHoursCountMonday = $("#total_hours_count_monday").val();
			$(this).closest("div").remove();
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

			let appendedElementTuesday = "<div class='appededItem'><input type='text' class='time-picker-tuesday' name='tue_coupon_start_time_"+totalHoursCountTuesday+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_tuesday'>-</span><input type='text' class='time-picker-tuesday' name='tue_coupon_expiry_time_"+totalHoursCountTuesday+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_tuesday'>X</a></div>";


			$(".tuesday").append(appendedElementTuesday);
			$("#total_hours_count_tuesday").val(totalHoursCountTuesday);
			flatPicker('tuesday');
		});

		// Remove each input item of tuesday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_tuesday", function (){
			let totalHoursCountTusday = $("#total_hours_count_tuesday").val();
			$(this).closest("div").remove();
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

			let appendedElementWednesday = "<div class='appededItem'><input type='text' class='time-picker-wednesday' name='tue_coupon_start_time_"+totalHoursCountWednesday+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_wednesday'>-</span><input type='text' class='time-picker-wednesday' name='wed_coupon_expiry_time_"+totalHoursCountWednesday+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_wednesday'>X</a></div>";


			$(".wednesday").append(appendedElementWednesday);
			$("#total_hours_count_wednesday").val(totalHoursCountWednesday);
			flatPicker('wednesday');
		});

		// Remove each input item of tuesday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_wednesday", function (){
			let totalHoursCountWednesday = $("#total_hours_count_wednesday").val();
			$(this).closest("div").remove();
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

			let appendedElementThursday = "<div class='appededItem'><input type='text' class='time-picker-thursday' name='tue_coupon_start_time_"+totalHoursCountThursday+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_thursday'>-</span><input type='text' class='time-picker-thursday' name='thu_coupon_expiry_time_"+totalHoursCountThursday+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_thursday'>X</a></div>";


			$(".thursday").append(appendedElementThursday);
			$("#total_hours_count_thursday").val(totalHoursCountThursday);
			flatPicker('thursday');
		});

		// Remove each input item of thursday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_thursday", function (){
			let totalHoursCountThursday = $("#total_hours_count_thursday").val();
			$(this).closest("div").remove();
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

			let appendedElementFriday = "<div class='appededItem'><input type='text' class='time-picker-friday' name='fri_coupon_start_time_"+totalHoursCountFriday+"' id='coupon_start_time' value='' placeholder='HH:MM'><span class='input_separator_friday'>-</span><input type='text' class='time-picker-friday' name='fri_coupon_expiry_time_"+totalHoursCountFriday+"'  id='coupon_expiry_time' value='' placeholder='HH:MM'><a href='javascript:void(0)' class='cross_hour_friday'>X</a></div>";


			$(".friday").append(appendedElementFriday);
			$("#total_hours_count_friday").val(totalHoursCountFriday);
			flatPicker('friday');
		});

		// Remove each input item of thursday after clicking  the cross icon.
		$(document).on("click", ".cross_hour_friday", function (){
			let totalHoursCountFriday = $("#total_hours_count_friday").val();
			$(this).closest("div").remove();
			totalHoursCountFriday -= 1;
			$("#total_hours_count_friday").val(totalHoursCountFriday);
		})

		$('.toggle-input').on('change', function() {
			// Set the value of the current checkbox to 'yes' if checked, or '' if unchecked
			$(this).val(this.checked ? 'yes' : '');
		});

	});
})(jQuery);

