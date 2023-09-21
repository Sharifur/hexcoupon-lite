<div id="vite-react-sample"></div>
<?php
//// Get the current month and year
//$current_month = date('n');
//$current_year = date('Y');
//
//// Get the total number of days in the current month
//$total_days = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);
//
//// Initialize an array to store daily post counts
//$daily_post_counts = array();
//
//// Loop through each day of the month
//for ($day = 1; $day <= $total_days; $day++) {
//	// Format the date in 'Y-m-d' format
//	$date = sprintf('%d-%02d-%02d', $current_year, $current_month, $day);
//
//	// WP_Query arguments to count posts created on a specific day
//	$args = array(
//		'post_type' => 'shop_coupon',      // Replace with your post type if needed
//		'post_status' => 'publish', // Retrieve only published posts
//		'date_query' => array(
//			array(
//				'year' => $current_year,
//				'month' => $current_month,
//				'day' => $day,
//			),
//		),
//		'fields' => 'ids', // Optimize query to retrieve post IDs only
//	);
//
//	// Create a new WP_Query instance
//	$query = new WP_Query($args);
//
//	// Store the post count for the current day
//	$daily_post_counts[$date] = $query->post_count;
//}
//var_dump($daily_post_counts);


