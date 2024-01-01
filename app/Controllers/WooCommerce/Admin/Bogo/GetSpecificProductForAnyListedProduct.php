<?php
namespace hexcoupon\app\Controllers\WooCommerce\Admin\Bogo;

use HexCoupon\App\Controllers\BaseController;
use hexcoupon\app\Controllers\WooCommerce\Admin\CouponGeneralTabController;
use HexCoupon\App\Core\Lib\SingleTon;

class GetSpecificProductForAnyListedProduct extends BaseController
{
	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method customer_gets_a_specific_product_against_any_product_listed_below
	 * @return mixed
	 * Customer gets a specific product against any product listed below
	 */
	public function customer_gets_a_specific_product_against_any_product_listed_below( $customer_purchases, $customer_gets_as_free, $wc_cart, $main_product_id, $coupon_id, $free_item_id )
	{
		$hexcoupon_bogo_instance = HexcouponBogoController::getInstance();

		if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) )
			return;

		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
			return;

		if ( 'a_specific_product' === $customer_gets_as_free && 'any_products_listed_below' === $customer_purchases ) {
			$main_product_in_cart = false;
			$quantities = $wc_cart->get_cart_item_quantities();

			foreach ( $main_product_id as $main_single_id ) {
				$main_single_key = $wc_cart->generate_cart_id( $main_single_id );

				$main_single_converted_title = HexcouponBogoController::getInstance()->convert_and_replace_unnecessary_string( $main_single_id );
				$main_single_min_quantity = get_post_meta( $coupon_id, $main_single_converted_title . '-purchased_min_quantity', true );

				if ( $wc_cart->find_product_in_cart( $main_single_key ) && $quantities[$main_single_id] >= $main_single_min_quantity ) {
					$main_product_in_cart = true;
					break;
				}
			}
			if ( $main_product_in_cart ) {
				foreach ( $free_item_id as $free_single_id ) {
					$free_single_converted_title = HexcouponBogoController::getInstance()->convert_and_replace_unnecessary_string( $free_single_id );
					$free_single_quantity = get_post_meta( $coupon_id, $free_single_converted_title . '-free_product_quantity', true );
					$free_single_key = $wc_cart->generate_cart_id( $free_single_id );
					// If the free product does not already exist in the cart, then add to cart
					if ( ! $wc_cart->find_product_in_cart( $free_single_key ) ) {
						$wc_cart->add_to_cart( $free_single_id, $free_single_quantity );
					}
					// If the free product does already exist in the cart, then update product quantity in the cart
					if ( $wc_cart->find_product_in_cart( $free_single_key ) ) {
						$wc_cart->set_quantity( $free_single_key, $free_single_quantity );
					}
				}
			}
			else {
				HexcouponBogoController::getInstance()->remove_cart_product( $free_item_id );
			}
		}
	}
}
