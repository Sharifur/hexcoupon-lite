<?php
namespace HexCoupon\App\Core\WooCommerce;

use HexCoupon\App\Core\Lib\SingleTon;
use function Symfony\Component\VarDumper\Dumper\esc;

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
		if ( is_user_logged_in() ) {
			$this->user = wp_get_current_user();
		}
		$roles = $this->user->roles;
		$roles = $roles[0];

		$coupon_posts = get_posts( [
			'post_type' => 'shop_coupon',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'orderby' => 'name',
			'order' => 'asc',
			'meta_key' => 'permitted_roles',
			'meta_value' => $roles,
			'meta_compare' => 'LIKE'
		] );

		foreach ( $coupon_posts as $coupon_post ) {
			?>
				<P>
				<?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ),  esc_html( $coupon_post->post_title ) ); ?>
				</P>
			<?php
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
		<?php
		$this->all_coupon_list();
	}
}
