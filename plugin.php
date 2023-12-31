<?php
/**
 * @package hexcoupon
 *
 * Plugin Name: HexCoupon
 * Plugin URI: https://wordpress.org/plugins/hexcoupon
 * Description: Extend coupon functionality in your Woocommerce store.
 * Version: 1.0.0
 * Author: WpHex
 * Requires at least: 5.4
 * Tested up to: 6.2.2
 * Requires PHP: 7.1
 * WC requires at least: 6.0
 * WC tested up to: 7.8.2
 * Author URI: https://wphex.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hexcoupon
 * Domain Path: /languages
 */

use HexCoupon\App\Core\Core;

if ( ! defined( 'ABSPATH' ) ) die();

define( 'HXC_FILE', __FILE__ );

require_once __DIR__ . '/configs/bootstrap.php';

if ( file_exists( HXC_DIR_PATH . '/vendor/autoload.php' ) ) {
	require_once HXC_DIR_PATH . '/vendor/autoload.php';
}

Core::getInstance();
