<?php
namespace HexCoupon\App\Core\WooCommerce;

use HexCoupon\App\Core\Lib\SingleTon;

/**
 * will add more
 * @since  1.0.0
 * */

class MyAccount
{
	use SingleTon;

	private $all_coupons;
	private $coupon_codes = [];

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return void
	 * @since 1.0.0
	 * Add all hooks that is need for this page
	 */
	public function register()
	{
		// Action hook for adding 'All Coupons' menu page in the 'My Account' Page menu
		add_filter ( 'woocommerce_account_menu_items', [ $this, 'coupon_menu_in_my_account_page' ], 40 );
		// Action hook for registering permalink endpoint
		add_action( 'init', [ $this, 'coupon_menu_page_endpoint' ] );
		// Action hook for displaying 'All Coupons' page content
		add_action( 'woocommerce_account_all-coupons_endpoint', [ $this, 'coupon_page_endpoint_content'] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method coupon_menu_in_my_account_page
	 * @param $all_menu_links
	 * @return array|string[]
	 * @since 1.0.0
	 */
	public function coupon_menu_in_my_account_page( $all_menu_links )
	{
		$all_menu_links = array_slice( $all_menu_links, 0, 5, true )
			+ [ 'all-coupons' => __( 'All Coupons', 'hexcoupon' ) ]
			+ array_slice( $all_menu_links, 5, NULL, true );

		return $all_menu_links;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method coupon_menu_page_endpoint
	 * @return string
	 * @since 1.0.0
	 * Add/rewrite the menu endpoint of 'All Coupons' menu page.
	 */
	public function coupon_menu_page_endpoint()
	{
		return add_rewrite_endpoint( 'all-coupons', EP_PAGES );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method all_coupon_list
	 * @return string
	 * @since 1.0.0
	 * Displays all available coupon codes.
	 */
	public function all_coupon_list()
	{
		$coupon_posts = get_posts( [
			'posts_per_page' => -1,
			'orderby' => 'name',
			'order' => 'asc',
			'post_type' => 'shop_coupon',
			'post_status' => 'publish',
//			'meta_query' => [
//				[
//					'key' => '_expiry_date',
//					'value' => date('Y-m-d'),
//					'compare' => '>=',
//					'type' => 'DATE',
//				],
//			],
		] );

		foreach ( $coupon_posts as $coupon_post ) {
			$this->coupon_codes[] = $coupon_post->post_title;
		}

		// Display available coupon codes
		return implode(', ', $this->coupon_codes);
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method coupon_page_endpoint_content
	 * @return string
	 * @since 1.0.0
	 * Display the content in the 'All Coupons' menu page, the contents are all coupon names.
	 */
	public function coupon_page_endpoint_content()
	{
		$this->all_coupons = $this->all_coupon_list();
		echo $this->all_coupons;
		return $this->all_coupons;
	}
}
