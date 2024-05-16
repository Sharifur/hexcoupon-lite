<?php
namespace HexCoupon\App\Core\WooCommerce\LoyaltyProgram;

use HexCoupon\App\Core\Lib\SingleTon;

class LoyaltyProgram
{
	use SingleTon;

	private $user;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return void
	 * @since 1.0.0
	 * Add all hooks that are needed.
	 */
	public function register()
	{
		// Action hook for adding 'Loyalty Points' menu page in the 'My Account' Page Menu
		add_filter ( 'woocommerce_account_menu_items', [ $this, 'loyalty_points_in_my_account_page' ], 40 );
		// Action hook for registering permalink endpoint
		add_action( 'init', [ $this, 'loyalty_points_menu_page_endpoint' ] );
		// Action hook for displaying 'Loyalty Points' page content
		add_action( 'woocommerce_account_loyalty-points_endpoint', [ $this, 'loyalty_points_page_endpoint_content' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method store_credit_in_my_account_page
	 * @return mixed
	 * @since 1.0.0
	 * Show 'Loyalty Points' tab in the 'My Account' page
	 */
	public function loyalty_points_in_my_account_page( $all_menu_links )
	{
		$all_menu_links = array_slice( $all_menu_links, 0, 7, true )
			+ [ 'loyalty-points' => esc_html__( 'Loyalty Points', 'hex-coupon-for-woocommerce' ) ]
			+ array_slice( $all_menu_links, 6, NULL, true );

		return $all_menu_links;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method store_credit_menu_page_endpoint
	 * @return mixed
	 * @since 1.0.0
	 * Register 'loyalty-points' endpoint
	 */
	public function loyalty_points_menu_page_endpoint()
	{
		return add_rewrite_endpoint( 'loyalty-points', EP_PAGES );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method store_credit_page_endpoint_content
	 * @return void
	 * @since 1.0.0
	 * Show content in the 'loyalty points' endpoint
	 */
	public function loyalty_points_page_endpoint_content()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'hex_loyalty_program_points';

		$user_id = get_current_user_id();


		$current_points = $wpdb->get_var( $wpdb->prepare(
			"SELECT points FROM $table_name WHERE user_id = %d",
			$user_id
		) );

		// If no entry exists, default the current points to 0
		$current_points = $current_points !== null ? intval( $current_points ) : 0;

		echo '<p>' . sprintf( esc_html__( 'You have %d reward points.', 'text-domain' ), $current_points ) . '</p>';
		?>
		vai go vai
		<?php
	}
}
