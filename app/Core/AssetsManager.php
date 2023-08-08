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
		$this->configs = Hxc_get_config();

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
		$folder_prefix = '/dist';//; Hxc_get_config('dev_mode') ? '/dev' : '/dist';

		wp_enqueue_script(
			Hxc_prefix( 'admin' ),
			Hxc_asset_url( $folder_prefix."/admin.js" ),
			[ 'jquery','select2' ],
			$this->version,
			true
		);
		wp_enqueue_script(
			Hxc_prefix( 'flatpickr' ),
			Hxc_asset_url( "/dev/admin/js/flatpickr.js" ),
			[ 'jquery'],
			$this->version,
			true
		);

//		if ( '/dev' == $folder_prefix ) {
//			wp_enqueue_script(
//				Hxc_prefix( 'admin-js' ),
//				Hxc_asset_url( $folder_prefix."/admin.js" ),
//				[ 'jquery','select2' ],
//				$this->version,
//				true
//			);
//
//
			wp_enqueue_style(
				Hxc_prefix( 'global-scss' ),
				Hxc_asset_url( "/dev/admin/scss/global.scss" ),
				array(),
				$this->version,
				'all'
			);

			wp_enqueue_style(
				Hxc_prefix( 'admin' ),
				Hxc_asset_url( "/dev/admin/css/admin.css" ),
				array(),
				$this->version,
				'all'
			);

			wp_enqueue_style(
				Hxc_prefix( 'flatpickr' ),
				Hxc_asset_url( "/dev/admin/css/flatpickr.min.css" ),
				array(),
				$this->version,
				'all'
			);
//		}
//
//		if ( '/dist' == $folder_prefix ) {
//			wp_enqueue_script(
//				Hxc_prefix( 'admin-js' ),
//				Hxc_asset_url( $folder_prefix."/admin/js/admin.min.js" ),
//				[ 'jquery','select2' ],
//				$this->version,
//				true
//			);
//		}

//		$select2_placeholder_data = array(
//			'escapedPlaceholderText1' => esc_html__( 'Select Roles', 'hexcoupon' ),
//			'escapedPlaceholderText2' => esc_html__( 'Select Payment Methods', 'hexcoupon' ),
//			'escapedPlaceholderText3' => esc_html__( 'Select Shipping Methods', 'hexcoupon' ),
//		);
//
//		wp_localize_script( Hxc_prefix( 'admin-js' ), 'escapedData', $select2_placeholder_data );
	}
}
