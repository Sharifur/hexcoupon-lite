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
		// Action hook for adding 'All Coupons' menu page in the 'My Account' Page Menu
		add_filter ( 'woocommerce_account_menu_items', [ $this, 'coupon_menu_in_my_account_page' ], 99, 1 );
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
	 * Adds a menu called 'All Coupons' in the 'My account' menu page.
	 */
	public function coupon_menu_in_my_account_page( $all_menu_links )
	{
		$all_menu_links = array_slice( $all_menu_links, 0, 5, true )
			+ [ 'all-coupons' => esc_html__( 'All Coupons', 'hex-coupon-for-woocommerce' ) ]
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
		return add_rewrite_endpoint( 'all-coupons', EP_ROOT | EP_PAGES );
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
			'post_type' => 'shop_coupon',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'orderby' => 'name',
			'order' => 'asc',
		] );

		if( $coupon_posts ) {
			foreach ( $coupon_posts as $coupon_post ) {
				$expiry_date = get_post_meta( $coupon_post->ID, 'date_expires', true );

				if ( $expiry_date ) {
					$real_expiry_date = date( 'Y-m-d', $expiry_date ); // Convert expiry date to a readable format
					$current_date = date( 'Y-m-d' ); // Get current date in the same format

					$coupon_description = get_post_field( 'post_excerpt', $coupon_post->ID );

					// Check if the expiry date has passed
					if ( $real_expiry_date > $current_date ) {
						?>
						<div class="discount-card">
							<div class="discount-info">
								<div class="discount-rate">
									20<span>%</span> <br> DISCOUNT
								</div>
								<div class="discount-details">
									<p><?php printf( esc_html__( '%s ', 'hex-coupon-for-woocommerce' ),  esc_html( $coupon_description ) ); ?></p>
									<div class="discount-code">
										<span class="icon">🎟️</span>
										<span class="code"><?php printf( esc_html__( '%s ', 'hex-coupon-for-woocommerce' ),  esc_html( $coupon_post->post_title ) ); ?></span>
									</div>
									<div class="discount-expiry">
										<span class="icon">⏰</span>
										<span class="date"><?php printf( esc_html__( 'Expiry Date:', 'hex-coupon-for-woocommerce' ), esc_html( $real_expiry_date ) ); ?></span>
									</div>
								</div>
							</div>
						</div>
						<?php
					} else {
						continue;
					}
				}
			}
		} else {
			echo esc_html__( 'No coupon found', 'hex-coupon-for-woocommerce' );
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method coupon_page_endpoint_content
	 * @return string
	 * @since 1.0.0
	 * Display the content in the 'All Coupons' menu page, the contents are all available coupon names.
	 */
	public function coupon_page_endpoint_content()
	{
		?>
		<header class="woocommerce-Address-title title">
			<h3><?php echo esc_html__( 'All Available Coupons', 'hex-coupon-for-woocommerce' ); ?></h3>
		</header>
		<h4>Active Coupons</h4>
		<h4>Expired Coupons</h4>
		<h4>Upcoming Coupons</h4>
		<?php
		$this->all_coupon_list();
	}
}
