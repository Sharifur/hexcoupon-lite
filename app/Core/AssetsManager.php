<?php

namespace HexCoupon\App\Core;

use HexCoupon\App\Core\Lib\SingleTon;

class AssetsManager
{
	use SingleTon;

	private $version = '';
	private $configs = [];

	public function register()
	{
		$this->configs = hexcoupon_get_config();

		$this->before_register_assets();
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
	}

	private function before_register_assets()
	{
		if ( $this->configs['dev_mode'] ) {
			return $this->version = time();
		}
		$this->version = $this->configs['plugin_version'];
	}

	public function admin_scripts()
	{
		$folder_prefix = hexcoupon_get_config('dev_mode') ? '/dev' : '/dist';

		if ( '/dev' == $folder_prefix ) {
			wp_enqueue_script(
				hexcoupon_prefix( 'admin' ),
				hexcoupon_asset_url( $folder_prefix."/admin/js/admin.js" ),
				['jquery','select2'],
				$this->version,
				true
			);
		}

		if ( '/dist' == $folder_prefix ) {
			wp_enqueue_script(
				hexcoupon_prefix( 'admin-js' ),
				hexcoupon_asset_url( $folder_prefix."/admin/js/admin.min.js" ),
				[ 'jquery','select2' ],
				$this->version,
				true
			);
		}

		$select2_placeholder_data = array(
			'escapedPlaceholderText1' => esc_html__( 'Select Roles', 'hex-coupon-for-woocommerce' ),
			'escapedPlaceholderText2' => esc_html__( 'Select Payment Methods', 'hex-coupon-for-woocommerce' ),
			'escapedPlaceholderText3' => esc_html__( 'Select Shipping Methods', 'hex-coupon-for-woocommerce' ),
		);

		wp_localize_script( hexcoupon_prefix( 'admin-js' ), 'escapedData', $select2_placeholder_data );
	}
}
