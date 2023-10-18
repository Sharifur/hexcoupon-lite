<?php
namespace HexCoupon\App\Core\WooCommerce;

use HexCoupon\App\Core\Helpers\FormHelpers;
use HexCoupon\App\Core\Helpers\RenderHelpers;
use HexCoupon\App\Core\Lib\SingleTon;

class CouponSingleSharableUrl {

	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method register
	 * @return mixed
	 * Registers all hooks that are needed to create custom tab 'Sharable URL coupon' on 'Coupon Single' page.
	 */
	public function register()
	{
		add_action( 'woocommerce_coupon_data_tabs', [ $this, 'add_sharable_url_coupon_tab' ] );
		add_filter( 'woocommerce_coupon_data_panels', [ $this, 'add_sharable_url_coupon_tab_content' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_sharable_url_coupon_tab
	 * @param array $tabs
	 * @return array
	 * Displays a new tab in the coupon single page called 'Sharable URL coupon'.
	 */
	public function add_sharable_url_coupon_tab( $tabs )
	{
		$tabs['sharable_url_coupon_tab'] = array(
			'label'    => esc_html__( 'Sharable URL coupon', 'hex-coupon-for-woocommerce' ),
			'target'   => 'sharable_url_coupon_tab',
			'class'    => array( 'sharable_url_coupon' ),
		);
		return $tabs;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_sharable_url_coupon_tab_content
	 * @return void
	 * Displays the content of custom tab 'Sharable URL coupon'.
	 */
	public function add_sharable_url_coupon_tab_content()
	{
		// declare the global $post object
		global $post;

		// get 'apply_automatic_coupon_by_url' meta field data
		$sharable_url_coupon = get_post_meta( $post->ID, 'sharable_url_coupon', true );

		$apply_automatic_coupon_by_url = ! empty( $sharable_url_coupon['apply_automatic_coupon_by_url'] ) ? $sharable_url_coupon['apply_automatic_coupon_by_url'] :'' ;

		echo '<div id="sharable_url_coupon_tab" class="panel woocommerce_options_panel sharable_url_coupon_tab">';

		woocommerce_wp_checkbox(
			[
				'id' => 'apply_automatic_coupon_by_url',
				'name' => 'sharable_url_coupon[apply_automatic_coupon_by_url]',
				'label' => '',
				'description' => esc_html__( 'Check this box to allow customers automatically apply the current coupon by visiting a URL', 'hex-coupon-for-woocommerce' ),
				'value' => $apply_automatic_coupon_by_url,
			]
		);

		$coupon_id = isset( $_GET['coupon_id'] ) ? intval( $_GET['coupon_id'] ) : 0;

		$coupon_code = get_the_title( $coupon_id ); // get the coupon code title

		$sharable_url = sanitize_url( get_site_url() . '/' . '?coupon_code=' . $coupon_code );

		woocommerce_wp_text_input(
			[
				'id' => 'sharable_url',
				'name' => 'sharable_url_coupon[sharable_url]',
				'label' => esc_html__( 'Edit URL link', 'hex-coupon-for-woocommerce' ),
				'desc_tip' => true,
				'description' => esc_html__( 'Please update the page to implement the url and afterwards copy the url and give to the users.', 'hex-coupon-for-woocommerce' ),
				'type' => 'text',
				'value' => $sharable_url,
				'class' => 'sharable-url form-control',
				'placeholder' => esc_html( 'coupon/20%discount' ),
				'data_type' => 'url',
			]
		);
		?>

		<p class="output-url-text"><span><?php echo esc_url ( $sharable_url ); ?></span></p>
		<p class="copy-sharable-url"><?php echo esc_html__( 'Copy URL', 'hex-coupon-for-woocommerce' ); ?></p>

		<?php
		// get 'message_for_coupon_discount_url' meta field data
		$message_for_coupon_discount_url = ! empty( $sharable_url_coupon['message_for_coupon_discount_url'] ) ? $sharable_url_coupon['message_for_coupon_discount_url'] : '';

		woocommerce_wp_textarea_input(
			[
				'id' => 'message_for_coupon_discount_url',
				'name' => 'sharable_url_coupon[message_for_coupon_discount_url]',
				'label' => '',
				'desc_tip' => true,
				'description' => esc_html__( 'Set a message for customers about the coupon discount they got.', 'hex-coupon-for-woocommerce' ),
				'placeholder' => esc_html__( 'Message for customer e.g. Congratulations you got 20% discount.', 'hex-coupon-for-woocommerce' ),
				'value' => $message_for_coupon_discount_url,
			]
		);

		// get 'apply_redirect_sharable_link' meta field data
		$apply_redirect_sharable_link = ! empty( $sharable_url_coupon['apply_redirect_sharable_link'] ) ? $sharable_url_coupon['apply_redirect_sharable_link'] : '';

		echo '<div class="options_group redirect_url">';

		woocommerce_wp_radio(
			[
				'id' => 'apply_redirect_sharable_link',
				'name' => 'sharable_url_coupon[apply_redirect_sharable_link]',
				'label' => '',
				'options' => [
					'redirect_to_custom_link' => esc_html__( 'Redirect to a custom URL', 'hex-coupon-for-woocommerce' ),
					'redirect_back_to_origin' => esc_html__( 'Redirect back to original', 'hex-coupon-for-woocommerce' ),
				],
				'value' => $apply_redirect_sharable_link,
			]
		);

		$redirect_link = ! empty( $sharable_url_coupon['redirect_link'] ) ? $sharable_url_coupon['redirect_link'] : '';

		$cart_url = wc_get_cart_url();
		$checkout_url = wc_get_checkout_url();

		// Adding coupon type select input field
		woocommerce_wp_select( [
			'class' => 'select short',
			'label' => esc_html__( 'Enter redirect URL', 'hex-coupon-for-woocommerce' ),
			'id' => 'redirect_link',
			'name' => 'sharable_url_coupon[redirect_link]',
			'options' => [
				esc_url( $cart_url ) => 'Cart Page',
				esc_url( $checkout_url ) => 'Checkout Page',
			],
			'value' => $redirect_link,
		] );

		echo '</div></div>';
	}
}
