<?php
/**
 * @package hexcoupon
 *
 * Plugin Name: HexCoupon: Ultimate WooCommerce Toolkit for Coupons, Store Credits, Loyalty Rewards, BOGO Offers, and Custom Discount Rules
 * Plugin URI: https://wordpress.org/plugins/hex-coupon-for-woocommerce
 * Description: Extend coupon functionality in your Woocommerce store.
 * Version: 1.0.10
 * Author: WpHex
 * Requires at least: 5.4
 * Tested up to: 6.4.2
 * Requires PHP: 7.1
 * WC requires at least: 6.0
 * WC tested up to: 8.4.0
 * Author URI: https://wphex.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hex-coupon-for-woocommerce
 * Domain Path: /languages
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use HexCoupon\App\Core\Core;

if ( ! defined( 'ABSPATH' ) ) die();

define( 'HEXCOUPON_FILE', __FILE__ );

require_once __DIR__ . '/configs/bootstrap.php';

if ( file_exists( HEXCOUPON_DIR_PATH . '/vendor/autoload.php' ) ) {
	require_once HEXCOUPON_DIR_PATH . '/vendor/autoload.php';
}

/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function appsero_init_tracker_hex_coupon_for_woocommerce() {

	if ( ! class_exists( 'Appsero\Client' ) ) {
		require_once __DIR__ . '/appsero/src/Client.php';
	}

	$client = new Appsero\Client( 'c0ee1555-4851-4d71-8b6d-75b1872dd3d2', 'HexCoupon &#8211; Advance Coupons For WooCommerce(Free)', __FILE__ );

	// Active insights
	$client->insights()->init();

}

appsero_init_tracker_hex_coupon_for_woocommerce();

add_filter( 'plugin_action_links', 'hexcoupon_plugin_page_action_list', 10, 2 );

/**
 * Add custom texts besides deactivate text in the plugin page
 *
 * @return void
 */
function hexcoupon_plugin_page_action_list( $actions, $plugin_file )
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

/**
 * Plugin compatibility declaration with WooCommerce HPOS - High Performance Order Storage
 *
 * @return void
 */
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( FeaturesUtil::class ) ) {
		FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

/**
 * Load the plugin text-domain
 *
 * @return void
 */
add_action( 'init', 'load_hexcoupon_textdomain', 1 );
function load_hexcoupon_textdomain() {
	load_plugin_textdomain( 'hex-coupon-for-woocommerce', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

/**
 * Redirect users to the dashboard of HexCoupon after activating the plugin
 *
 * @return void
 */
add_action( 'activated_plugin', 'redirect_to_hexcoupon_dashboard_after_plugin_activation' );
function redirect_to_hexcoupon_dashboard_after_plugin_activation( $plugin ) {
	if ( $plugin == 'hex-coupon-for-woocommerce/hex-coupon-for-woocommerce.php' ) {
		// Check if WooCommerce is active and then redirect to HexCoupon menu page
		if ( class_exists( 'WooCommerce' ) ) {
			// Redirect to the specified page after activation
			wp_safe_redirect( admin_url( 'admin.php?page=hexcoupon-page' ) );
			exit;
		}
	}
}

/**
 * Override the cart page and checkout page with the old woocommerce classic pattern content
 *
 * @return void
 */
function alter_cart_page_with_cart_shortcode( $content ) {
	if ( class_exists( 'WooCommerce' ) ) {
		// Check if it's the WooCommerce cart page
		if ( is_cart() ) {
			// Insert the [woocommerce_cart] shortcode in the cart page of the site.
			$content = '[woocommerce_cart]';
		}

		if ( is_checkout() ) {
			// Insert the [woocommerce_checkout] shortcode in the checkout page of the site
			$content = '[woocommerce_checkout]';
		}
	}

	return $content;
}

add_filter( 'the_content', 'alter_cart_page_with_cart_shortcode' );

add_filter('manage_shop_coupon_posts_custom_column', 'custom_coupon_type_column_content', 10, 2);

function custom_coupon_type_column_content($column, $post_id) {
	if ($column === 'type') {
		$coupon = new WC_Coupon($post_id);

		if ($coupon) {
			// Get the coupon type
			$coupon_type = $coupon->get_discount_type();

			// Customize the output based on the coupon type
			switch ($coupon_type) {
				case 'fixed_cart':
					echo 'Fixed Cart Discount';
					break;
				case 'percent':
					echo 'Percentage Discount';
					break;
				case 'fixed_product':
					echo 'Fixed Product Discount';
					break;
				case 'custom_option':
					echo 'Custom Option';
					break;
				default:
					echo 'Unknown Type';
			}
		} else {
			echo 'N/A';
		}
	}
}





Core::getInstance();
