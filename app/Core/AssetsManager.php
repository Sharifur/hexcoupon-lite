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
		add_action( 'wp_enqueue_scripts', [ $this, 'public_scripts' ] );
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
		$js_file_extension = hexcoupon_get_config('dev_mode') ? '.js' : '.min.js';
		$css_file_extension = hexcoupon_get_config('dev_mode') ? '.css' : '.min.css';

		if ( ( str_contains( $_SERVER['REQUEST_URI'], 'post-new.php' ) && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'shop_coupon') ||
			( isset( $_GET['post'] ) && isset( $_GET['action'] ) && $_GET['action'] === 'edit' ) ) {

			wp_enqueue_script(
				hexcoupon_prefix( 'admin' ),
				hexcoupon_asset_url( $folder_prefix . "/admin/js/admin" . $js_file_extension ),
				['jquery', 'select2', 'wp-i18n'],
				$this->version,
				true
			);

			wp_enqueue_script(
				hexcoupon_prefix( 'flatpickr' ),
				hexcoupon_asset_url( $folder_prefix . "/admin/js/flatpickr.min.js" ),
				[ 'jquery'],
				$this->version,
				true
			);

			wp_enqueue_style(
				hexcoupon_prefix( 'admin' ),
				hexcoupon_asset_url( $folder_prefix. "/admin/css/admin" .$css_file_extension ),
				array(),
				$this->version,
				'all'
			);

			wp_enqueue_style(
				hexcoupon_prefix( 'flatpickr' ),
				hexcoupon_asset_url( $folder_prefix . "/admin/css/flatpickr.min.css" ),
				array(),
				$this->version,
				'all'
			);

		}

		wp_enqueue_script(
			hexcoupon_prefix( 'admin' ),
			hexcoupon_asset_url( $folder_prefix . "/admin/js/all-coupon-page" . $js_file_extension ),
			['jquery', 'select2', 'wp-i18n'],
			$this->version,
			true
		);

		wp_enqueue_style(
			hexcoupon_prefix( 'hexcoupon-admin-notice' ),
			hexcoupon_asset_url( $folder_prefix . "/admin/css/hex-dashboard-notice" . $css_file_extension ),
			array(),
			$this->version,
			'all'
		);

		wp_enqueue_style(
			hexcoupon_prefix( 'all-coupon-page' ),
			hexcoupon_asset_url( $folder_prefix . "/admin/css/all-coupon-page" . $css_file_extension ),
			array(),
			$this->version,
			'all'
		);

		//load react js and css only on the hexcoupon plugin page
		$screen = get_current_screen();

		if ( $screen->base === "toplevel_page_hexcoupon-page" ){
			wp_enqueue_script(
				hexcoupon_prefix( 'main' ),
				hexcoupon_url( "/dist/assets/index.js" ),
				['jquery','wp-element'],
				$this->version,
				true
			);

			wp_enqueue_style(
				hexcoupon_prefix( 'main' ),
				hexcoupon_url( "/dist/assets/index.css" ),
				[],
				$this->version,
				"all"
			);
		}

		$coupon_dashboard_label_text = [
			'couponsCreatedLabel' => esc_html__( 'Coupons Created', 'hex-coupon-for-woocommerce' ),
			'couponsRedeemedLabel' => esc_html__( 'Coupons Redeemed', 'hex-coupon-for-woocommerce' ),
			'couponsActiveLabel' => esc_html__( 'Coupons Active', 'hex-coupon-for-woocommerce' ),
			'couponsExpiredLabel' => esc_html__( 'Coupons Expired', 'hex-coupon-for-woocommerce' ),
			'redeemedCouponValueLabel' => esc_html__( 'Redeemed Coupon Amount', 'hex-coupon-for-woocommerce' ),
			'sharableUrlCouponsLabel' => esc_html__( 'Sharable Url Coupons', 'hex-coupon-for-woocommerce' ),
			'bogoCouponlabel' => esc_html__( 'Bogo Coupons', 'hex-coupon-for-woocommerce' ),
			'geographicRestrictionLabel' => esc_html__( 'Geographically Restricted', 'hex-coupon-for-woocommerce' ),
			'couponInsightsLabel' => esc_html__( 'Coupon Insights', 'hex-coupon-for-woocommerce' ),
			'thisWeekLabel' => esc_html__( 'This Week', 'hex-coupon-for-woocommerce' ),
			'yesterdayLabel' => esc_html__( 'Yesterday', 'hex-coupon-for-woocommerce' ),
			'todayLabel' => esc_html__( 'Today', 'hex-coupon-for-woocommerce' ),
		];

		wp_localize_script( hexcoupon_prefix( 'main' ), 'hexCuponData', [
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce('hexCuponData-react_nonce'),
			'restApiUrl' => get_site_url().'/wp-json/hexcoupon/v1/',
			'translate_array' => [
				'couponsCreatedLabel' => $coupon_dashboard_label_text['couponsCreatedLabel'],
				'couponsRedeemedLabel' => $coupon_dashboard_label_text['couponsRedeemedLabel'],
				'couponsActiveLabel' => $coupon_dashboard_label_text['couponsActiveLabel'],
				'couponsExpiredLabel' => $coupon_dashboard_label_text['couponsExpiredLabel'],
				'redeemedCouponValueLabel' => $coupon_dashboard_label_text['redeemedCouponValueLabel'],
				'sharableUrlCouponsLabel' => $coupon_dashboard_label_text['sharableUrlCouponsLabel'],
				'bogoCouponlabel' => $coupon_dashboard_label_text['bogoCouponlabel'],
				'geographicRestrictionLabel' => $coupon_dashboard_label_text['geographicRestrictionLabel'],
				'couponInsightsLabel' => $coupon_dashboard_label_text['couponInsightsLabel'],
				'thisWeekLabel' => $coupon_dashboard_label_text['thisWeekLabel'],
				'yesterdayLabel' => $coupon_dashboard_label_text['yesterdayLabel'],
				'todayLabel' => $coupon_dashboard_label_text['todayLabel'],
			]
		] );

		wp_set_script_translations( 'admin-js', 'hex-coupon-for-woocommerce', plugin_dir_path( __FILE__ ) . 'languages' );
	}

	public function public_scripts()
	{
		$folder_prefix = hexcoupon_get_config( 'dev_mode' ) ? '/dev' : '/dist';
		$js_file_extension = hexcoupon_get_config('dev_mode') ? '.js' : '.min.js';
		$css_file_extension = hexcoupon_get_config('dev_mode') ? '.css' : '.min.css';

		wp_enqueue_script(
			hexcoupon_prefix( 'public' ),
			hexcoupon_asset_url( $folder_prefix . "/public/js/public" . $js_file_extension ),
			[],
			$this->version,
			true
		);

		wp_enqueue_style(
			hexcoupon_prefix( 'public' ),
			hexcoupon_asset_url( $folder_prefix . "/public/css/public" . $css_file_extension ),
			array(),
			$this->version,
			'all'
		);
	}
}
