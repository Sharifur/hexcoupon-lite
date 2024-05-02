<?php
namespace HexCoupon\App\Core\WooCommerce;

use HexCoupon\App\Core\Lib\SingleTon;

class AddCustomLinksInAllPluginsPage
{
	use singleton;

	/**
	 * @return void
	 * @author WpHex
	 * @method register
	 * @package hexcoupon
	 * @since 1.0.0
	 * Registers all hooks that are needed to create 'Coupon' category.
	 */
	public function register()
	{
		add_filter( 'plugin_action_links', [ $this, 'hexcoupon_plugin_page_action_list' ], 10, 2 );
	}

	/**
	 * Add custom link besides deactivate text in the plugin page
	 *
	 * @return void
	 */
	public function hexcoupon_plugin_page_action_list( $actions, $plugin_file )
	{
		// Specify the directory and file name of the specific plugin
		$specific_plugin_directory = 'hex-coupon-for-woocommerce';
		$specific_plugin_file = 'hex-coupon-for-woocommerce.php';

		$support_link = 'https://wordpress.org/support/plugin/hex-coupon-for-woocommerce/';
		$documentation_link = 'https://hexcoupon.com/docs/';

		// Check if the current plugin is the specific one
		if ( strpos( $plugin_file, $specific_plugin_directory . '/' . $specific_plugin_file ) !== false ) {
			// Add custom link(s) beside the "Deactivate" link
			$actions[] = '<a href=" ' . esc_url( $support_link ) . ' " target="_blank">'. __( 'Support', 'hex-coupon-for-woocommerce' ) .'</a>';
			$actions[] = '<a href=" ' . esc_url( $documentation_link ) . ' " target="_blank"><b>'. __( 'Documentation', 'hex-coupon-for-woocommerce' ) .'</b></a>';
		}

		return $actions;
	}

}
