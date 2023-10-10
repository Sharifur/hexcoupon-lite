<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Configurations for the plugin
 *
 * @package hexcoupon
 */
return array(
	'plugin_prefix'		=> 'hexcoupon',
	'plugin_slug'		=> 'hexcoupon',
	'namaspace_root'	=> 'HexCoupon',
	'plugin_version'	=> '1.0.5',
	'plugin_name'		=> 'HexCoupon - Advance Coupons For WooCommerce',
	'dev_mode'			=> false,
	'root_dir'			=> dirname(__DIR__),
	'middlewares'		=> [
		'auth'	=> HexCoupon\App\Controllers\Middleware\Auth::class,
	],
);
