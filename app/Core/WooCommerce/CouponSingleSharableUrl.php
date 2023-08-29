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
			'label'    => esc_html__( 'Sharable URL coupon', 'hexcoupon' ),
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
		$apply_automatic_coupon_by_url = get_post_meta( $post->ID, 'apply_automatic_coupon_by_url', true );

		echo '<div id="sharable_url_coupon_tab" class="panel woocommerce_options_panel sharable_url_coupon_tab">';

		woocommerce_wp_checkbox(
			[
				'id' => 'apply_automatic_coupon_by_url',
				'label' => '',
				'description' => esc_html__( 'Check this box to allow customers automatically apply the current coupon by visiting a URL', 'hexcoupon' ),
				'value' => $apply_automatic_coupon_by_url,
			]
		);

		$coupon_id = isset( $_GET['coupon_id'] ) ? intval( $_GET['coupon_id'] ) : 0;

		$coupon_code = get_the_title( $coupon_id ); // get the coupon code title

		$sharable_url = sanitize_url( get_site_url() . '/' . '?coupon_code=' . $coupon_code );

		woocommerce_wp_text_input(
			[
				'id' => 'sharable_url',
				'label' => esc_html__( 'Edit URL link', 'hexcoupon' ),
				'desc_tip' => true,
				'description' => esc_html__( 'Please update the page to implement the url and afterwards copy the url and give to the users.', 'hexcoupon' ),
				'type' => 'text',
				'value' => $sharable_url,
				'class' => 'sharable-url form-control',
				'placeholder' => esc_html( 'coupon/20%discount' ),
				'data_type' => 'url',
			]
		);
		?>

		<p class="output-url-text"><span><?php echo esc_url ( $sharable_url ); ?></span></p>
		<p class="copy-sharable-url"><?php echo esc_html__( 'Copy URL', 'hexcoupon' ); ?></p>

		<?php
		// get 'message_for_coupon_discount_url' meta field data
		$message_for_coupon_discount_url = get_post_meta( $post->ID, 'message_for_coupon_discount_url', true );

		woocommerce_wp_textarea_input(
			[
				'id' => 'message_for_coupon_discount_url',
				'label' => '',
				'desc_tip' => true,
				'description' => esc_html__( 'Set a message for customers about the coupon discount they got.', 'hexcoupon' ),
				'placeholder' => esc_html__( 'Message for customer e.g. Congratulations you got 20% discount.', 'hexcoupon' ),
				'value' => $message_for_coupon_discount_url,
			]
		);

		// get 'apply_redirect_sharable_link' meta field data
		$apply_redirect_sharable_link = get_post_meta( $post->ID, 'apply_redirect_sharable_link', true );

		echo '<div class="options_group redirect_url">';

		woocommerce_wp_radio(
			[
				'id' => 'apply_redirect_sharable_link',
				'label' => '',
				'options' => [
					'redirect_to_custom_link' => esc_html__( 'Redirect to a custom URL', 'hexcoupon' ),
					'redirect_back_to_origin' => esc_html__( 'Redirect back to original', 'hexcoupon' ),
				],
				'value' => $apply_redirect_sharable_link,
			]
		);

		// get 'redirect_link' meta field data
//		$redirect_link = get_post_meta( $post->ID, 'redirect_link', true );
//
//		woocommerce_wp_text_input(
//			[
//				'id' => 'redirect_link',
//				'label' => esc_html__( 'Enter redirect URL', 'hexcoupon' ),
//				'desc_tip' => true,
//				'description' => esc_html__( 'Set the coupon redirect url link. E.g: http://yourwebsite.com/cart or http://yourwebsite.com/checkout', 'hexcoupon' ),
//				'type' => 'text',
//				'value' => $redirect_link,
//				'class' => 'redirect_link form-control',
//				'placeholder' => esc_html( 'https://www.example.com/cart' ),
//				'data_type' => 'url',
//			]
//		);

		$redirect_link = get_post_meta( $post->ID, 'redirect_link', true );
		$redirect_link = ! empty( $redirect_link ) ? $redirect_link : '';

		$cart_url = wc_get_cart_url();
		$checkout_url = wc_get_checkout_url();

		// Adding coupon type select input field
		woocommerce_wp_select( [
			'class' => 'select short',
			'label' => esc_html__( 'Enter redirect URL', 'hexcoupon' ),
			'id' => 'redirect_link',
			'name' => 'redirect_link',
			'options' => [
				esc_url( $cart_url ) => 'Cart Page',
				esc_url( $checkout_url ) => 'Checkout Page',
			],
			'value' => $redirect_link,
		] );

		echo '</div></div>';
	}
}
