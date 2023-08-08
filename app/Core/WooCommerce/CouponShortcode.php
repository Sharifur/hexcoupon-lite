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
		add_shortcode('hexcoupon_info', [ $this, 'display_coupon_info_shortcode' ] );
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
			return esc_html__( 'Please provide a coupon code.', 'hexcoupon' );
		}

		// Get the coupon object using the provided coupon code.
		$coupon = new \WC_Coupon( $atts['code'] );

		// Check if the coupon exists and is valid.
//		if( $coupon->get_date_expires() ) {
//			return esc_html__( 'Invalid or expired coupon code.', 'hexcoupon' );
//		}

		// Get coupon information.
		$coupon_code = $coupon->get_code();
		$coupon_description = $coupon->get_description();
		$coupon_discount_type = $coupon->get_discount_type();
		$coupon_amount = $coupon->get_amount();
		$coupon_discount = $coupon_amount . ( $coupon_discount_type === 'percent' ? '%' : get_woocommerce_currency_symbol() );

		// Build the HTML output for the coupon information.
		$output = '<div class="coupon-info">';
		$output .= '<p>' . esc_html__( 'Coupon Code: ', 'hexcoupon' ) . esc_html( $coupon_code ) . '</p>';
		$output .= '<p>' . esc_html__( 'Description: ', 'hexcoupon' ) . esc_html( $coupon_description ) . '</p>';
		$output .= '<p>' . esc_html__( 'Discount: ', 'hexcoupon' ) . esc_html( $coupon_discount ) . '</p>';
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
		return '[hexcoupon_info code="' . esc_attr( $coupon_code ) . '"]';
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
		$columns['coupon_shortcode'] = esc_html__( 'Shortcode', 'hexcoupon' );
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
