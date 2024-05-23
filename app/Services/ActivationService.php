<?php

namespace HexCoupon\App\Services;

use HexCoupon\App\Core\Helpers\LoyaltyProgram\CreateAllTables;
use HexCoupon\App\Core\Helpers\QrCodeGeneratorHelpers;
use HexCoupon\App\Core\Lib\SingleTon;
use HexCoupon\App\Core\Helpers\StoreCreditHelpers;

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
		add_action( 'init', [ __CLASS__, 'load_hexcoupon_textdomain' ], 1 );
		QrCodeGeneratorHelpers::getInstance()->qr_code_generator_for_url( 0 );

		// Creating all necessary tables for store credit
		StoreCreditHelpers::getInstance()->create_hex_store_credit_logs_table();
		StoreCreditHelpers::getInstance()->create_hex_notification_table();
		StoreCreditHelpers::getInstance()->create_hex_store_credit_table();

		// Creating all necessary tables for loyalty program
		CreateAllTables::getInstance()->create_points_transactions_table();
		CreateAllTables::getInstance()->create_points_log_table();

		// enabling store credit on plugin activation
		$store_credit_enable_settings = [
			'enable' => true,
		];
		update_option( 'store_credit_enable_data', $store_credit_enable_settings );

		// enabling loyalty program on plugin activation
		$loyalty_program_enable_settings = [
			'enable' => true,
		];
		update_option( 'loyalty_program_enable_settings', $loyalty_program_enable_settings );
	}

	public static function load_hexcoupon_textdomain()
	{
		load_plugin_textdomain( 'hex-coupon-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}
