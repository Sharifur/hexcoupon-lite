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
		add_action( 'wp_ajax_coupon_data', [ $this, 'total_coupon_created_and_redeemed' ] );
		add_action( 'wp_ajax_get_additional_data', [ $this, 'get_additional_data'] );
		add_action( 'wp_ajax_full_coupon_creation_data', [ $this, 'full_coupon_creation_data'] );
		add_action( 'wp_ajax_weekly_coupon_creation_data', [ $this, 'weekly_coupon_creation_data'] );
		add_action( 'wp_ajax_monthlyCouponCountInYear', [ $this, 'monthlyCouponCountInYear'] );
		add_action( 'wp_ajax_todayActiveExpiredCoupon', [ $this, 'todayYesterdayActiveExpiredCoupon'] );
		add_action( 'wp_ajax_weeklyCouponActiveExpiredCoupon', [ $this, 'weeklyCouponActiveExpiredCoupon'] );
		add_action( 'wp_ajax_yesterdayRedeemedCoupon', [ $this, 'yesterdayRedeemedCoupon'] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method yesterdayRedeemedCoupon
	 * @return void
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
					'year' => date('Y', strtotime($yesterday)),
					'month' => date('n', strtotime($yesterday)),
					'day' => date('j', strtotime($yesterday)),
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

		// Check the nonce and action
		if ( $this->verify_nonce() ) {
			// Nonce is valid, proceed with your code
			wp_send_json( [
				// Your response data here
				'msg' => __('hello'),
				'type' => 'success',
				'yesterdayRedeemedCoupon' => __( $total_redeemed_coupons_yesterday ),

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
	 * @method weeklyCouponActiveExpiredCoupon
	 * @return void
	 * Show all the categories of the product.
	 */
	public function weeklyCouponActiveExpiredCoupon()
	{

	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method todayYesterdayActiveExpiredCoupon
	 * @return void
	 * Show all the categories of the product.
	 */
	public function todayYesterdayActiveExpiredCoupon()
	{
		// Get the current date
		$current_date = date('Y-m-d');

		// Calculate yesterday's date
		$yesterday = date('Y-m-d', strtotime('-1 day', strtotime($current_date)));

		// Get the coupons
		$args = [
			'post_type' => 'shop_coupon', // Replace 'coupon' with your custom post type name
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
				$expiry_date = get_post_meta(get_the_ID(), 'expiry_date', true);

				// Check if the expiry date matches today's date (active) or is in the past (expired)
				if ( $expiry_date != $current_date ) {
					$today_active_coupons++;
				}
				else {
					$today_expired_coupons++;
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

		// Check the nonce and action
		if ( $this->verify_nonce() ) {
			// Nonce is valid, proceed with your code
			wp_send_json( [
				// Your response data here
				'msg' => __('hello'),
				'type' => 'success',
				'todayActiveCoupons' => __( $today_active_coupons ),
				'todayExpiredCoupons' => __( $today_expired_coupons ),
				'yesterdayActiveCoupons' => __( $yesterday_active_coupons ),
				'yesterdayExpiredCoupons' => __( $yesterday_expired_coupons ),

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
	 * @method total_coupon_created_and_redeemed
	 * @return void
	 * Show all the categories of the product.
	 */
	public function total_coupon_created_and_redeemed()
	{
		$result = DB::select('posts.post_status')
			->count('posts.ID', 'posts')
			->from('posts posts')
			->where('posts.post_type', '=', 'shop_coupon')
			->andWhere('posts.post_status', '=', 'publish')
			->get();

		$final_result = ! empty( $result[0]['posts'] ) ? $result[0]['posts'] : '';

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

		// Check the nonce and action
		if ( $this->verify_nonce() ) {
			// Nonce is valid, proceed with your code
			wp_send_json( [
				// Your response data here
				'msg' => __('hello'),
				'type' => 'success',
				'created' => __( $final_result ),
				'redeemedAmount' => __( $total_redeemed_value ),

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
	 * @method weekly_coupon_creation_data
	 * @return void
	 * Show all the categories of the product.
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
		for ($i = 0; $i < 7; $i++) {
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
		}

		// Check the nonce and action
		if ( $this->verify_nonce() ) {
			// Nonce is valid, proceed with your code
			wp_send_json( [
				// Your response data here
				'msg' => __('hello'),
				'type' => 'success',
				'weeklyCouponCreated' => __( $daily_post_counts_current_week ),

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
	 * @method get_additional_data
	 * @return void
	 * Show all the categories of the product.
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

			$discount_type = get_post_meta( get_the_ID(), 'discount_type', true );

			$geographic_restriction = get_post_meta( get_the_ID(), 'geographic_restriction', true );


			if ( ! empty( $geographic_restriction['apply_geographic_restriction'] ) && 'restrict_by_shipping_zones' == $geographic_restriction['apply_geographic_restriction'] || ! empty( $geographic_restriction['apply_geographic_restriction'] ) && 'restrict_by_countries' == $geographic_restriction['apply_geographic_restriction'] )  $geographic_restriction_count++;

			if ( 'buy_x_get_x_bogo' === $discount_type ) $bogo_coupon_count++;

			if ( $sharable_url_post ) {
				$sharable_url_post_count++;
			}

			// Check if the coupon has an expiry date
			if ( ! empty( $expiry_date ) ) {
				// Compare the expiry date with the current date
				$expiry_timestamp = strtotime( $expiry_date );

				// Compare the expiry date with the current date
				if ( strtotime( $expiry_date ) >= $current_time ) {
					// Coupon is active
					$active_coupons++;
				} else {
					// Coupon has expired
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

		// Check the nonce and action
		if ( $this->verify_nonce() ) {
			// Nonce is valid, proceed with your code
			wp_send_json( [
				// Your response data here
				'msg' => __( 'Additional data fetched successfully' ),
				'type' => 'success',
				'active' => __( $active_coupons ),
				'expired' => __( $expired_coupons ),
				'redeemed' => __( $total_redeemed ),
				'sharableUrlPost' => __( $sharable_url_post_count ),
				'bogoCoupon' => __( $bogo_coupon_count ),
				'geographicRestriction' => __( $geographic_restriction_count )
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
	 * @method full_coupon_creation_data
	 * @return void
	 * Show all the categories of the product.
	 */
	public function full_coupon_creation_data()
	{
		// Get the current month and year
		$current_month = date('n');
		$current_year = date('Y');

		// Get the current date
		$current_date = date('Y-m-d');

		// Calculate yesterday's date
		$yesterday = date('Y-m-d', strtotime('-1 day', strtotime($current_date)));

		// Initialize an array to store post counts of yesterday and today
		$post_counts = [
			'yesterday' => 0,
			'today' => 0,
		];

		// Initialize an array to store daily post counts
		$daily_post_counts = [];

		// Initialize an array to store monthly post counts
		$day_counts_for_month = [];

		// Loop through each month of the year (from January to December)
		for ( $month = 1; $month <= 12; $month++ ) {
			// Get the total number of days in the current month
			$total_days = cal_days_in_month(CAL_GREGORIAN, $month, $current_year);

			// Loop through each day of the month
			for ( $day = 1; $day <= $total_days; $day++ ) {
				// Format the date in 'Y-m-d' format
				$date = sprintf('%d-%02d-%02d', $current_year, $current_month, $day);

				// WP_Query arguments to count posts created on a specific day
				$args = [
					'post_type' => 'shop_coupon',      // Replace with your post type if needed
					'post_status' => 'publish', // Retrieve only published posts
					'date_query' => [
						[
							'year' => $current_year,
							'month' => $current_month,
							'day' => $day,
						],
					],
					'fields' => 'ids', // Optimize query to retrieve post IDs only
				];

				// Create a new WP_Query instance
				$query = new \WP_Query($args);

				// Store the post count for the current day
				$daily_post_counts[$date] = $query->post_count;

				// Check if the date matches yesterday or today and update counts accordingly
				if ( $date === $yesterday ) {
					$post_counts['yesterday'] = $query->post_count;
				} elseif ( $date === $current_date ) {
					$post_counts['today'] = $query->post_count;
				}

				// Now you have post counts for yesterday, today, and other days
				$yesterday_count = $post_counts['yesterday'];
				$today_count = $post_counts['today'];

				$monthly_post_counts = $query->post_count;

				$day_counts_for_month[$day] = $monthly_post_counts;
			}

		}

		// Check the nonce and action
		if ( $this->verify_nonce() ) {
			// Nonce is valid, proceed with your code
			wp_send_json( [
				// Your response data here
				'msg' => __( 'Data fetched successfully'),
				'type' => 'success',
				'todayCouponCreated' => __( $today_count) ,
				'yesterdayCouponCreated' => __( $yesterday_count ),
				'dailyCouponCreatedInMonth' => __( $day_counts_for_month ),
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
	 * @method monthlyCouponCountInYear
	 * @return void
	 * Show all the categories of the product.
	 */
	public function monthlyCouponCountInYear()
	{
		$current_year = date('Y');
		// Initialize an array to store monthly post counts
		$monthly_post_counts = [];

		// Loop through each month of the year (from January to December)
		for ( $month = 1; $month <= 12; $month++ ) {
			// WP_Query arguments to count posts created in the current month
			$args = [
				'post_type' => 'shop_coupon',  // Replace with your post type if needed
				'post_status' => 'publish',    // Retrieve only published posts
				'date_query' => [
					[
						'year' => $current_year,
						'month' => $month,
					],
				],
				'fields' => 'ids',  // Optimize query to retrieve post IDs only
			];

			// Create a new WP_Query instance
			$query = new \WP_Query( $args );

			// Get the post count for the current month
			$monthly_post_counts[date('F', mktime(0, 0, 0, $month, 1))] = $query->post_count;
		}

		// Check the nonce and action
		if ( $this->verify_nonce() ) {
			// Nonce is valid, proceed with your code
			wp_send_json( [
				// Your response data here
				'msg' => __( 'Data fetched successfully'),
				'type' => 'success',
				'monthlyCouponCountInYear' => __( $monthly_post_counts ),
			], 200);
		} else {
			// Nonce verification failed, handle the error
			wp_send_json( [
				'error' => 'Nonce verification failed',
			], 403); // 403 Forbidden status code
		}
	}

	private function verify_nonce()
	{
		return isset($_GET['nonce']) && !empty($_GET['nonce']) && wp_verify_nonce($_GET['nonce'],'hexCuponData-react_nonce') == 1 ;
	}
}
