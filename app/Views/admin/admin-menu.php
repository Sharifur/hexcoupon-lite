<div id="vite-react-sample"></div>
<?php

use CodesVault\Howdyqb\DB;

$result1 = DB::select('posts.post_status')
	->count('posts.ID', 'posts')
	->from('posts posts')
	->where('posts.post_type', '=', 'shop_coupon')
	->andWhere('posts.post_status', '=', 'trash')
	->get();

$result2 = DB::select('posts.post_status')
	->count('posts.ID', 'posts')
	->from('posts posts')
	->where('posts.post_type', '=', 'shop_coupon')
	->andWhere('posts.post_status', '=', 'publish')
	->get();

$final = $result1[0]['posts'] + $result2[0]['posts'];

// Get the current date in the format 'Y-m-d'
$current_time = time();

// Query the database to get all coupon posts and their expiration dates
$coupon_query = new WP_Query( [
	'post_type' => 'shop_coupon', // WooCommerce coupon post type
	'posts_per_page' => -1, // Retrieve all coupons
] );

// Initialize counters for active and expired coupons
$active_coupons = 0;
$expired_coupons = 0;
$total_redeemed = 0;
$sharable_url_post_count = 0;

// Loop through the coupon posts
while ( $coupon_query->have_posts() ) {
	$coupon_query->the_post();

	// get coupon code of every coupon
	$coupon_code = wc_get_coupon_code_by_id( get_the_ID() );

	$wc_coupon_data = new WC_Coupon( $coupon_code );

	// get usage count of every coupon, means get the no of redeemed coupon
	$count = $wc_coupon_data->get_usage_count();

	// calculate the total redeemed no
	$total_redeemed += $count;

	// Get the coupon's expiration date from post meta
	$expiry_date = get_post_meta( get_the_ID(), 'expiry_date', true );

	$sharable_url_post = get_post_meta( get_the_ID(), 'sharable_url_coupon', true );
	if ( $sharable_url_post ) {
		$sharable_url_post_count++;
	}

	// Check if the coupon has an expiry date
	if ( ! empty( $expiry_date ) ) {
		// Compare the expiry date with the current date
		if ( strtotime( $expiry_date ) >= $current_time ) {
			// Coupon is active
			$active_coupons++;
		} else {
			// Coupon has expired
			$expired_coupons++;
		}
	}
}

// Reset post data
wp_reset_postdata();

// Initialize the total redeemed coupon value
$total_redeemed_value = 0;

// Query all WooCommerce orders
$orders = wc_get_orders( [
	'status' => array( 'completed', 'processing' ),
] );

// initialize the variable
$total_redeemed_value = 0;

// Loop through the orders
foreach ( $orders as $order ) {
	$discount_amount = (float)$order->get_discount_total();

	// Add the discount to the total redeemed value
	$total_redeemed_value += $discount_amount;
}

// define ending and starting date of last year
$last_year_starting_date = date( 'Y-01-01', strtotime('-1 year') );
$last_year_ending_date = date( 'Y-12-31', strtotime('-1 year') );

// define ending and starting date of last month
$starting_date_of_last_month = date('Y-m-01', strtotime('last month')) . ' ';
$ending_date_of_last_month = date('Y-m-t', strtotime('last month')) . ' ';

// define ending and starting date of last week
// Get the current date
$current_date = strtotime('now');

// Calculate the starting day of the previous week (10th day of the previous week)
$starting_day_last_week = strtotime('last sunday', strtotime('-6 days', $current_date));

// Calculate the last day of the last week (Saturday)
$last_day_last_week = strtotime('last saturday', $current_date);

// Format the dates as 'Y-m-d'
$starting_date_last_week = date('Y-m-d', $starting_day_last_week);
$ending_date_last_week = date('Y-m-d', $last_day_last_week);

/** last year coupon query **/
$last_year_coupon_query = new WP_Query( [
	'post_type' => 'shop_coupon', // WooCommerce coupon post type
	'post_status' => [ 'publish', 'trash' ],
	'posts_per_page' => -1, // Retrieve all coupons
	'date_query' => [
		[
			'after' => $last_year_starting_date,
			'before' => $last_year_ending_date,
			'inclusive' => true,
		],
	],
] );

$total_posts_last_year = 0;
$total_redeemed_last_year = 0;
$active_coupons_last_year = 0;
$expired_coupons_last_year = 0;

while( $last_year_coupon_query->have_posts() ) {
	$last_year_coupon_query->the_post();

	// get coupon code of every coupon
	$coupon_code = wc_get_coupon_code_by_id( get_the_ID() );

	$wc_coupon_data = new WC_Coupon( $coupon_code );

	// get usage count of every coupon, means get the no of redeemed coupon
	$count = $wc_coupon_data->get_usage_count();

	$total_redeemed_last_year += $count;

	// Get the coupon's expiration date from post meta
	$expiry_date = get_post_meta( get_the_ID(), 'expiry_date', true );

	// Check if the coupon has an expiry date
	if ( ! empty( $expiry_date ) ) {
		// Compare the expiry date with the current date
		if ( strtotime( $expiry_date ) >= $current_time ) {
			// Coupon is active
			$active_coupons_last_year++;
		} else {
			// Coupon has expired
			$expired_coupons_last_year++;
		}
	}

	$total_posts_last_year++;
}

wp_reset_postdata();

/** last month coupon query **/
$last_month_coupon_query = new WP_Query( [
	'post_type' => 'shop_coupon', // WooCommerce coupon post type
	'post_status' => [ 'publish', 'trash' ],
	'posts_per_page' => -1, // Retrieve all coupons
	'date_query' => [
		[
			'after' => $starting_date_of_last_month,
			'before' => $ending_date_of_last_month,
			'inclusive' => true,
		],
	],
] );

$total_posts_last_month = 0;
$total_redeemed_last_month = 0;
$active_coupons_last_month = 0;
$expired_coupons_last_month = 0;

while( $last_month_coupon_query->have_posts() ) {
	$last_month_coupon_query->the_post();

	// get coupon code of every coupon
	$coupon_code = wc_get_coupon_code_by_id( get_the_ID() );

	$wc_coupon_data = new WC_Coupon( $coupon_code );

	// get usage count of every coupon, means get the no of redeemed coupon
	$count = $wc_coupon_data->get_usage_count();

	$total_redeemed_last_month += $count;

	// Get the coupon's expiration date from post meta
	$expiry_date = get_post_meta( get_the_ID(), 'expiry_date', true );

	// Check if the coupon has an expiry date
	if ( ! empty( $expiry_date ) ) {
		// Compare the expiry date with the current date
		if ( strtotime( $expiry_date ) >= $current_time ) {
			// Coupon is active
			$active_coupons_last_month++;
		} else {
			// Coupon has expired
			$expired_coupons_last_month++;
		}
	}

	$total_posts_last_month++;
}

wp_reset_postdata();

/** last week coupon query **/
$last_week_coupon_query = new WP_Query( [
	'post_type' => 'shop_coupon', // WooCommerce coupon post type
	'post_status' => [ 'publish', 'trash' ],
	'posts_per_page' => -1, // Retrieve all coupons
	'date_query' => [
		[
			'after' => $starting_date_last_week,
			'before' => $ending_date_last_week,
			'inclusive' => true,
		],
	],
] );

$total_posts_last_week = 0;
$total_redeemed_last_week = 0;
$active_coupons_last_week = 0;
$expired_coupons_last_week = 0;

while( $last_week_coupon_query->have_posts() ) {
	$last_week_coupon_query->the_post();

	// get coupon code of every coupon
	$coupon_code = wc_get_coupon_code_by_id( get_the_ID() );

	$wc_coupon_data = new WC_Coupon( $coupon_code );

	// get usage count of every coupon, means get the no of redeemed coupon
	$count = $wc_coupon_data->get_usage_count();

	$total_redeemed_last_week += $count;

	// Get the coupon's expiration date from post meta
	$expiry_date = get_post_meta( get_the_ID(), 'expiry_date', true );

	// Check if the coupon has an expiry date
	if ( ! empty( $expiry_date ) ) {
		// Compare the expiry date with the current date
		if ( strtotime( $expiry_date ) >= $current_time ) {
			// Coupon is active
			$active_coupons_last_week++;
		} else {
			// Coupon has expired
			$expired_coupons_last_week++;
		}
	}

	$total_posts_last_week++;
}

wp_reset_postdata();

/** yesterday coupon query **/
$yesterday_coupon_query = new WP_Query( [
	'post_type' => 'shop_coupon', // WooCommerce coupon post type
	'post_status' => [ 'publish', 'trash' ],
	'posts_per_page' => -1, // Retrieve all coupons
	'date_query' => [
		[
			'day' => date('d') - 1,
			'inclusive' => true,
		],
	],
] );

$total_posts_yesterday = 0;
$total_redeemed_yesterday = 0;
$active_coupons_yesterday = 0;
$expired_coupons_yesterday = 0;

while( $last_week_coupon_query->have_posts() ) {
	$last_week_coupon_query->the_post();

	// get coupon code of every coupon
	$coupon_code = wc_get_coupon_code_by_id( get_the_ID() );

	$wc_coupon_data = new WC_Coupon( $coupon_code );

	// get usage count of every coupon, means get the no of redeemed coupon
	$count = $wc_coupon_data->get_usage_count();

	$total_redeemed_yesterday += $count;

	// Get the coupon's expiration date from post meta
	$expiry_date = get_post_meta( get_the_ID(), 'expiry_date', true );

	// Check if the coupon has an expiry date
	if ( ! empty( $expiry_date ) ) {
		// Compare the expiry date with the current date
		if ( strtotime( $expiry_date ) >= $current_time ) {
			// Coupon is active
			$active_coupons_yesterday++;
		} else {
			// Coupon has expired
			$expired_coupons_yesterday++;
		}
	}

	$total_posts_yesterday++;
}

wp_reset_postdata();

/** today's coupon query **/
$today_coupon_query = new WP_Query( [
	'post_type' => 'shop_coupon', // WooCommerce coupon post type
	'post_status' => [ 'publish', 'trash' ],
	'posts_per_page' => -1, // Retrieve all coupons
	'date_query' => [
		[
			'day' => date('d'),
			'inclusive' => true,
		],
	],
] );

$total_posts_today = 0;
$total_redeemed_today = 0;
$active_coupons_today = 0;
$expired_coupons_today = 0;

while( $today_coupon_query->have_posts() ) {
	$today_coupon_query->the_post();

	// get coupon code of every coupon
	$coupon_code = wc_get_coupon_code_by_id( get_the_ID() );

	$wc_coupon_data = new WC_Coupon( $coupon_code );

	// get usage count of every coupon, means get the no of redeemed coupon
	$count = $wc_coupon_data->get_usage_count();

	$total_redeemed_today += $count;

	// Get the coupon's expiration date from post meta
	$expiry_date = get_post_meta( get_the_ID(), 'expiry_date', true );

	// Check if the coupon has an expiry date
	if ( ! empty( $expiry_date ) ) {
		// Compare the expiry date with the current date
		if ( strtotime( $expiry_date ) >= $current_time ) {
			// Coupon is active
			$active_coupons_today++;
		} else {
			// Coupon has expired
			$expired_coupons_today++;
		}
	}

	$total_posts_today++;
}

wp_reset_postdata();

?>


