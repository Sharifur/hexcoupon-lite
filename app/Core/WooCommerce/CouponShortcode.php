<?php
namespace HexCoupon\App\Core\WooCommerce;

use HexCoupon\App\Core\Lib\SingleTon;

class CouponShortcode
{
	use singleton;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return mixed
	 * @since 1.0.0
	 * Registers all hooks that are needed to create 'Coupon' shortcode functionality.
	 */
	public function register()
	{
		add_filter( 'manage_edit-shop_coupon_columns', [ $this, 'custom_coupon_list_table_columns' ] );
		add_action( 'manage_shop_coupon_posts_custom_column', [ $this, 'custom_coupon_list_table_column_values' ], 10, 2);
		if ( class_exists( 'WooCommerce' ) ) {
			add_shortcode('hexcoupon', [ $this, 'display_coupon_info_shortcode' ] );
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method display_coupon_info_shortcode
	 * @param $atts
	 * @return mixed
	 * @since 1.0.0
	 * Creates a shortcode for the coupon.
	 */
	public function display_coupon_info_shortcode ( $atts )
	{
		// Shortcode attributes (if provided) or default values.
		$atts = shortcode_atts( [
			'code' => '', // Coupon code to display information for.
		], $atts );

		// Check if the 'code' attribute is provided.
		if ( empty( $atts['code'] ) ) {
			return esc_html__( 'Provide a coupon code.', 'hex-coupon-for-woocommerce' );
		}

		// Get the coupon object using the provided coupon code.
		$coupon = new \WC_Coupon( $atts['code'] );

		// Get coupon information.
		$coupon_code = $coupon->get_code();
		$coupon_description = $coupon->get_description();
		$coupon_discount_type = $coupon->get_discount_type();
		$coupon_amount = wc_price( $coupon->get_discount_amount( $coupon->get_amount() ) );

		$discount_type = '';

		switch ( $coupon_discount_type ) {
			case 'percent' :
				$discount_type = 'Percentage Discount';
				break;
			case 'fixed_cart' :
				$discount_type = 'Fixed Cart Discount';
				break;
			case 'fixed_product' :
				$discount_type = 'Fixed Product Discount';
				break;
				case 'buy_x_get_x_bogo':
					$discount_type = 'Bogo Discount';
		}

		$allowed_html  = [
			'a' => [
				'href' => [],
			],
			'p' => [],
			'b' => [

			]
		];

		// Build the HTML output for the coupon information.
		$output = '<div class="hexcoupon-shortcode-banner">';
		$output .= '<p class="coupon-code">' . esc_html__( 'Coupon Code: ', 'hex-coupon-for-woocommerce' ) . '<span>' . sprintf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $coupon_code ) ) . '</span></p>';
		$output .= '<p class="coupon-discount">' . esc_html__( 'Coupon Type: ', 'hex-coupon-for-woocommerce' ) . '<span>' . sprintf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $discount_type ) ) . '</span></p>';
		$output .= '<p class="coupon-description">' . esc_html__( 'Description: ', 'hex-coupon-for-woocommerce' ) . '<span>' . sprintf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $coupon_description ) ) . '</span></p>';
		$output .= '<p <p class="coupon-amount">' . esc_html__( 'Discount Amount: ', 'hex-coupon-for-woocommerce' ) . '<span>' . wp_kses( $coupon_amount, $allowed_html ) . '</span></p>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method display_coupon_info_shortcode
	 * @param $coupon_code
	 * @return mixed
	 * @since 1.0.0
	 * Creates a shortcode for the coupon.
	 */
	public function generate_coupon_shortcode( $coupon_code )
	{
		return '[hexcoupon code="' . esc_attr( $coupon_code ) . '"]';
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method custom_coupon_list_table_columns
	 * @param $columns
	 * @return mixed
	 * @since 1.0.0
	 * Creates a shortcode for the coupon.
	 */
	public function custom_coupon_list_table_columns( $columns )
	{
		$columns['coupon_shortcode'] = esc_html__( 'Shortcode', 'hex-coupon-for-woocommerce' );
		return $columns;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method custom_coupon_list_table_column_values
	 * @param $column
	 * @param $coupon_id
	 * @return mixed
	 * @since 1.0.0
	 * Creates a shortcode for the coupon.
	 */
	public function custom_coupon_list_table_column_values( $column, $coupon_id )
	{
		if ( 'coupon_shortcode' === $column ) {
			$shortcode = $this->generate_coupon_shortcode( $coupon_id );
			?>
			<input type="text" readonly="readonly" class="shortcode_column" value="<?php echo esc_attr( $shortcode ); ?>" />
			<?php
		}
	}
}
