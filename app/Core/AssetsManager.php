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
		$folder_prefix = Hxc_get_config('dev_mode') ? '/dev' : '/dist';

		if ( ( str_contains( $_SERVER['REQUEST_URI'], 'post-new.php' ) && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'shop_coupon') ||
			( isset( $_GET['post'] ) && isset( $_GET['action'] ) && $_GET['action'] === 'edit' ) ) {

			wp_enqueue_script(
				Hxc_prefix( 'admin-js' ),
				Hxc_asset_url( $folder_prefix . "/admin/js/admin.js" ),
				['jquery', 'select2', 'wp-i18n'],
				$this->version,
				true
			);

			wp_enqueue_script(
				Hxc_prefix( 'flatpickr' ),
				Hxc_asset_url( $folder_prefix . "/admin/js/flatpickr.min.js" ),
				[ 'jquery'],
				$this->version,
				true
			);

			wp_enqueue_style(
				Hxc_prefix( 'admin' ),
				Hxc_asset_url( $folder_prefix. "/admin/css/admin.css" ),
				array(),
				$this->version,
				'all'
			);

			wp_enqueue_style(
				Hxc_prefix( 'flatpickr' ),
				Hxc_asset_url( $folder_prefix . "/admin/css/flatpickr.min.css" ),
				array(),
				$this->version,
				'all'
			);

		}

		//load react js and css only on the hexcoupon plugin page
		$screen = get_current_screen();

		if ( $screen->base === "toplevel_page_hexcoupon-page" ){
			wp_enqueue_script(
				Hxc_prefix( 'main' ),
				Hxc_url( "/dist/assets/index.js" ),
				['jquery','wp-element'],
				$this->version,
				true
			);

			wp_enqueue_style(
				Hxc_prefix( 'main' ),
				Hxc_url( "/dist/assets/index.css" ),
				[],
				$this->version,
				"all"
			);
		}

		$coupon_dashboard_label_text = [
			'couponsCreatedLabel' => esc_html__( 'Coupons Created', 'hexcoupon' ),
			'couponsRedeemedLabel' => esc_html__( 'Coupons Redeemed', 'hexcoupon' ),
			'couponsActiveLabel' => esc_html__( 'Coupons Active', 'hexcoupon' ),
			'couponsExpiredLabel' => esc_html__( 'Coupons Expired', 'hexcoupon' ),
			'redeemedCouponValueLabel' => esc_html__( 'Redeemed Coupon Amount', 'hexcoupon' ),
			'sharableUrlCouponsLabel' => esc_html__( 'Sharable Url Coupons', 'hexcoupon' ),
			'bogoCouponlabel' => esc_html__( 'Bogo Coupons', 'hexcoupon' ),
			'geographicRestrictionLabel' => esc_html__( 'Geographically Restricted', 'hexcoupon' ),
			'couponInsightsLabel' => esc_html__( 'Coupon Insights', 'hexcoupon' ),
			'thisYearLabel' => esc_html__( 'This Year', 'hexcoupon' ),
			'thisMonthLabel' => esc_html__( 'This Month', 'hexcoupon' ),
			'thisWeekLabel' => esc_html__( 'This Week', 'hexcoupon' ),
			'yesterdayLabel' => esc_html__( 'Yesterday', 'hexcoupon' ),
			'todayLabel' => esc_html__( 'Today', 'hexcoupon' ),
		];

		wp_localize_script( Hxc_prefix( 'main' ), 'hexCuponData', [
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
				'thisYearLabel' => $coupon_dashboard_label_text['thisYearLabel'],
				'thisMonthLabel' => $coupon_dashboard_label_text['thisMonthLabel'],
				'thisWeekLabel' => $coupon_dashboard_label_text['thisWeekLabel'],
				'yesterdayLabel' => $coupon_dashboard_label_text['yesterdayLabel'],
				'todayLabel' => $coupon_dashboard_label_text['todayLabel'],
			]
		] );

		wp_set_script_translations( 'admin-js', 'hexcoupon', plugin_dir_path( __FILE__ ) . 'languages' );
	}

	public function public_scripts()
	{
		$folder_prefix = Hxc_get_config( 'dev_mode' ) ? '/dev' : '/dist';

		wp_enqueue_script(
			Hxc_prefix( 'public' ),
			Hxc_asset_url( $folder_prefix . "/public/js/public.js" ),
			[],
			$this->version,
			true
		);

		wp_enqueue_style(
			Hxc_prefix( 'public' ),
			Hxc_asset_url( $folder_prefix . "/public/css/public.css" ),
			array(),
			$this->version,
			'all'
		);
	}
}
