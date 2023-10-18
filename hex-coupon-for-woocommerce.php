<?php
/**
 * @package hexcoupon
 *
 * Plugin Name: HexCoupon - Advance Coupons For WooCommerce
 * Plugin URI: https://wordpress.org/plugins/hex-coupon-for-woocommerce
 * Description: Extend coupon functionality in your Woocommerce store.
 * Version: 1.0.0
 * Author: WpHex
 * Requires at least: 5.4
 * Tested up to: 6.3
 * Requires PHP: 7.1
 * WC requires at least: 6.0
 * WC tested up to: 7.8.2
 * Author URI: https://wphex.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hex-coupon-for-woocommerce
 * Domain Path: /languages
 */

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

Core::getInstance();
