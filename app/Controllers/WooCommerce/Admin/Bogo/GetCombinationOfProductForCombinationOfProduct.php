<?php
namespace hexcoupon\app\Controllers\WooCommerce\Admin\Bogo;

use HexCoupon\App\Controllers\BaseController;
use hexcoupon\app\Controllers\WooCommerce\Admin\CouponGeneralTabController;
use HexCoupon\App\Core\Lib\SingleTon;

class GetCombinationOfProductForCombinationOfProduct extends BaseController
{
	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method customer_gets_a_combination_of_product_against_a_combination_of_product
	 * @return mixed
	 * Customer gets a combination of product against a combination of product
	 */
	public function customer_gets_a_combination_of_product_against_a_combination_of_product( $customer_purchases, $customer_gets_as_free, $main_product_id, $coupon_id, $free_item_id, $wc_cart )
	{
		$hexcoupon_bogo_instance = HexcouponBogoController::getInstance();

		if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) )
			return;

		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
			return;

		if ( 'a_combination_of_products' === $customer_gets_as_free && 'a_combination_of_products' === $customer_purchases ) {
			foreach ( $wc_cart->get_cart() as $cart_item ) {
				// Checking if the cart has all products that the store owner has selected to purchase
				if ( in_array( $cart_item['product_id'], $main_product_id ) ) {
					$product_title = HexcouponBogoController::getInstance()->convert_and_replace_unnecessary_string( $cart_item['product_id'] );

					$main_product_min_quantity = get_post_meta( $coupon_id, $product_title . '-purchased_min_quantity', true );

					if ( $cart_item['quantity'] >= $main_product_min_quantity ) {
						$is_main_product_greater_or_equal_to_min = true;
					}
					else {
						$is_main_product_greater_or_equal_to_min = false;
						// Show error message to the user if main product quantity is less than the store owner has selected
						add_action( 'woocommerce_before_cart', [ $hexcoupon_bogo_instance, 'cart_custom_error_message' ] );
						break;
					}
				}
			}

			foreach ( $free_item_id as $free_single ) {
				$free_single_key = $wc_cart->generate_cart_id( $free_single );
				if ( $is_main_product_greater_or_equal_to_min ) {
					// Add product to the cart if the product does not already exist in the cart
					if ( ! $wc_cart->find_product_in_cart( $free_single_key ) ) {
						$free_single_title = HexcouponBogoController::getInstance()->convert_and_replace_unnecessary_string( $free_single );
						$free_single_quantity = get_post_meta( $coupon_id, $free_single_title . '-free_product_quantity', true );
						$wc_cart->add_to_cart( $free_single, $free_single_quantity );
					}
					// Increase the product quantity if it already exists in the cart
					if ( $wc_cart->find_product_in_cart( $free_single_key ) ) {
						$free_single_title = HexcouponBogoController::getInstance()->convert_and_replace_unnecessary_string( $free_single );
						$free_single_quantity = get_post_meta( $coupon_id, $free_single_title . '-free_product_quantity', true );
						$wc_cart->set_quantity( $free_single_key, $free_single_quantity );
					}
				}
			}
		}
	}
}

