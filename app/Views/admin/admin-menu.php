<?php if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

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


//		// Get the current month and year
//		$current_month = date('n');
//		$current_year = date('Y');
//
//		// Get the current date
//		$current_date = date('Y-m-d');
//
//		// Calculate yesterday's date
//		$yesterday = date('Y-m-d', strtotime('-1 day', strtotime($current_date)));
//
//		// Initialize an array to store post counts of yesterday and today
//		$post_counts = array(
//			'yesterday' => 0,
//			'today' => 0,
//		);
//
//		// Initialize an array to store daily post counts
//		$daily_post_counts = array();
//
//		// Initialize an array to store monthly post counts
//		$monthly_post_counts = array();
//
//		// Initialize an array to store weekly post counts
//		$weekly_post_counts = array();
//
//		// Initialize an array to store daily counts for the current week
//		$daily_counts_current_week = array();
//
//
//		// Loop through each month of the year (from January to December)
//		for ( $month = 1; $month <= 12; $month++ ) {
//			// Get the total number of days in the current month
//			$total_days = cal_days_in_month(CAL_GREGORIAN, $month, $current_year);
//
//			// Initialize a count for the current month
//			$monthly_count = 0;
//
//			// Loop through each day of the month
//			for ( $day = 1; $day <= $total_days; $day++ ) {
//				// Format the date in 'Y-m-d' format
//				$date = sprintf('%d-%02d-%02d', $current_year, $current_month, $day);
//
//				// WP_Query arguments to count posts created on a specific day
//				$args = array(
//					'post_type' => 'shop_coupon',      // Replace with your post type if needed
//					'post_status' => 'publish', // Retrieve only published posts
//					'date_query' => array(
//						array(
//							'year' => $current_year,
//							'month' => $current_month,
//							'day' => $day,
//						),
//					),
//					'fields' => 'ids', // Optimize query to retrieve post IDs only
//				);
//
//				// Create a new WP_Query instance
//				$query = new WP_Query($args);
//
//				// Calculate the week number for the current date
//				$week_number = date('W', strtotime($date));
//
//				// Accumulate the post count for the current month
//				$monthly_count += $query->post_count;
//
//				// Store the post count for the current day
//				$daily_post_counts[$date] = $query->post_count;
//
//				// Check if the date matches yesterday or today and update counts accordingly
//				if ($date === $yesterday) {
//					$post_counts['yesterday'] = $query->post_count;
//				} elseif ($date === $current_date) {
//					$post_counts['today'] = $query->post_count;
//				}
//
//				// Now you have post counts for yesterday, today, and other days
//				$yesterday_count = $post_counts['yesterday'];
//				$today_count = $post_counts['today'];
//
//				// Add the post count to the weekly counts array
//				if (!isset($weekly_post_counts[$week_number])) {
//					$weekly_post_counts[$week_number] = 0;
//				}
//				$weekly_post_counts[$week_number] += $query->post_count;
//
//				// Create an array with daily counts for the current week
//				$daily_counts_array = array();
//				// Initialize daily counts for all days of the week
//				for ( $day = 0; $day <= 6; $day++ ) {
//					$daily_counts_array[$day] = array(
//						'created' => 0,
//					);
//				}
//
//				// Merge the daily counts for the current week into the array
//				foreach ( $daily_counts_current_week as $day_of_week => $counts ) {
//					$daily_counts_array[$day_of_week] = $counts;
//				}
//
//				// Store the monthly post count in the array
//				$monthly_post_counts[date('F', mktime(0, 0, 0, $month, 1))] = $monthly_count;
//			}
//		}
//		var_dump($daily_counts_array);


//// Get the current year and month
//$current_date = getdate();
//$year = $current_date['year'];
//$month = $current_date['mon'];
//
//$day_counts = array();
//
//// Loop through each day of the month (1-31) and count posts for each day
//for ($day = 1; $day <= 31; $day++) {
//	$args = array(
//		'post_type' => 'shop_coupon', // You can change 'post' to the desired post type
//		'posts_per_page' => -1,
//		'date_query' => array(
//			'year' => $year,
//			'month' => $month,
//			'day' => $day,
//		),
//	);
//
//	$query = new WP_Query($args);
//
//	// Get the post count for the current day
//	$post_count = $query->found_posts;
//
//	$day_counts[$day] = $post_count;
//
//
//
//	// Restore original post data
//	wp_reset_postdata();
//}

//print_r($day_counts);

//// Get the current date
//$current_date = date('Y-m-d');
//
//// Calculate the start date of the current week (Sunday)
//$week_start = date('Y-m-d', strtotime('last Sunday', strtotime($current_date)));
//
//// Initialize an array to store daily post counts for the current week
//$daily_post_counts_current_week = array();
//
//// Define an array to map day numbers to day names
//$day_names = array(
//	0 => 'Sun',
//	1 => 'Mon',
//	2 => 'Tue',
//	3 => 'Wed',
//	4 => 'Thu',
//	5 => 'Fri',
//	6 => 'Sat',
//);
//
//// Loop through each day of the current week (from Sunday to Saturday)
//for ($i = 0; $i < 7; $i++) {
//	// Calculate the date for the current day in 'Y-m-d' format
//	$day_date = date('Y-m-d', strtotime("+$i days", strtotime($week_start)));
//
//	// Get the day name for the current day
//	$day_name = $day_names[date('w', strtotime($day_date))];
//
//	// WP_Query arguments to count posts created on the current day
//	$args = array(
//		'post_type' => 'shop_coupon', // Replace with your post type if needed
//		'post_status' => 'publish',    // Retrieve only published posts
//		'date_query' => array(
//			array(
//				'year' => date('Y', strtotime($day_date)),
//				'month' => date('m', strtotime($day_date)),
//				'day' => date('d', strtotime($day_date)),
//			),
//		),
//		'fields' => 'ids', // Optimize query to retrieve post IDs only
//	);
//
//	// Create a new WP_Query instance
//	$query = new \WP_Query($args);
//
//	// Store the post count for the current day in the array
//	$daily_post_counts_current_week[$day_name] = $query->post_count;
//}

//var_dump($daily_post_counts_current_week);


//$current_year = date('Y');
//// Initialize an array to store monthly post counts
//$monthly_post_counts = array();
//
//// Loop through each month of the year (from January to December)
//for ($month = 1; $month <= 12; $month++) {
//	// WP_Query arguments to count posts created in the current month
//	$args = array(
//		'post_type' => 'shop_coupon',  // Replace with your post type if needed
//		'post_status' => 'publish',    // Retrieve only published posts
//		'date_query' => array(
//			array(
//				'year' => $current_year,
//				'month' => $month,
//			),
//		),
//		'fields' => 'ids',  // Optimize query to retrieve post IDs only
//	);
//
//	// Create a new WP_Query instance
//	$query = new \WP_Query($args);
//
//	// Get the post count for the current month
//	$monthly_post_counts[date('F', mktime(0, 0, 0, $month, 1))] = $query->post_count;
//}
//
//// Now $monthly_post_counts contains the total post count for each month from Jan to Dec
//
//var_dump($monthly_post_counts);




// Get the current month and year
//$current_month = date('n');
//$current_year = date('Y');
//
//// Get the current date
//$current_date = date('Y-m-d');
//
//// Calculate yesterday's date
//$yesterday = date('Y-m-d', strtotime('-1 day', strtotime($current_date)));
//
//// Initialize an array to store post counts of yesterday and today
//$post_counts = array(
//	'yesterday' => 0,
//	'today' => 0,
//);
//
//// Initialize an array to store daily post counts
//$daily_post_counts = array();
//
//// Initialize an array to store monthly post counts
//$day_counts_for_month = array();
//
//// Loop through each month of the year (from January to December)
//for ( $month = 1; $month <= 12; $month++ ) {
//	// Get the total number of days in the current month
//	$total_days = cal_days_in_month(CAL_GREGORIAN, $month, $current_year);
//	echo "Month: $month, Total Days: $total_days<br>";
//
//	// Loop through each day of the month
//	for ($day = 1; $day <= $total_days; $day++) {
//		// Format the date in 'Y-m-d' format
//		$date = sprintf('%d-%02d-%02d', $current_year, $current_month, $day);
//
//		// WP_Query arguments to count posts created on a specific day
//		$args = array(
//			'post_type' => 'shop_coupon',      // Replace with your post type if needed
//			'post_status' => 'publish', // Retrieve only published posts
//			'date_query' => array(
//				array(
//					'year' => $current_year,
//					'month' => $current_month,
//					'day' => $day,
//				),
//			),
//			'fields' => 'ids', // Optimize query to retrieve post IDs only
//		);
//
//		// Create a new WP_Query instance
//		$query = new \WP_Query($args);
//
//		// Store the post count for the current day
//		$daily_post_counts[$date] = $query->post_count;
//
//		// Check if the date matches yesterday or today and update counts accordingly
//		if ($date === $yesterday) {
//			$post_counts['yesterday'] = $query->post_count;
//		} elseif ($date === $current_date) {
//			$post_counts['today'] = $query->post_count;
//		}
//
//		// Now you have post counts for yesterday, today, and other days
//		$yesterday_count = $post_counts['yesterday'];
//		$today_count = $post_counts['today'];
//
//		$monthly_post_counts = $query->post_count;
//
//		$day_counts_for_month[$day] = $monthly_post_counts;
//	}
//}

//
//// Get the current date
//$current_date = date('Y-m-d');
//
//// Get the coupons
//$args = array(
//	'post_type' => 'shop_coupon', // Replace 'coupon' with your custom post type name
//	'posts_per_page' => -1, // Get all coupons
//);
//
//$coupons = new WP_Query($args);
//
//// Initialize a counter for active coupons
//$active_coupons_count = 0;
//
//// Loop through the coupons
//if ($coupons->have_posts()) {
//	while ($coupons->have_posts()) {
//		$coupons->the_post();
//
//		// Get the expiry date for the current coupon
//		$expiry_date = get_post_meta(get_the_ID(), 'expiry_date', true);
//
//		// Check if the expiry date matches today's date (active) or is in the past (expired)
//		if ($expiry_date != $current_date) {
//			$active_coupons_count++;
//		}
//	}
//
//	// Reset post data
//	wp_reset_postdata();
//}



// Calculate the start date of the current week (Sunday)
//$current_date = date('Y-m-d');
//$week_start = date('Y-m-d', strtotime('last Sunday', strtotime($current_date)));
//
//// Initialize an array to store counts of active and expired coupons for each day of the current week
//$daily_coupon_counts_current_week = array();
//
//// Define an array to map day numbers to day names
//$day_names = array(
//	0 => 'Sun',
//	1 => 'Mon',
//	2 => 'Tue',
//	3 => 'Wed',
//	4 => 'Thu',
//	5 => 'Fri',
//	6 => 'Sat',
//);
//
//// Loop through each day of the current week (from Sunday to Saturday)
//foreach ($day_names as $day_index => $day_name) {
//	// Calculate the date for the current day in 'Y-m-d' format
//	$day_date = date('Y-m-d', strtotime("+$day_index days", strtotime($week_start)));
//
//	// Get counts of active and expired coupons for the current day
//	$args = array(
//		'post_type' => 'shop_coupon',
//		'post_status' => 'publish',
//		'posts_per_page' => -1, // Retrieve all coupons
//		'meta_query' => array(
//			'relation' => 'AND',
//			array(
//				'key' => 'expiry_date',
//				'value' => $day_date,
//				'compare' => '==', // Expires on or after the current day
//				'type' => 'DATE',
//			),
//		),
//	);
//
//	$active_coupons_query = new WP_Query($args);
//
//	$args['meta_query'][0]['compare'] = '<'; // Expires before the current day
//	$expired_coupons_query = new WP_Query($args);
//
//	// Store counts of active and expired coupons for the current day
//	$daily_coupon_counts_current_week[$day_name]['active'] = $active_coupons_query->found_posts;
//	$daily_coupon_counts_current_week[$day_name]['expired'] = $expired_coupons_query->found_posts;
//}
//
//// Output counts of active and expired coupons for each day of the week
//foreach ($day_names as $day_name) {
//	echo "Total active coupons for {$day_name}: {$daily_coupon_counts_current_week[$day_name]['active']}\n";
//	echo "Total expired coupons for {$day_name}: {$daily_coupon_counts_current_week[$day_name]['expired']}\n";
//}

//// Get the current date
//$current_date = date('Y-m-d');
//
//// Calculate yesterday's date
//$yesterday = date('Y-m-d', strtotime('-1 day', strtotime($current_date)));
//
//// Initialize a variable to store the total count of redeemed coupons for yesterday
//$total_redeemed_coupons_yesterday = 0;
//
//// WP_Query arguments to count redeemed coupons
//$args = array(
//	'post_type' => 'shop_order',  // WooCommerce orders
//	'post_status' => array('wc-completed', 'wc-processing'),  // Orders in completed and processing status
//	'date_query' => array(
//		array(
//			'year' => date('Y', strtotime($yesterday)),
//			'month' => date('n', strtotime($yesterday)),
//			'day' => date('j', strtotime($yesterday)),
//		),
//	),
//);
//
//// Create a new WP_Query instance
//$query = new WP_Query($args);
//
//// Loop through the orders to count redeemed coupons
//if ($query->have_posts()) {
//	while ($query->have_posts()) {
//		$query->the_post();
//
//		// Get order ID
//		$order_id = get_the_ID();
//
//		// Get coupons used in the order
//		$order = wc_get_order($order_id);
//		$coupons = $order->get_used_coupons();
//
//		// Check if coupons were used in this order
//		if (!empty($coupons)) {
//			$total_redeemed_coupons_yesterday += count($coupons);
//		}
//	}
//}
//
//// Reset post data
//wp_reset_postdata();
//
//// Now, $total_redeemed_coupons_yesterday contains the total count of coupons redeemed yesterday
//echo $total_redeemed_coupons_yesterday;





