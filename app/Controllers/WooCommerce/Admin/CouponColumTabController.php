<?php

namespace hexcoupon\app\Controllers\WooCommerce\Admin;

use HexCoupon\App\Controllers\BaseController;
use hexcoupon\app\Core\Helpers\ValidationHelper;
use HexCoupon\App\Core\Lib\SingleTon;
use Kathamo\Framework\Lib\Http\Request;
class CouponColumTabController extends BaseController
{
	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return mixed
	 * @since 1.0.0
	 * Add all hooks that are needed to save the coupon meta-data and apply it on products
	 */
	public function register()
	{
		add_action( 'save_post', [ $this, 'save_coupon_all_meta_data' ] );
		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'apply_coupon_meta_data' ], 10, 2 );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_coupon_meta_data
	 * @param $key
	 * @param $data_type
	 * @param $post_id
	 * @return mixed
	 * @since 1.0.0
	 * Save the coupon usage restriction meta-data.
	 */
	private function save_coupon_meta_data( $key, $data_type, $post_id )
	{
		$validator = $this->validate( [
			$key => $data_type
		] );
		$error = $validator->error();
		if ( $error ) {

		}
		$data = $validator->getData();

		update_post_meta( $post_id, $key, $data[$key] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_coupon_role_meta_data
	 * @param int $post_id post ID of Coupon.
	 * @return mixed
	 * @since 1.0.0
	 * Save the coupon user roles custom meta-data when the coupon is updated.
	 */
	public function save_coupon_all_meta_data( $post_id )
	{
		// Save coupon permitted payment method meta field data
		$this->save_coupon_meta_data( 'permitted_payment_methods', 'array', $post_id );

		// Save coupon permitted payment method meta field data
		$this->save_coupon_meta_data( 'permitted_shipping_methods', 'array', $post_id );
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @method apply_selected_payments_method_to_coupon
	 * @param bool $valid
	 * @param object $coupon
	 * @since 1.0.0
	 * @return bool
	 * Apply coupon to user selected payment methods only.
	 */
	private function apply_selected_payments_method_to_coupon( $valid, $coupon )
	{
		$selected_permitted_payment_methods = get_post_meta( $coupon->get_id(), 'permitted_payment_methods', true );
		if ( empty( $selected_permitted_payment_methods ) ) {
			return $valid;
		}

		$current_payment_method = WC()->session->get( 'chosen_payment_method' );

		if ( in_array( $current_payment_method, $selected_permitted_payment_methods ) ) {
			return $valid;
		}

		return false;
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @method apply_selected_shipping_methods_to_coupon
	 * @param bool $valid
	 * @param object $coupon
	 * @since 1.0.0
	 * @return bool
	 * Apply coupon to user selected payment methods only.
	 */
	private function apply_selected_shipping_methods_to_coupon( $valid, $coupon )
	{
		$selected_shipping_methods = get_post_meta( $coupon->get_id(), 'permitted_shipping_methods', true );

		if ( empty( $selected_shipping_methods ) ) {
			return $valid;
		}

		$chosen_shipping_methods = WC()->session->get('chosen_shipping_methods' );

		foreach ( $chosen_shipping_methods as $chosen_shipping_method ) {
			if ( in_array( $chosen_shipping_method, $selected_shipping_methods ) ) {
				return $valid;
			}
		}

		return false;
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @method apply_coupon_meta_data
	 * @param bool $valid
	 * @param object $coupon
	 * @since 1.0.0
	 * @return bool
	 * Apply coupon based on all criteria.
	 */
	public function apply_coupon_meta_data( $valid, $coupon )
	{
		$selectedPaymentMethod = $this->apply_selected_payments_method_to_coupon( $valid, $coupon );
		$selectedShippingMethods = $this->apply_selected_shipping_methods_to_coupon( $valid, $coupon );

		if ( ! $selectedPaymentMethod && ! $selectedShippingMethods ) {
//			echo '## coupon is valid coz there is no payment and shipping method selected. <br>';
			return $valid;
		}

		if ( $selectedPaymentMethod && $selectedShippingMethods ) {
//			echo '## coupon is valid coz payment method is returning '.$selectedPaymentMethod.' and shipping method is returning '.$selectedShippingMethods.' . <br>';
			return $valid;
		}

		return false;
	}

}
