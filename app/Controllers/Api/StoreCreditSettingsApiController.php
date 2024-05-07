<?php
namespace HexCoupon\App\Controllers\Api;

use HexCoupon\App\Core\Helpers\StoreCreditHelpers;
use HexCoupon\App\Core\Lib\SingleTon;
use HexCoupon\App\Traits\NonceVerify;
use Kathamo\Framework\Lib\Controller;

class StoreCreditSettingsApiController extends Controller
{

	use SingleTon, NonceVerify;

	/**
	 * Register hooks callback
	 *
	 * @return void
	 */
	public function register()
	{
		add_action( 'admin_post_store_credit_settings_save', [ $this, 'store_credit_settings_save' ] );
		add_action( 'admin_post_store_credit_save', [ $this, 'store_credit_all_save' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method store_credit_settings_save
	 * @return mixed
	 * Saving store credit enable/disable option in the option table
	 */
	public function store_credit_settings_save()
	{
		if ( $this->verify_nonce('POST') ) {
			$formData = json_encode( $_POST );

			$dataArray = json_decode( $formData, true );

			$store_credit_enable_settings = [
				'enable' => rest_sanitize_boolean( $dataArray['enable'] ),
			];

			// Apply filter hook to modify the data array for pro version
			$store_credit_enable_settings = apply_filters( 'store_credit_settings_data', $store_credit_enable_settings, $dataArray );

			update_option( 'store_credit_enable_data', $store_credit_enable_settings ); // saving the value in the option table

			wp_send_json( $_POST );
		} else {
			wp_send_json( [
				'error' => 'Nonce verification failed',
			], 403); // 403 Forbidden status code
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method store_credit_all_save
	 * @return mixed
	 * Storing gift store credit to the credit table, logs table and sending email to the customer
	 */
	public function store_credit_all_save()
	{
		if ( $this->verify_nonce('POST') ) {
			$formData = json_encode( $_POST );
			$order_id = 0;
			$status = 1;

			$dataArray = json_decode( $formData, true );
			$amount = sanitize_text_field( $dataArray['amount'] );
			$admin_id = sanitize_text_field( $dataArray['adminId'] );
			$admin_name = sanitize_text_field( $dataArray['adminName'] );
			$note = sanitize_text_field( $dataArray['note'] );
			$user_ids = $dataArray['userIds'];
			$date = get_the_date( 'D M Y' );

			// Sending gift credit data to the 'hex_store_credit' table
			StoreCreditHelpers::getInstance()->send_store_credit_info( $amount, $user_ids );

			// Sending gift credit data to the 'hex_store_credit_logs' table
			StoreCreditHelpers::getInstance()->hex_store_credit_logs_initial_insertion( $order_id, $user_ids, $admin_id, $admin_name, $amount, $status );

			// Sending email notification to the user for gift credit
			StoreCreditHelpers::getInstance()->send_confirmation_email_for_gift_credit( $user_ids, $amount, $note, $order_id, $date );

			wp_send_json( $_POST );
		}else {
			wp_send_json( [
				'error' => 'Nonce verification failed',
			], 403); // 403 Forbidden status code
		}
	}

}
