<?php

namespace HexCoupon\App\Controllers;

use HexCoupon\App\Core\Lib\SingleTon;
use HexCoupon\App\Services\AddNewCouponMenuService;
use HexCoupon\App\Services\AdminMenuService;
use HexCoupon\App\Services\AllCouponsMeuService;
use HexCoupon\App\Services\CouponCategoryMenuService;
use Kathamo\Framework\Lib\Http\Request;
use function Symfony\Component\VarDumper\Dumper\esc;

class AdminMenuController extends BaseController
{
	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return mixed
	 * @since 1.0.0
	 * Register all hooks for adding menus in the dashboard area.
	 */
	public function register()
	{
		add_action( 'plugins_loaded', [ $this, 'show_hexcoupon_plugin_menu' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method show_hexcoupon_plugin_menu
	 * @return mixed
	 * @since 1.0.0
	 * Checks whether 'Woocommerce' plugin is active or not and based on that the 'Hexcoupon' menu is then displayed.
	 */
	public function show_hexcoupon_plugin_menu()
	{
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'admin_menu', [ $this, 'add_hexcoupon_menu' ] );
			add_action( 'admin_menu', [ $this, 'add_all_coupons_submenu' ] );
			add_action( 'admin_menu', [ $this, 'add_addnew_coupon_submenu' ] );
			add_action( 'admin_menu', [ $this, 'add_coupon_category_submenu' ] );
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_hexcoupon_menu
	 * @return mixed
	 * @since 1.0.0
	 * Add a menu named 'HexCoupon' in the admin dashboard area.
	 */
	public function add_hexcoupon_menu()
	{
		add_menu_page(
			esc_html__( 'HexCoupon', 'hex-coupon-for-woocommerce' ),
			esc_html__( 'HexCoupon', 'hex-coupon-for-woocommerce' ),
			'manage_options',
			'hexcoupon-page',
			[ $this, 'render_hexcoupon' ],
			'dashicons-admin-settings',
			40
		);
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_all_coupons_submenu
	 * @return string
	 * @since 1.0.0
	 * Add a sub-menu named 'Add All Coupons' in the admin dashboard area under the menu 'HexCoupon'.
	 */
	public function add_all_coupons_submenu()
	{
		add_submenu_page(
			'hexcoupon-page',
			esc_html__( 'All Coupons', 'hex-coupon-for-woocommerce' ),
			esc_html__( 'All Coupons', 'hex-coupon-for-woocommerce' ),
			'manage_options',
			'all-coupons',
			[ $this, 'render_all_coupons_submenu' ]
		);
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_addnew_coupon_submenu
	 * @return string
	 * @since 1.0.0
	 * Add a sub-menu named 'Add New Coupon' in the admin dashboard area under the menu 'HexCoupon'.
	 */
	public function add_addnew_coupon_submenu()
	{
		add_submenu_page(
			'hexcoupon-page',
			esc_html__( 'Add Coupon', 'hex-coupon-for-woocommerce' ),
			esc_html__( 'Add Coupon', 'hex-coupon-for-woocommerce' ),
			'manage_options',
			'add_new_coupon',
			[ $this, 'render_addnew_coupon_submenu' ],
		);
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_coupon_category_submenu
	 * @return string
	 * @since 1.0.0
	 * Add a sub-menu named 'Coupon Category' in the admin dashboard area under the menu 'HexCoupon'.
	 */
	public function add_coupon_category_submenu()
	{
		add_submenu_page(
			'hexcoupon-page',
			esc_html__( 'Coupon Categories', 'hex-coupon-for-woocommerce' ),
			esc_html__( 'Coupon Categories', 'hex-coupon-for-woocommerce' ),
			'manage_options',
			'coupon_category',
			[ $this, 'render_coupon_category_submenu' ],
		);
	}

	public function render_hexcoupon()
	{
		$this->render( '/admin/admin-menu.php' );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method render_all_coupons_submenu
	 * @return string
	 * @since 1.0.0
	 * Rednders the 'All Coupons' sub-menu page content.
	 */
	public function render_all_coupons_submenu()
	{
		$menu_data = AllCouponsMeuService::getInstance();
		$data      = $menu_data->getData();
		$this->render( '/admin/all-coupons-submenu.php', $data );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method render_addnew_coupon_submenu
	 * @return string
	 * @since 1.0.0
	 * Rednders the 'Add New Coupon' sub-menu page content.
	 */
	public function render_addnew_coupon_submenu()
	{
		$menu_data = AddNewCouponMenuService::getInstance();
		$data      = $menu_data->getData();
		$this->render( '/admin/addnew-coupon-submenu.php', $data );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method render_coupon_category_submenu
	 * @return string
	 * @since 1.0.0
	 * Rednders the 'Coupon Category' sub-menu page content.
	 */
	public function render_coupon_category_submenu()
	{
		$menu_data = CouponCategoryMenuService::getInstance();
		$data      = $menu_data->getData();
		$this->render( '/admin/coupon-category-submenu.php', $data );
	}
}
