<?php
namespace HexCoupon\App\Core\WooCommerce\StoreCredit;

use HexCoupon\App\Core\Helpers\StoreCreditPaymentHelpers;
use HexCoupon\App\Core\Lib\SingleTon;

class AddStoreCreditCheckbox {

	/**
	 * Register hooks callback
	 *
	 * @return void
	 */
	public function register()
	{
		// Add checkbox in the checkout page for applying store credit in the legacy checkout
		add_action( 'woocommerce_review_order_before_submit', [ $this, 'add_store_credit_checkbox_in_checkout' ] );
	}

	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_store_credit_checkbox_in_checkout
	 * @return void
	 * Add store credit checkbox in checkout page
	 */
	public function add_store_credit_checkbox_in_checkout()
	{
		$show_total_remaining_amount = StoreCreditPaymentHelpers::getInstance()->show_total_remaining_amount();

		echo '<div class="store-credit-checkbox"><h3>' . esc_html__( 'Available Store Credit: ', 'hex-coupon-for-woocommerce-pro' ) . esc_html( number_format( $show_total_remaining_amount, 2 ) ) . '</h3>';
		woocommerce_form_field( 'store_credit_checkbox', [
			'type' => 'checkbox',
			'class' => array( 'input-checkbox' ),
			'label' => esc_html__( 'Deduct credit amount from total', 'hex-coupon-for-woocommerce-pro' ),
			'required' => true,
		], WC()->checkout->get_value( 'store_credit_checkbox' ) );
		echo '</div>';
	}

}
