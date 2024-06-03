<?php
namespace HexCoupon\App\Core\WooCommerce\StoreCredit;

use HexCoupon\App\Core\Lib\SingleTon;

class OrderDetailsForStoreCredit {

	use SingleTon;

	/**
	 * Register hooks callback
	 *
	 * @return void
	 */
	public function register()
	{
		add_action( 'woocommerce_admin_order_totals_after_total', [ $this, 'add_custom_order_total_row' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_custom_order_total_row
	 * @return void
	 * Add store credit row to the checkout order details section
	 */
	public function add_custom_order_total_row( $order_id )
	{
		// Get the meta value
		$deducted_store_credit = get_post_meta( $order_id, 'deducted_store_credit', true );

		// Check if there is a value to display
		if ( $deducted_store_credit ) {
			echo '<tr>';
			echo '<td class="label">' . esc_html__( 'Store Credit Used:', 'hex-coupon-for-woocommerce' ) . '</td>';
			echo '<td class="total"><span class="woocommerce-Price-amount amount">-' . esc_html( $deducted_store_credit ) . '</span></td>';
			echo '</tr>';
		}
	}

}
