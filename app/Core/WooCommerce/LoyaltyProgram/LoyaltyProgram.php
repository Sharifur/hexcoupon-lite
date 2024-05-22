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
		add_action( 'init', [ $this, 'register_endpoints' ] );
		// Action hook for displaying 'Loyalty Points' page content
		add_action( 'woocommerce_account_loyalty-points_endpoint', [ $this, 'loyalty_points_page_endpoint_content' ] );
		add_action( 'woocommerce_account_loyalty-points-logs_endpoint', [ $this, 'loyalty_points_logs_page_endpoint_content' ] );
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
	 * @return void
	 * @since 1.0.0
	 * Register 'loyalty-points' endpoint
	 */
	public function register_endpoints()
	{
		add_rewrite_endpoint( 'loyalty-points', EP_PAGES );
		add_rewrite_endpoint( 'loyalty-points-logs', EP_PAGES );
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

		// Creating referral link for each user
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$site_url = get_site_url();
			$referral_link = $site_url . "?ref=" . $user_id;
		}
		$logs_page_url = get_site_url() . '/my-account/loyalty-points-logs'
		?>
		<div class="referral-top-bar">
			<div class="current-points"><?php printf( esc_html__( 'Points earned so far: %s', 'hex-coupon-for-woocommerce' ), esc_html( $current_points ) ); ?></div>
			<div class="points-log-link"><a href="<?php echo esc_url( $logs_page_url ); ?>"><?php esc_html_e( 'View Log', 'hex-coupon-for-woocommerce' ); ?></a></div>
		</div>
		<div class="referral-container">
			<h2><?php esc_html_e( 'Referral Link', 'hex-coupon-for-woocommerce' ); ?></h2>
			<div class="referral-box">
				<input type="text" id="referral-link" value="<?php echo esc_url( $referral_link ); ?>" readonly>
				<button class="copy-referral-link"><?php esc_html_e( 'Copy', 'hex-coupon-for-woocommerce' ); ?></button>
			</div>
		</div>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method loyalty_points_logs_page_endpoint_content
	 * @return void
	 * @since 1.0.0
	 * Show content in the 'loyalty points logs' endpoint
	 */
	public function loyalty_points_logs_page_endpoint_content()
	{
	?>
	<div class="loyalty-points-log">
		<h2>Loyalty Points Log</h2>
		<table class="loyalty-points-log-table">
			<thead>
			<tr>
				<th><?php echo esc_html__( 'Points', 'hex-coupon-for-woocommerce' ); ?></th>
				<th><?php echo esc_html__( 'Reason', 'hex-coupon-for-woocommerce' ); ?></th>
				<th><?php echo esc_html__( 'Referer ID', 'hex-coupon-for-woocommerce' ); ?></th>
				<th><?php echo esc_html__( 'Date', 'hex-coupon-for-woocommerce' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<!-- Example log entries -->
			<tr>
				<td>50</td>
				<td>Purchase</td>
				<td>10</td>
				<td>2024-05-20</td>
			</tr>
			<tr>
				<td>100</td>
				<td>Referral</td>
				<td>11</td>
				<td>2024-05-18</td>
			</tr>
			<!-- Add your log entries here -->
			</tbody>
		</table>
	</div>
	<?php
	}
}
