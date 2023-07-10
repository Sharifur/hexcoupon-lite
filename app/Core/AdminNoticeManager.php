<?php

namespace HexCoupon\App\Core;

use HexCoupon\App\Core\Lib\SingleTon;

class AdminNoticeManager
{
	use SingleTon;

	private $woocommerce_plugin_url = 'https://wordpress.org/plugins/woocommerce/';

	public function register()
	{
		// Hook for showing a notice to activate the WooCommerce plugin to the admin_notices action
		add_action( 'admin_notices', [ $this, 'show_woocommerce_activation_notice' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method show_woocommerce_activation_notice
	 * @return string
	 * @since 1.0.0
	 * Define the WooCommerce activation function to display the admin notice
	 */
	public function show_woocommerce_activation_notice()
	{
		if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			?>
			<div class="notice notice-error">
				<p><?php printf( esc_html__( '%s', 'hexcoupon' ), $this->getWooCommerceNoticeMessage() ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method getWooCommerceNoticeMessage
	 * @return string
	 * @since 1.0.0
	 * Render string for WooCommerce Notice for users.
	 * */
	private function getWooCommerceNoticeMessage()
	{
		return sprintf( __( 'Please activate the <a href="%s">WooCommerce</a> plugin to use HexCoupon features.','hexcoupon' ), esc_url( $this->woocommerce_plugin_url ) );
	}

}
