<?php
namespace HexCoupon\App\Core\Helpers;

use HexCoupon\App\Core\Lib\SingleTon;
use WC_Blocks_Utils;

class StoreCreditPaymentHelpers
{
	use SingleTon;
	private $wpdb;
	private $table_name;

	/**
	 * Constructor to initialize global $wpdb
	 */
	public function __construct()
	{
		global $wpdb;
		$this->wpdb = $wpdb;

		$this->table_name = $wpdb->prefix . 'hex_store_credit_logs';
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method show_log_on_user_end
	 * @return array
	 * Show all store credit log to for the user
	 */
	public function show_log_on_user_end()
	{
		$table_name = $this->table_name;

		$user_id = get_current_user_id();

		$query = $this->wpdb->prepare( "SELECT * FROM $table_name WHERE user_id = %d ORDER BY id DESC LIMIT 10", $user_id );
		$results = $this->wpdb->get_results( $query );

		return $results;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method show_total_remaining_amount
	 * @return float
	 * Show total remaining store credit
	 */
	public function show_total_remaining_amount()
	{
		$table_name = $this->wpdb->prefix . 'hex_store_credit';
		$user_id = get_current_user_id();

		$query = $this->wpdb->prepare( "SELECT amount FROM $table_name WHERE user_id = %d", $user_id );

		$result = $this->wpdb->get_var( $query );

		return $result;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method error_message_for_low_store_credit
	 * @return mixed
	 * Showing error message for low store credit
	 */
	public function error_message_for_low_store_credit()
	{
		global $post;

		// Getting the chosen payment method
		$chosen_payment_method = isset( $_POST['payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) : '';

		function is_checkout_block() {
			return WC_Blocks_Utils::has_block_in_page( wc_get_page_id('checkout'), 'woocommerce/checkout' );
		}


		// Checking if the chosen payment method is store credit
		if ( $chosen_payment_method === 'hex_store_credit' ) {
			$available_store_credit = StoreCreditPaymentHelpers::getInstance()->show_total_remaining_amount();

			// Getting the cart total amount
			$cart_total = WC()->cart->total;

			// Show the error message to the user if store credit amount is not sufficient to make the order.
			if ( $available_store_credit < $cart_total ) {
				$error_message = esc_html__( 'You do not have enough store credit to make this payment', 'hex-coupon-for-woocommerce' );
				wc_add_notice( $error_message, 'error' );
			} else {
				if ( ! is_checkout_block() ) {
					$amount_to_deduct = floatval( WC()->cart->total );
					$this->deduct_store_credit( $amount_to_deduct );
				}
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method deduct_store_credit
	 * @return mixed
	 * Deduct store credit after order payment
	 */
	public function deduct_store_credit( $order_amount )
	{
		$table_name = $this->wpdb->prefix . 'hex_store_credit';

		$user_id = get_current_user_id();

		$amount_to_deduct = $order_amount;

		$this->wpdb->query( $this->wpdb->prepare( "UPDATE $table_name SET amount = amount - %f WHERE user_id = %d", $amount_to_deduct, $user_id ) );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method send_log_for_store_credit_order_purchase
	 * @return mixed
	 * Send log to the log table after order checkout
	 */
	public function send_log_for_store_credit_order_purchase( $order_id, $order_amount )
	{
		$user_id = get_current_user_id();
		$current_order_id = absint( $order_id );
		$amount = floatval( $order_amount );
		$type = boolval( 1 );
		$status = boolval( 0 );

		$data = [
			'user_id' => $user_id,
			'amount' => $amount,
			'order_id' => $current_order_id,
			'type' => $type,
			'status' => $status,
		];

		$data_types = [
			'user_id' => '%d',
			'amount' => '%f',
			'order_id' => '%d',
			'type' => '%d',
			'status' => '%d',
		];

		$this->wpdb->insert( $this->table_name, $data, $data_types );
	}
}
