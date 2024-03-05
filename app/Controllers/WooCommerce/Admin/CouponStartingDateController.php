<?php

namespace hexcoupon\app\Controllers\WooCommerce\Admin;

use HexCoupon\App\Controllers\BaseController;
use HexCoupon\App\Core\Lib\SingleTon;

class CouponStartingDateController extends BaseController
{
	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return void
	 * @since 1.0.0
	 * Register hook that is needed to validate the coupon according to the starting date.
	 */
	public function register()
	{
		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'apply_coupon' ], 10, 2 );
		add_filter( 'woocommerce_coupon_error', [ $this, 'custom_error_message_for_expiry_date' ], 10, 3 );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method apply_coupon
	 * @return bool
	 * @since 1.0.0
	 * Applying the coupon.
	 */
	public function apply_coupon( $valid, $coupon )
	{
		// get 'apply_coupon_starting_date' return value
		$apply_coupon_starting_date = $this->apply_coupon_starting_date( $valid, $coupon );

		// Finally apply coupon and check the validity
		if ( $apply_coupon_starting_date ) {
			return true;
		}

		return false;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method custom_error_message_for_expiry_date
	 * @return void
	 * @since 1.0.0
	 * Altering the default coupon expiry message with the custom one
	 */
	public function custom_error_message_for_expiry_date( $err_message, $err_code, $coupon ) {
		$coupon_id = $coupon->get_id();
		$custom_expiry_message = get_post_meta( $coupon_id, 'message_for_coupon_expiry_date', true );

		if ( 107 === $err_code && ! empty( $custom_expiry_message ) ) {
			$err_message = sprintf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $custom_expiry_message ) );
		}

		return $err_message;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method apply_coupon_starting_date
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * Apply/validate the coupon starting date.
	 */
	private function apply_coupon_starting_date( $valid, $coupon )
	{
		$current_time = time();

		$coupon_starting_date = get_post_meta( $coupon->get_id(), 'coupon_starting_date', true );
		$coupon_converted_starting_date = strtotime( $coupon_starting_date );

		if ( empty( $coupon_starting_date ) && $current_time >= $coupon_converted_starting_date ) {
			return $valid;
		}
		else {
			// display a custom coupon error message if the coupon is invalid
			add_filter( 'woocommerce_coupon_error', [ $this, 'coupon_starting_date_invalid_error_message' ] , 10, 3 );
		}
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @since 1.0.0
	 * @method coupon_starting_date_invalid_error_message
	 * @param string $err
	 * @param int $err_code
	 * @param object $coupon
	 * @return string
	 * Display custom error message for invalid coupon.
	 */
	public function coupon_starting_date_invalid_error_message( $err, $err_code, $coupon )
	{
		$coupon = new \WC_Coupon( $coupon );

		// Get the ID of the coupon
		$coupon_id = $coupon->get_id();

		$message_for_coupon_starting_date = get_post_meta( $coupon_id, 'message_for_coupon_starting_date', true );

		if ( $err_code === 100 ) {
			if ( ! empty( $message_for_coupon_starting_date ) ) {
				// Change the error message for the INVALID_FILTERED error here
				$err = sprintf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $message_for_coupon_starting_date ) );
			} else {
				$err = esc_html__( 'This coupon has not been started yet. ' );
			}
		}

		return $err;
	}
}
