<?php
namespace HexCoupon\App\Controllers;

use HexCoupon\App\Core\Lib\SingleTon;
use Kathamo\Framework\Lib\Controller;
use CodesVault\Howdyqb\DB;

class AjaxApiController extends Controller
{
	use SingleTon;

	private $base_url = 'hexcoupon/v1/';

	/**
	 * Register hooks callback
	 *
	 * @return void
	 */
	public function register()
	{
		add_action( 'wp_ajax_all_combined_data', [ $this, 'all_combined_data' ] );
		add_action( 'wp_ajax_coupon_data', [ $this, 'total_coupon_created_and_redeemed' ] );
		add_action( 'wp_ajax_get_additional_data', [ $this, 'get_additional_data'] );
		add_action( 'wp_ajax_full_coupon_creation_data', [ $this, 'today_yesterday_coupon_created'] );
		add_action( 'wp_ajax_todayActiveExpiredCoupon', [ $this, 'todayYesterdayActiveExpiredCoupon'] );
		add_action( 'wp_ajax_todayRedeemedCoupon', [ $this, 'todayRedeemedCoupon'] );
		add_action( 'wp_ajax_yesterdayRedeemedCoupon', [ $this, 'yesterdayRedeemedCoupon'] );
		add_action( 'wp_ajax_weekly_coupon_creation_data', [ $this, 'weekly_coupon_creation_data'] );
		add_action( 'wp_ajax_weekly_coupon_active_expired_data', [ $this, 'weekly_coupon_active_expired_data'] );
		add_action( 'wp_ajax_weeklyCouponRedeemedData', [ $this, 'weeklyCouponRedeemedData'] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method all_combined_data
	 * @return void
	 * Get all data in a combined place.
	 */
	public function all_combined_data()
	{
		$total_coupon_created_and_redeemed = $this->total_coupon_created_and_redeemed();

		$get_additional_date = $this->get_additional_data();

		$full_coupon_creation_data = $this->today_yesterday_coupon_created();

		$today_coupon_redeemed = $this->todayRedeemedCoupon();

		$today_yesterday_active_expired_coupon = $this->todayYesterdayActiveExpiredCoupon();

		$yesterday_redeemed_coupon = $this->yesterdayRedeemedCoupon();

		$weekly_coupon_creation_data = $this->weekly_coupon_creation_data();

		$weekly_coupon_redeemed_data = $this->weeklyCouponRedeemedData();

		$weekly_coupon_active_expired_data = $this->weekly_coupon_active_expired_data();

		// Check the nonce and action
		if ( $this->verify_nonce() ) {
			// Nonce is valid, proceed with your code
			wp_send_json( [
				// Your response data here
				'msg' => 'hello',
				'type' => 'success',
				'created' => $total_coupon_created_and_redeemed[0],
				'redeemedAmount' => $total_coupon_created_and_redeemed[1],
				'active' => $get_additional_date[0],
				'expired' => $get_additional_date[1],
				'redeemed' => $get_additional_date[2],
				'sharableUrlPost' => $get_additional_date[3],
				'bogoCoupon' => $get_additional_date[4],
				'geographicRestriction' => $get_additional_date[5],

				'todayCouponCreated' => $full_coupon_creation_data['today'],
				'todayRedeemedCoupon' => $today_coupon_redeemed,
				'todayActiveCoupons' => $today_yesterday_active_expired_coupon[0],
				'todayExpiredCoupons' => $today_yesterday_active_expired_coupon[1],

				'yesterdayCouponCreated' => $full_coupon_creation_data['yesterday'],
				'yesterdayActiveCoupons' => $today_yesterday_active_expired_coupon[2],
				'yesterdayExpiredCoupons' => $today_yesterday_active_expired_coupon[3],
				'yesterdayRedeemedCoupon' => $yesterday_redeemed_coupon,

				'weeklyCouponCreated' => $weekly_coupon_creation_data,
				'weeklyCouponRedeemed' => $weekly_coupon_redeemed_data,
				'weeklyActiveCoupon' => $weekly_coupon_active_expired_data[0],
				'weeklyExpiredCoupon' => $weekly_coupon_active_expired_data[1],
			], 200);
		} else {
			// Nonce verification failed, handle the error
			wp_send_json( [
				'error' => 'Nonce verification failed',
			], 403); // 403 Forbidden status code
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method weeklyCouponRedeemedData
	 * @return array
	 * Get all data in a combined place.
	 */
	public function weeklyCouponRedeemedData()
	{
		// Get the current date
		$current_date = date( 'Y-m-d' );

		// Calculate the start and end dates of the current week
		$week_start = date( 'Y-m-d', strtotime( 'last Sunday', strtotime( $current_date ) ) );
		$week_end = date( 'Y-m-d', strtotime( 'this Saturday', strtotime( $current_date ) ) );

		// Initialize an array to store the coupon counts for each day of the week
		$coupon_counts = [
			'Sun' => 0,
			'Mon' => 0,
			'Tue' => 0,
			'Wed' => 0,
			'Thu' => 0,
			'Fri' => 0,
			'Sat' => 0,
		];

		// Loop through the days of the week
		for ( $i = 0; $i < 7; $i++ ) {
			$target_date = date( 'Y-m-d', strtotime( "+$i days", strtotime( $week_start ) ) );

			// WP_Query arguments to count redeemed coupons for the target date
			$args = [
				'post_type' => 'shop_order',  // WooCommerce orders
				'post_status' => ['wc-completed', 'wc-processing'],  // Orders in completed and processing status
				'date_query' => [
					[
						'year' => date( 'Y', strtotime( $target_date ) ),
						'month' => date( 'n', strtotime( $target_date ) ),
						'day' => date( 'j', strtotime( $target_date ) ),
					],
				],
			];

			// Create a new WP_Query instance
			$query = new \WP_Query( $args );

			// Initialize the count for this day
			$total_redeemed_coupons = 0;

			// Loop through the orders to count redeemed coupons for the day
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					// Get order ID
					$order_id = get_the_ID();

					// Get coupons used in the order
					$order = wc_get_order($order_id);
					$coupons = $order->get_coupon_codes();

					// Check if coupons were used in this order
					if ( !empty( $coupons ) ) {
						$total_redeemed_coupons += count( $coupons );
					}
				}
			}

			// Reset post data
			wp_reset_postdata();

			// Store the count for this day in the array
			$day_of_week = date( 'D', strtotime( $target_date ) );
			$coupon_counts[$day_of_week] = $total_redeemed_coupons;
		}

		return $coupon_counts;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method yesterdayRedeemedCoupon
	 * @return int
	 * Show total number of redeemed coupon for yesterday.
	 */
	public function yesterdayRedeemedCoupon()
	{
		// Get the current date
		$current_date = date( 'Y-m-d' );

		// Calculate yesterday's date
		$yesterday = date( 'Y-m-d', strtotime( '-1 day', strtotime( $current_date ) ) );

		// Initialize a variable to store the total count of redeemed coupons for yesterday
		$total_redeemed_coupons_yesterday = 0;

		// WP_Query arguments to count redeemed coupons
		$args = [
			'post_type' => 'shop_order',  // WooCommerce orders
			'post_status' => [ 'wc-completed', 'wc-processing' ],  // Orders in completed and processing status
			'date_query' => [
				[
					'year' => date( 'Y', strtotime( $yesterday ) ),
					'month' => date( 'n', strtotime( $yesterday ) ),
					'day' => date( 'j', strtotime( $yesterday ) ),
				],
			],
		];

		// Create a new WP_Query instance
		$query = new \WP_Query( $args );

		// Loop through the orders to count redeemed coupons
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				// Get order ID
				$order_id = get_the_ID();

				// Get coupons used in the order
				$order = wc_get_order( $order_id );
				$coupons = $order->get_coupon_codes();

				// Check if coupons were used in this order
				if ( ! empty( $coupons ) ) {
					$total_redeemed_coupons_yesterday += count( $coupons );
				}
			}
		}

		// Reset post data
		wp_reset_postdata();

		return $total_redeemed_coupons_yesterday;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method yesterdayRedeemedCoupon
	 * @return int
	 * Show total number of redeemed coupon for today.
	 */
	public function todayRedeemedCoupon()
	{
		// Get the current date
		$current_date = date( 'Y-m-d' );

		// Initialize a variable to store the total count of redeemed coupons for yesterday
		$total_redeemed_coupons_today = 0;

		// WP_Query arguments to count redeemed coupons
		$args = [
			'post_type' => 'shop_order',  // WooCommerce orders
			'post_status' => [ 'wc-completed', 'wc-processing' ],  // Orders in completed and processing status
			'date_query' => [
				[
					'year' => date( 'Y', strtotime( $current_date ) ),
					'month' => date( 'n', strtotime( $current_date ) ),
					'day' => date( 'j', strtotime( $current_date ) ),
				],
			],
		];

		// Create a new WP_Query instance
		$query = new \WP_Query( $args );

		// Loop through the orders to count redeemed coupons
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				// Get order ID
				$order_id = get_the_ID();

				// Get coupons used in the order
				$order = wc_get_order( $order_id );
				$coupons = $order->get_coupon_codes();

				// Check if coupons were used in this order
				if ( ! empty( $coupons ) ) {
					$total_redeemed_coupons_today += count( $coupons );
				}
			}
		}

		// Reset post data
		wp_reset_postdata();

		return $total_redeemed_coupons_today;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method todayYesterdayActiveExpiredCoupon
	 * @return array
	 * Show all active and expired coupon of today.
	 */
	public function todayYesterdayActiveExpiredCoupon()
	{
		// Get the current date
		$current_date = date('Y-m-d');
		$current_date = strtotime( $current_date );

		// Calculate yesterday's date
		$yesterday = date('Y-m-d', strtotime('-1 day', strtotime($current_date)));

		// Get the coupons
		$args = [
			'post_type' => 'shop_coupon', // Replace 'coupon' with your custom post type name
			'post_status' => 'publish',
			'posts_per_page' => -1, // Get all coupons
		];

		$coupons = new \WP_Query($args);

		// Initialize counters for today active and expired coupons
		$today_active_coupons = 0;
		$today_expired_coupons = 0;

		// Initialize counters for yesterday active and expired coupons
		$yesterday_active_coupons = 0;
		$yesterday_expired_coupons = 0;

		// Loop through the coupons
		if ( $coupons->have_posts() ) {
			while ( $coupons->have_posts() ) {
				$coupons->the_post();

				// Get the expiry date for the current coupon
				$expiry_date = get_post_meta( get_the_ID(), 'date_expires', true );

				// Check if the expiry date matches today's date (active) or is in the past (expired)
				if ( ! empty( (int)$expiry_date ) && (int)$expiry_date < $current_date ) {
					$today_expired_coupons++;
				}
				else {
					$today_active_coupons++;
				}

				if ( $expiry_date != $yesterday ) {
					$yesterday_active_coupons++;
				}
				else {
					$yesterday_expired_coupons++;
				}
			}

			// Reset post data
			wp_reset_postdata();
		}

		$final_array = [ $today_active_coupons, $today_expired_coupons, $yesterday_active_coupons, $yesterday_expired_coupons ];

		return $final_array;

	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method total_coupon_created_and_redeemed
	 * @return array
	 * Show all the created and redeemed coupon total count.
	 */
	public function total_coupon_created_and_redeemed()
	{
		global $wpdb;

		$post_type = 'shop_coupon';
		$post_status = 'publish';

		$query = $wpdb->prepare(
			"SELECT COUNT(ID) as count
					FROM {$wpdb->prefix}posts
					WHERE post_type = %s
					AND post_status = %s",
					$post_type,
					$post_status,
		);
		$result = (int)$wpdb->get_var( $query );

		// Initialize the total redeemed coupon value
		$total_redeemed_value = 0;

		// Query all WooCommerce orders
		$orders = wc_get_orders( [
			'status' => [ 'completed', 'processing' ],
		] );

		// Loop through the orders
		foreach ( $orders as $order ) {
			$discount_amount = (float)$order->get_discount_total();

			// Add the discount to the total redeemed value
			$total_redeemed_value += $discount_amount;
		}

		$final_array = [ $result, $total_redeemed_value ];

		return $final_array;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method weekly_coupon_creation_data
	 * @return array
	 * Show all the coupon creation data of a week.
	 */
	public function weekly_coupon_creation_data()
	{
		// Get the current date
		$current_date = date('Y-m-d');

		// Calculate the start date of the current week (Sunday)
		$week_start = date('Y-m-d', strtotime('last Sunday', strtotime($current_date)));

		// Initialize an array to store daily post counts for the current week
		$daily_post_counts_current_week = [];

		// Define an array to map day numbers to day names
		$day_names = [
			0 => 'Sun',
			1 => 'Mon',
			2 => 'Tue',
			3 => 'Wed',
			4 => 'Thu',
			5 => 'Fri',
			6 => 'Sat',
		];

		// Loop through each day of the current week (from Sunday to Saturday)
		for ( $i = 0; $i < 7; $i++ ) {
			// Calculate the date for the current day in 'Y-m-d' format
			$day_date = date('Y-m-d', strtotime("+$i days", strtotime($week_start)));

			// Get the day name for the current day
			$day_name = $day_names[date('w', strtotime($day_date))];

			// WP_Query arguments to count posts created on the current day
			$args = [
				'post_type' => 'shop_coupon', // Replace with your post type if needed
				'post_status' => 'publish',    // Retrieve only published posts
				'date_query' => [
					[
						'year' => date('Y', strtotime($day_date)),
						'month' => date('m', strtotime($day_date)),
						'day' => date('d', strtotime($day_date)),
					],
				],
				'fields' => 'ids', // Optimize query to retrieve post IDs only
			];

			// Create a new WP_Query instance
			$query = new \WP_Query($args);

			// Store the post count for the current day in the array
			$daily_post_counts_current_week[$day_name] = $query->post_count;

			$final_array = [];

			foreach ( $daily_post_counts_current_week as $value ) {
				$final_array[] = $value;
			}
		}

		return $final_array;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method weekly_coupon_active_data
	 * @return array
	 * Show all active and expired coupon for the current week.
	 */
	public function weekly_coupon_active_expired_data()
	{
		$current_date = date( 'Y-m-d' );
		$week_start = date( 'Y-m-d', strtotime( 'last Sunday', strtotime( $current_date ) ) );
		$week_end = date( 'Y-m-d', strtotime( 'this Saturday', strtotime( $current_date ) ) );

		// Initialize an array to store the count of active coupons for each day
		$active_coupon_count_by_day = [
			'Sun' => 0,
			'Mon' => 0,
			'Tue' => 0,
			'Wed' => 0,
			'Thu' => 0,
			'Fri' => 0,
			'Sat' => 0,
		];
		$expired_coupon_count_by_day = [
			'Sun' => 0,
			'Mon' => 0,
			'Tue' => 0,
			'Wed' => 0,
			'Thu' => 0,
			'Fri' => 0,
			'Sat' => 0,
		];

		$args = array(
			'post_type' => 'shop_coupon',
			'posts_per_page' => -1,
		);

		$loop = new \WP_Query($args);

		while ( $loop->have_posts() ) : $loop->the_post();

			$title = get_the_title();

			// Instantiate the WC_Coupon object with the coupon code
			$coupon_code = $title;
			$coupon = new \WC_Coupon($coupon_code);

			// Check if the coupon exists
			if ( $coupon->get_id() > 0 ) {
				// Retrieve coupon data
				$expiry_date = $coupon->get_date_expires();

				// Separate the date and time portion
				$new_date = !empty( $expiry_date ) ? explode( 'T', $expiry_date ) : '';

				$final_date = '';

				if ( !empty( $new_date[0] ) ) {
					$final_date = $new_date[0]; // Convert to Unix timestamp
				}

				// Loop through each day of the current week and compare expiry date
				for ( $day = strtotime( $week_start ); $day <= strtotime( $week_end ); $day = strtotime( '+1 day', $day ) ) {
					$day_name = date('D', $day);

					if ( strtotime( $final_date ) <= $day && $final_date <= strtotime( '+1 day', $day ) ) {
						$active_coupon_count_by_day[$day_name]++;
					}
					else {
						$expired_coupon_count_by_day[$day_name]++;
					}
				}
			}

		endwhile;

		wp_reset_postdata();

		$final_array = [ $active_coupon_count_by_day, $expired_coupon_count_by_day ];

		return $final_array;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method get_additional_data
	 * @return mixed
	 * Show all the active, expired, redeemed, sharable url coupon, bogo coupon, and geographically restricted coupon of all time.
	 */
	public function get_additional_data()
	{
		// Get the current date in the format 'Y-m-d'
		$current_time = time();

		// Calculate the current week number
		$current_week_number = date('W', $current_time);

		// Initialize an array to store daily counts for the current week
		$daily_counts_current_week = [];

		// Query the database to get all coupon posts and their expiration dates
		$coupon_query = new \WP_Query( [
			'post_type' => 'shop_coupon', // WooCommerce coupon post type
			'posts_per_page' => -1, // Retrieve all coupons
			'post_status' => 'publish' // Retrieve only the published post
		] );

		// Initialize counters for active and expired coupons
		$active_coupons = 0;
		$expired_coupons = 0;
		$total_redeemed = 0;
		$sharable_url_post_count = 0;
		$bogo_coupon_count = 0;
		$geographic_restriction_count = 0;

		// Loop through the coupon posts
		while ( $coupon_query->have_posts() ) {
			$coupon_query->the_post();

			// get coupon code of every coupon
			$coupon_code = wc_get_coupon_code_by_id( get_the_ID() );

			$wc_coupon_data = new \WC_Coupon( $coupon_code );

			// get usage count of every coupon, means get the no of redeemed coupon
			$count = $wc_coupon_data->get_usage_count();

			// calculate the total redeemed no
			$total_redeemed += $count;

			// Get the coupon's expiration date from post meta
			$expiry_date = get_post_meta( get_the_ID(), 'expiry_date', true );

			$sharable_url_post = get_post_meta( get_the_ID(), 'sharable_url_coupon', true );

			$apply_automatic_coupon_by_url = ! empty( $sharable_url_post['apply_automatic_coupon_by_url'] ) ? $sharable_url_post['apply_automatic_coupon_by_url'] : '';

			$discount_type = get_post_meta( get_the_ID(), 'discount_type', true );

			$geographic_restriction = get_post_meta( get_the_ID(), 'geographic_restriction', true );


			if ( ! empty( $geographic_restriction['apply_geographic_restriction'] ) && 'restrict_by_shipping_zones' == $geographic_restriction['apply_geographic_restriction'] || ! empty( $geographic_restriction['apply_geographic_restriction'] ) && 'restrict_by_countries' == $geographic_restriction['apply_geographic_restriction'] )  $geographic_restriction_count++;

			if ( 'buy_x_get_x_bogo' === $discount_type ) $bogo_coupon_count++;

			if ( ! empty( $apply_automatic_coupon_by_url ) && 'yes' === $apply_automatic_coupon_by_url ) {
				$sharable_url_post_count++;
			}

			// Check if the coupon has an expiry date
			if ( ! empty( $expiry_date ) ) {
				// Compare the expiry date with the current date
				$expiry_timestamp = strtotime( $expiry_date );

				// Compare the expiry date with the current date
				if ( strtotime( $expiry_date ) <= $current_time ) {
					// Coupon is active
					$expired_coupons++;
				}

				// Check if the expiry date falls within the current week
				if ( date('W', $expiry_timestamp) == $current_week_number ) {
					// Coupon is within the current week

					// Calculate the day of the week for the expiry date (0 = Sunday, 6 = Saturday)
					$day_of_week = date('w', $expiry_timestamp);

					// Store the count in the corresponding day of the week
					if ( !isset( $daily_counts_current_week[$day_of_week] ) ) {
						$daily_counts_current_week[$day_of_week] = [
							'active' => 0,
							'expired' => 0,
						];
					}

					if ( $expiry_timestamp >= $current_time ) {
						// Coupon is active
						$daily_counts_current_week[$day_of_week]['active']++;
					} else {
						// Coupon has expired
						$daily_counts_current_week[$day_of_week]['expired']++;
					}
				}
			} else {
				$active_coupons++;
			}

			// Create an array with daily counts for the current week
			$daily_counts_array = [];
			// Initialize daily counts for all days of the week
			for ( $day = 0; $day <= 6; $day++ ) {
				$daily_counts_array[$day] = [
					'active' => 0,
					'expired' => 0,
				];
			}

			// Merge the daily counts for the current week into the array
			foreach ( $daily_counts_current_week as $day_of_week => $counts ) {
				$daily_counts_array[$day_of_week] = $counts;
			}
		}

		// Reset post data
		wp_reset_postdata();

		$final_array = [ $active_coupons, $expired_coupons, $total_redeemed, $sharable_url_post_count, $bogo_coupon_count, $geographic_restriction_count ];

		return $final_array;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method today_yesterday_coupon_created
	 * @return array
	 * Show all the coupon creation data for yesterday and today.
	 */
	public function today_yesterday_coupon_created()
	{
		// Define the date range (today and yesterday)
		$today = date( 'Y-m-d' );
		$yesterday = date( 'Y-m-d', strtotime( '-1 day' ) );

		$args = [
			'post_type' => 'shop_coupon', // WooCommerce coupon post type
			'date_query' => [
				'relation' => 'OR',
				[
					'after' => $yesterday,
					'before' => $today,
					'inclusive' => true,
				],
			],
			'posts_per_page' => -1, // Retrieve all matching coupons
		];

		$query = new \WP_Query( $args );

		// Initialize an array to store the counts
		$counts = [
			'today' => 0,
			'yesterday' => 0,
		];

		// Loop through the results to count coupons created today and yesterday
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_date = get_the_date( 'Y-m-d' );

			if ( $post_date === $today ) {
				$counts['today']++;
			} elseif ( $post_date === $yesterday ) {
				$counts['yesterday']++;
			}
		}

		return $counts;
	}

	private function verify_nonce()
	{
		return isset( $_GET['nonce'] ) && !empty( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'],'hexCuponData-react_nonce' ) == 1 ;
	}
}
