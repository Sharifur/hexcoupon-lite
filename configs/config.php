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
	'plugin_version'	=> '1.0.0',
	'plugin_name'		=> 'HexCoupon',
	'dev_mode'			=> true,
	'root_dir'			=> dirname(__DIR__),
	'middlewares'		=> [
		'auth'	=> HexCoupon\App\Controllers\Middleware\Auth::class,
	],
);
