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
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this, 'save_coupon_role_meta_data' ] );
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this, 'save_coupon_payment_methods_meta_data' ] );
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this, 'save_coupon_shipping_methods_meta_data' ] );
		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'apply_selected_user_roles_to_coupon' ], 10, 2 );
		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'apply_selected_payments_method_to_coupon' ], 10, 2 );
		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'apply_selected_shipping_methods_to_coupon' ], 10, 2 );
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
	public function save_coupon_role_meta_data( $post_id )
	{
		//permitted_roles will not be present if no data is selected
		$validator = $this->validate( [
			'permitted_roles' => 'array'
		] );
		$error = $validator->error();
		if ( $error ) {
			?>
			<div class="notice notice-info is-dismissible">
				<p>
					<?php esc_html_e( 'An error occured while saving the value', 'hex-coupon-for-woocommerce' ); ?>
				</p>
			</div>
			<?php
		}
		$data = $validator->getData();

		update_post_meta( $post_id, 'permitted_roles', $data['permitted_roles'] );
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @method save_coupon_payment_methods_meta_data
	 * @param int $post_id post ID of Coupon.
	 * @since 1.0.0
	 * @return mixed
	 * Save the coupon custom payment methods meta-data when the coupon is updated.
	 */
	public function save_coupon_payment_methods_meta_data( $post_id )
	{
		//permitted_payment_methods will not present if no data selected
		$validator = $this->validate( [
			'permitted_payment_methods' => 'array'
		] );
		$error = $validator->error();
		if ( $error ) {
			?>
			<div class="notice notice-info is-dismissible">
				<p>
					<?php esc_html_e( 'An error occured while saving the value', 'hex-coupon-for-woocommerce' ); ?>
				</p>
			</div>
			<?php
		}
		$data = $validator->getData();

		update_post_meta( $post_id, 'permitted_payment_methods', $data['permitted_payment_methods'] );
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @method save_coupon_shipping_methods_meta_data
	 * @param int $post_id post ID of Coupon.
	 * @since 1.0.0
	 * @return mixed
	 * Save the coupon custom shipping methods meta-data when the coupon is updated.
	 */
	public function save_coupon_shipping_methods_meta_data( $post_id )
	{
		//permitted_shipping_methods will not present if no data selected
		$validator = $this->validate( [
			'permitted_shipping_methods' => 'array'
		] );
		$error = $validator->error();
		if ( $error ) {
			?>
			<div class="notice notice-info is-dismissible">
				<p>
					<?php esc_html_e( 'An error occured while saving the value', 'hex-coupon-for-woocommerce' ); ?>
				</p>
			</div>
			<?php
		}
		$data = $validator->getData();

		update_post_meta( $post_id, 'permitted_shipping_methods', $data['permitted_shipping_methods'] );
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @method apply_selected_user_roles_to_coupon
	 * @param bool $valid
	 * @param string $coupon
	 * @since 1.0.0
	 * @return bool
	 * Apply coupon to selected user roles only.
	 */
	function apply_selected_user_roles_to_coupon( $valid, $coupon )
	{
		$selected_roles = get_post_meta( $coupon->get_id(), 'permitted_roles', true );
		if ( empty( $selected_roles ) ) {
			return $valid;
		}

		$user = wp_get_current_user();
		$user_roles = $user->roles;

		foreach ( $user_roles as $user_role ) {
			if ( in_array( $user_role, $selected_roles ) ) {
				return $valid;
			}
		}

		return false;
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @method apply_selected_payments_method_to_coupon
	 * @param bool $valid
	 * @param string $coupon
	 * @since 1.0.0
	 * @return bool
	 * Apply coupon to user selected payment methods only.
	 */
	function apply_selected_payments_method_to_coupon( $valid, $coupon )
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
	 * @param string $coupon
	 * @since 1.0.0
	 * @return bool
	 * Apply coupon to user selected payment methods only.
	 */
	function apply_selected_shipping_methods_to_coupon( $valid, $coupon )
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

}
