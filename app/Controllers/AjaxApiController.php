<?php
namespace HexCoupon\App\Controllers;

use HexCoupon\App\Core\Lib\SingleTon;
use HexCoupon\App\Core\Helpers\StoreCreditHelpers;
use Kathamo\Framework\Lib\Controller;

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
		add_action( 'wp_ajax_loyalty_program_enable_data', [ $this, 'loyalty_program_enable_data' ] );
		add_action( 'wp_ajax_point_loyalty_program_data', [ $this, 'point_loyalty_program_data' ] );
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
	 * @method loyalty_program_enable_data
	 * @return void
	 * Getting loyalty program enable data
	 */
	public function loyalty_program_enable_data()
	{
		$loyalty_program_enable_settings = $this->loyalty_program_enable_settings();

		// Check the nonce and action
		if ( $this->verify_nonce() ) {
			// Nonce is valid, proceed with your code
			wp_send_json( [
				// Your response data here
				'msg' => 'hello',
				'type' => 'success',
				'loyaltyProgramEnable' => array_map( 'esc_html', $loyalty_program_enable_settings ),
			], 200);
		} else {
			// Nonce verification failed, handle the error
			wp_send_json( [
				'error' => 'Nonce verification failed',
			], 403); // 403 Forbidden status code
		}
	}

	public function point_loyalty_program_data()
	{
		$point_loyalty_program_settings = $this->point_loyalty_program_settings();

		// Check the nonce and action
		if ( $this->verify_nonce() ) {
			// Nonce is valid, proceed with your code
			wp_send_json( [
				// Your response data here
				'msg' => 'hello',
				'type' => 'success',
				'pointLoyaltyProgramData' => $point_loyalty_program_settings,
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
	 * @method all_combined_data
	 * @return void
	 * Get all data in a combined place.
	 */
	public function all_combined_data()
	{
		$total_coupon_created_and_redeemed = $this->total_coupon_created_and_redeemed();

		$get_additional_data = $this->get_additional_data();

		$full_coupon_creation_data = $this->today_yesterday_coupon_created();

		$today_coupon_redeemed = $this->todayRedeemedCoupon();

		$today_yesterday_active_expired_coupon = $this->todayYesterdayActiveExpiredCoupon();

		$yesterday_redeemed_coupon = $this->yesterdayRedeemedCoupon();

		$weekly_coupon_creation_data = $this->weekly_coupon_creation_data();

		$weekly_coupon_redeemed_data = $this->weeklyCouponRedeemedData();

		$weekly_coupon_active_expired_data = $this->weekly_coupon_active_expired_data();

		$store_credit_logs = $this->all_refunded_order_data();

		$store_credit_enable = $this->store_credit_enable_data();

		$current_user_data = $this->current_user_data();

		$all_customers_info = StoreCreditHelpers::getInstance()->get_all_customer_info();

		$total_store_credit_amount = StoreCreditHelpers::getInstance()->get_all_data_from_hex_store_credit_table();

		// Check the nonce and action
		if ( $this->verify_nonce() ) {
			// Nonce is valid, proceed with your code
			wp_send_json( [
				// Your response data here
				'msg' => 'hello',
				'type' => 'success',
				'created' => $total_coupon_created_and_redeemed[0],
				'redeemedAmount' => $total_coupon_created_and_redeemed[1],
				'active' => $get_additional_data[0],
				'expired' => $get_additional_data[1],
				'redeemed' => $get_additional_data[2],
				'sharableUrlPost' => $get_additional_data[3],
				'bogoCoupon' => $get_additional_data[4],
				'geographicRestriction' => $get_additional_data[5],

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

				// store credit data
				'storeCreditLogs' => array_map( function( $item ) {
					return array_map( 'esc_html', $item );
				}, $store_credit_logs ),
				'storeCreditEnable' => array_map( 'esc_html', $store_credit_enable ),
				'adminData' => array_map( 'esc_html', $current_user_data ),
				'allCustomersInfo' => array_map( 'esc_html', $all_customers_info ),
				'totalStoreCreditAmount' => array_map( 'esc_html', $total_store_credit_amount ),
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
	 * @method all_refunded_order_data
	 * @return array
	 * Get all the data of refunded order.
	 */
	public function all_refunded_order_data()
	{
		$store_credit_logs = StoreCreditHelpers::getInstance()->get_all_refunded_order_data();

		return $store_credit_logs;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method store_credit_enable_data
	 * @return array
	 * Get data about enable disable option of store credit
	 */
	public function store_credit_enable_data()
	{
		$store_credit_enable_data = get_option( 'store_credit_enable_data' );

		return $store_credit_enable_data;
	}

	public function loyalty_program_enable_settings()
	{
		$loyalty_program_enable_data = get_option( 'loyalty_program_enable_settings' );

		return $loyalty_program_enable_data;
	}

	public function point_loyalty_program_settings()
	{
		$points_on_purchase = get_option( 'pointsOnPurchase' );
		$points_for_signup = get_option( 'pointsForSignup' );
		$points_for_referral = get_option( 'pointsForReferral' );
		$conversion_rate = get_option( 'conversionRate' );

		$point_loyalty_program_settings = [
			'pointsOnPurchase' => $points_on_purchase,
			'pointsForSignup' => $points_for_signup,
			'pointsForReferral' => $points_for_referral,
			'conversionRate' => $conversion_rate,
		];

		return $point_loyalty_program_settings;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method current_user_data
	 * @return array
	 * Get all user data of current admin.
	 */
	public function current_user_data()
	{
		$current_user_data = StoreCreditHelpers::getInstance()->get_current_user_data();

		return $current_user_data;
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

		$new_array = [];

		foreach ( $coupon_counts as $value ) {
			$new_array[] = (string)$value;
		}

		return $new_array;
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

		// Initialize the values
		$total_redeemed_value = 0;

		$query = "SELECT COUNT(ID) as count
          FROM {$wpdb->prefix}posts
          WHERE post_type = 'shop_coupon'
          AND post_status = 'publish'";
		$result = $wpdb->get_var( $query );

		$total_coupon_created = (int)$result;

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

		$final_array = [ $total_coupon_created, $total_redeemed_value ];

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
				$final_array[] = (string)$value;
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
		$week_start = date( 'Y-m-d', strtotime('last Sunday', strtotime( $current_date ) ) );
		$week_end = date( 'Y-m-d', strtotime( 'this Saturday', strtotime( $current_date ) ) );

		// Initialize indexed arrays to store the count of active and expired coupons for each day
		$active_coupon_count_by_day = [
			0, // Sun
			0, // Mon
			0, // Tue
			0, // Wed
			0, // Thu
			0, // Fri
			0, // Sat
		];
		$expired_coupon_count_by_day = [
			0, // Sun
			0, // Mon
			0, // Tue
			0, // Wed
			0, // Thu
			0, // Fri
			0, // Sat
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
			$coupon = new \WC_Coupon( $coupon_code );

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
				for ( $day = strtotime( $week_start ); $day <= strtotime( $week_end ); $day = strtotime('+1 day', $day)) {
					$day_name = date('D', $day );

					if ( strtotime( $final_date ) <= $day && $final_date <= strtotime( '+1 day', $day ) ) {
						$active_coupon_count_by_day[array_search( $day_name, ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] )]++;
					} else {
						$expired_coupon_count_by_day[array_search( $day_name, ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] )]++;
					}
				}
			}

		endwhile;

		wp_reset_postdata();

		$active_coupon_count_by_day = array_map( 'strval', $active_coupon_count_by_day );
		$expired_coupon_count_by_day = array_map( 'strval', $expired_coupon_count_by_day );

		$final_array = [$active_coupon_count_by_day, $expired_coupon_count_by_day];

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
		// Get current date
		$current_date = new \DateTime();

		// Initialize counts
		$active_coupons_count = 0;
		$expired_coupons_count = 0;
		$redeemed_coupons_count = 0;
		$sharable_url_coupons_count = 0;
		$bogo_coupon_count = 0;
		$geographic_restriction_count = 0;

		// Get all coupons
		$coupons = get_posts( [
			'post_type' => 'shop_coupon',
			'numberposts' => -1,
		] );

		// Loop through each coupon
		foreach ( $coupons as $coupon ) {
			// Create WC_Coupon object
			$coupon_obj = new \WC_Coupon( $coupon->ID );

			// Get expiry date
			$expiry_date = $coupon_obj->get_date_expires();

			$redeemed_coupons_count += $coupon_obj->get_usage_count();


			// Compare expiry date with current date
			if ( $expiry_date ) {
				if ( $expiry_date < $current_date ) {
					$expired_coupons_count++;
				} else {
					$active_coupons_count++;
				}
			} else {
				$active_coupons_count++; // Coupons without expiry dates are considered active
			}

			// Check if the coupon is a sharable URL coupon
			$sharable_url_coupon_meta = get_post_meta( $coupon->ID, 'sharable_url_coupon', true );
			if ( isset( $sharable_url_coupon_meta['apply_automatic_coupon_by_url'] ) && $sharable_url_coupon_meta['apply_automatic_coupon_by_url'] === 'yes' ) {
				$sharable_url_coupons_count++;
			}

			$discount_type = get_post_meta( $coupon->ID, 'discount_type', true );

			if ( 'buy_x_get_x_bogo' === $discount_type ) $bogo_coupon_count++;

			$geographic_restriction = get_post_meta( $coupon->ID, 'geographic_restriction', true );

			if ( ! empty( $geographic_restriction ) )  $geographic_restriction_count++;
		}

		$final_array = [ $active_coupons_count, $expired_coupons_count, $redeemed_coupons_count, $sharable_url_coupons_count, $bogo_coupon_count, $geographic_restriction_count ];

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
