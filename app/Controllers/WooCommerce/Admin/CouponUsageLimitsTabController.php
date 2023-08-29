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
	 * @since 1.0.0
	 * @method register
	 * @return void
	 * Register all hooks that are needed.
	 */
	public function register()
	{
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this, 'save_coupon_usage_limit' ] );
		add_action( 'woocommerce_coupon_options_save', [ $this, 'perform_resetting_task_of_usage_limit' ], 10, 2 );
		add_action( 'coupon_periodic_task_hook', [ $this, 'reset_usage_limit'] );
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this, 'delete_meta_value' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method perform_resetting_task_of_usage_limit
	 * @param object $coupon
	 * @param int $post_id
	 * @return void
	 * Set the time length of interval of periodic usage resetting task.
	 */
	public function perform_resetting_task_of_usage_limit( $post_id, $coupon )
	{
		$reset_usage_limit = get_post_meta( $coupon->get_id(), 'reset_usage_limit', true );

		$reset_option_value = get_post_meta( $coupon->get_id(), 'reset_option_value', true );
		$reset_option_value = ! empty( $reset_option_value ) ? $reset_option_value : '';

		$days_count = 0; // Initialize $days_count to a default value

		switch ( $reset_option_value ) {
			case 'annually':
				$days_count = 365 * DAY_IN_SECONDS;
				break;
			case 'monthly':
				$days_count = 30 * DAY_IN_SECONDS;
				break;
			case 'weekly':
				$days_count = 7 * DAY_IN_SECONDS;
				break;
			case 'daily':
				$days_count = DAY_IN_SECONDS;
				break;
		}

		// Check if the reset_usage_limit checkbox is enabled and if days count is set
		if ( ! empty( $reset_usage_limit ) && 'yes' === $reset_usage_limit && $days_count > 0 ) {
			// Check if there are any usage limit given
			if ( $coupon->get_usage_limit() || $coupon->get_usage_limit_per_user() ) {
				// Create a schedule event and hook that with 'coupon_periodic_task_hook' custom hook
				wp_schedule_single_event( time() + $days_count, 'coupon_periodic_task_hook', array( $coupon ) );
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method reset_usage_limit
	 * @param object $coupon
	 * @return void
	 * Reset the usage limit values.
	 */
	public function reset_usage_limit( $coupon )
	{
		$coupon->set_usage_limit( null ); // set the 'usage_limit' value to null

		$coupon->set_usage_limit_per_user( null ); // set 'usage_limit_per_user' value to null

		$coupon->set_limit_usage_to_x_items( null ); // set 'set_limit_usage_to_x_items' value to null

		$coupon->save(); // finally save the value
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method save_coupon_usage_limits_meta_data
	 * @param string $key
	 * @param string $data_type
	 * @param int $coupon_id
	 * @return void
	 * Save the coupon usage restriction meta-fields data.
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

		update_post_meta( $coupon_id, $key, $data[ $key ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method save_coupon_usage_limit
	 * @param $coupon_id
	 * @return void
	 * Save the coupon cart condition.
	 */
	public function save_coupon_usage_limit( $coupon_id )
	{
		// save 'reset_usage_limit' meta-data
		$this->save_coupon_usage_limits_meta_data( 'reset_usage_limit', 'string', $coupon_id );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method delete_meta_value
	 * @param int $coupon_id
	 * @return void
	 * Delete the 'reset_option_value' meta value .
	 */
	public function delete_meta_value( $coupon_id )
	{
		$reset_usage_limit = get_post_meta( $coupon_id, 'reset_usage_limit', true );
		$reset_usage_limit = ! empty( $reset_usage_limit ) ? 'yes' : '';

		// Delete the reset_option_value meta value if reset_usage_limit meta values is not set.
		if ( 'yes' != $reset_usage_limit ) {
			delete_post_meta( $coupon_id, 'reset_option_value' );
		}
	}
}
