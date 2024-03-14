<?php

namespace HexCoupon\App\Services;

use HexCoupon\App\Core\Lib\SingleTon;

class ActivationService
{
	use SingleTon;

	public function register()
	{
		// activation event handler
		\register_activation_hook(
			HEXCOUPON_FILE,
			[ __CLASS__, 'activate' ]
		);
	}

	public static function activate()
	{
		add_action('init', [__CLASS__, 'load_hexcoupon_textdomain'], 1);
	}

	public static function load_hexcoupon_textdomain()
	{
		load_plugin_textdomain('hex-coupon-for-woocommerce', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}
}
