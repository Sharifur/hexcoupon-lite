<?php

namespace HexCoupon\App\Controllers;

use HexCoupon\App\Core\Lib\SingleTon;
use HexCoupon\App\Services\AddNewCouponMenuService;
use HexCoupon\App\Services\AdminMenuService;
use HexCoupon\App\Services\AllCouponsMeuService;
use HexCoupon\App\Services\CouponCategoryMenuService;
use Kathamo\Framework\Lib\Http\Request;

class AdminMenuController extends BaseController
{
	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return void
	 * @since 1.0.0
	 * Register all hooks for adding menus in the dashboard area.
	 */
	public function register()
	{
		add_action( 'admin_menu', [ $this, 'add_hexcoupon_menu' ] );
		add_action( 'admin_menu', [ $this, 'add_all_coupon_submenu' ] );
		add_action( 'admin_menu', [ $this, 'add_addnew_coupon_submenu' ] );
		add_action( 'admin_menu', [ $this, 'add_coupon_category_submenu' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_hexcoupon_menu
	 * @return string
	 * @since 1.0.0
	 * Add a menu named 'HexCoupon' in the admin dashboard area.
	 */
	public function add_hexcoupon_menu()
	{
		add_menu_page(
			__( 'HexCoupon', 'hexcoupon' ),
			__( 'HexCoupon', 'hexcoupon' ),
			'manage_options',
			'hexcoupon-page',
			[ $this, 'render_admin_menu' ],
			'dashicons-admin-settings',
			40
		);
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_all_coupon_submenu
	 * @return string
	 * @since 1.0.0
	 * Add a sub-menu named 'All Coupons' in the admin dashboard area under the menu 'HexCoupon'.
	 */
	public function add_all_coupon_submenu()
	{

		//
		add_submenu_page(
			'hexcoupon-page',
			__( 'All Coupons', 'hexcoupon' ),
			__( 'All Coupons', 'hexcoupon' ),
			'manage_options',
			'all_coupons',
			[ $this, 'render_all_coupons_submenu' ],
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
			__( 'Add Coupon', 'hexcoupon' ),
			__( 'Add Coupon', 'hexcoupon' ),
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
			__( 'Coupon Category', 'hexcoupon' ),
			__( 'Coupon Category', 'hexcoupon' ),
			'manage_options',
			'coupon_category',
			[ $this, 'render_coupon_category_submenu' ],
		);
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method render_admin_menu
	 * @return string
	 * @since 1.0.0
	 * Rednders the 'HexCoupon' menu page content.
	 */
	public function render_admin_menu()
	{
		// $this->middleware( 'auth' );

		// $validate = $this->validate( [
		// 	'page' => 'stringOnly',
		// ] );

		// $res = Request::get( 'https://jsonplaceholder.typicode.com/posts/1' );
		// dump($validate->getData(), $res->getBody(), 'Response from jsonplaceholder');

		$menu_data = AdminMenuService::getInstance();
		$data      = $menu_data->getData();
		$this->render( '/admin/admin-menu.php', $data );
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
