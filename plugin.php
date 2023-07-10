<?php
/**
 * @package hexcoupon
 *
 * Plugin Name: HexCoupon
 * Plugin URI: https://wphex.com/
 * Description: Add coupon functionality in your Woocommerce store.
 * Version: 1.0.0
 * Author: WpHex
 * Author URI: https://wphex.com/
 * License: GPLv2 or later
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
