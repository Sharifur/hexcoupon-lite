<?php
namespace hexcoupon\app\Controllers\WooCommerce\Admin;

use HexCoupon\App\Controllers\BaseController;
use hexcoupon\app\Core\Helpers\ValidationHelper;
use HexCoupon\App\Core\Lib\SingleTon;
use Kathamo\Framework\Lib\Http\Request;

class CouponUsageLimitsTabController extends BaseController {

	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return void
	 * @since 1.0.0
	 * Register hook that is needed to validate the coupon.
	 */
	public function register()
	{
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this, 'save_coupon_usage_limit' ] );
		add_action( 'save_post', [ $this,'reset_usage_limit' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_coupon_usage_limits_meta_data
	 * @param $key
	 * @param $data_type
	 * @param $coupon_id
	 * @return mixed
	 * @since 1.0.0
	 * Save the coupon usage restriction meta-data.
	 */
	private function save_coupon_usage_limits_meta_data( $key, $data_type, $coupon_id )
	{
		$validator = $this->validate( [
			$key => $data_type
		] );
		$error = $validator->error();
		if ( $error ) {

		}
		$data = $validator->getData();

		update_post_meta( $coupon_id, $key, $data[$key] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_coupon_usage_limit
	 * @param $coupon_id
	 * @return mixed
	 * @since 1.0.0
	 * Save the coupon cart condition.
	 */
	public function save_coupon_usage_limit( $coupon_id )
	{
		$this->save_coupon_usage_limits_meta_data( 'reset_usage_limit', 'string', $coupon_id );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method reset_usage_limit
	 * @param int $coupon_id
	 * @return mixed
	 * @since 1.0.0
	 * Save the coupon cart condition.
	 */
	public function reset_usage_limit( $coupon_id )
	{
		$reset_usage_limit = get_post_meta( $coupon_id, 'reset_usage_limit', true );
		$reset_option_value = get_post_meta( $coupon_id, 'reset_option_value', true );

		global $days_count;

		switch ( $reset_option_value ) {
			case 'annually':
				$days_count = 365;
				break;
			case 'monthly':
				$days_count = 30;
				break;
			case 'weekly':
				$days_count = 7;
				break;
			case 'daily':
				$days_count = 1;
				break;
		}

		if ( 'yes' === $reset_usage_limit ) {
			// Schedule the event to delete the post meta after the desired interval
//			$interval = $days_count * DAY_IN_SECONDS;

			add_action( 'init', [ $this, 'custom_delete_coupon_meta_function' ] );

			wp_schedule_single_event( time() + 60, 'init', array( $coupon_id ) );
		}

//		 Hook the function to be executed when the scheduled event runs
//		add_action( 'init', 'custom_delete_coupon_meta_function' );

//		if ( empty( $reset_usage_limit ) ) {
//			delete_post_meta( $coupon_id, 'reset_option_value' );
//			delete_post_meta( $coupon_id, 'usage_limit_per_user' );
//			delete_post_meta( $coupon_id, 'usage_limit' );
//		}
	}

	// Define the function to delete the post meta
	private function custom_delete_coupon_meta_function($coupon_id) {
		add_action('save_post','a');
		function a($coupon_id) {
			delete_post_meta( $coupon_id, 'usage_limit' );
			delete_post_meta( $coupon_id, 'usage_limit_per_user' );
		}

	}
}
