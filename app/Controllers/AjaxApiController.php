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
	public function register() {
		add_action('wp_ajax_coupon_data', [$this, 'total_coupon_created_and_redeemed']);
		add_action('wp_ajax_get_additional_data', [$this, 'get_additional_data']);
		add_action('wp_ajax_get_additional_data', [$this, 'get_coupon_count_in_a_month']);
	}

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
			'status' => array( 'completed', 'processing' ),
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

	public function get_additional_data() {
		// Get the current date in the format 'Y-m-d'
		$current_time = time();

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

		// Check the nonce and action
		if ($this->verify_nonce()) {
			// Nonce is valid, proceed with your code
			wp_send_json([
				// Your response data here
				'msg' => __('Additional data fetched successfully'),
				'type' => __('Success'),
				'active' => __($active_coupons),
				'expired' => __($expired_coupons),
				'redeemed' => __($total_redeemed),
				'sharableUrlPost' => __($sharable_url_post_count),
				'bogoCoupon' => __($bogo_coupon_count),
				'geographicRestriction' => __($geographic_restriction_count)
			], 200);
		} else {
			// Nonce verification failed, handle the error
			wp_send_json([
				'error' => 'Nonce verification failed',
			], 403); // 403 Forbidden status code
		}
	}

	public function get_coupon_count_in_a_month() {
		// Get the current month and year
		$current_month = date('n');
		$current_year = date('Y');

// Get the total number of days in the current month
		$total_days = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);

// Initialize an array to store daily post counts
		$daily_post_counts = array();

// Loop through each day of the month
		for ($day = 1; $day <= $total_days; $day++) {
			// Format the date in 'Y-m-d' format
			$date = sprintf('%d-%02d-%02d', $current_year, $current_month, $day);

			// WP_Query arguments to count posts created on a specific day
			$args = array(
				'post_type' => 'shop_coupon',      // Replace with your post type if needed
				'post_status' => 'publish', // Retrieve only published posts
				'date_query' => array(
					array(
						'year' => $current_year,
						'month' => $current_month,
						'day' => $day,
					),
				),
				'fields' => 'ids', // Optimize query to retrieve post IDs only
			);

			// Create a new WP_Query instance
			$query = new WP_Query($args);

			// Store the post count for the current day
			$daily_post_counts[$date] = $query->post_count;
		}
	}

	private function verify_nonce(){
		return isset($_GET['nonce']) && !empty($_GET['nonce']) && wp_verify_nonce($_GET['nonce'],'hexCuponData-react_nonce') == 1 ;
	}
}
