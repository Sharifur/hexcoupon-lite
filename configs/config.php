<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Configurations for the plugin
 *
 * @package hexcoupon
 */
return array(
	'plugin_prefix'		=> 'Hxc',
	'plugin_slug'		=> 'Hxc',
	'namaspace_root'	=> 'HexCoupon',
	'plugin_version'	=> '1.0.3',
	'plugin_name'		=> 'HexCoupon',
	'dev_mode'			=> false,
	'root_dir'			=> dirname(__DIR__),
	'middlewares'		=> [
		'auth'	=> HexCoupon\App\Controllers\Middleware\Auth::class,
	],
);
