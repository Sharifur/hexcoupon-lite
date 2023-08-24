<?php
namespace hexcoupon\app\Controllers\WooCommerce\Admin;

use HexCoupon\App\Controllers\BaseController;
use hexcoupon\app\Core\Helpers\ValidationHelper;
use HexCoupon\App\Core\Lib\SingleTon;
use Kathamo\Framework\Lib\Http\Request;

class CouponSharableUrlTabController extends BaseController {

	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method register
	 * @return void
	 * Add all hooks that are needed for 'Coupon Sharable URL' tab
	 */
	public function register()
	{
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this, 'save_coupon_all_meta_data' ] );
//		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'apply_coupon_meta_data' ], 10, 2 );
		add_action( 'wp_loaded', [ $this, 'apply_coupon_activation_via_url' ] );
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this,'delete_post_meta' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method save_coupon_meta_data
	 * @param string $key
	 * @param string $data_type
	 * @param int $post_id
	 * @return void
	 * Save the coupon sharable url meta-data.
	 */
	private function save_coupon_meta_data( $key, $data_type, $post_id )
	{
		$validator = $this->validate( [
			$key => $data_type
		] );

		$error = $validator->error();
		if ( $error ) {

		}
		$data = $validator->getData();

		update_post_meta( $post_id, $key, $data[$key] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method save_coupon_all_meta_data
	 * @param int $coupon_id post ID of Coupon.
	 * @return void
	 * Save the coupon sharable url custom meta-data when the coupon is updated.
	 */
	public function save_coupon_all_meta_data( $coupon_id )
	{
		// assign all the meta key and their data type
		$meta_data_list = [
			[ 'apply_automatic_coupon_by_url', 'string' ],
			[ 'sharable_url', 'string' ],
			[ 'message_for_coupon_discount_url', 'string' ],
			[ 'apply_redirect_sharable_link', 'string' ],
			[ 'redirect_link', 'string' ],
		];

		foreach ( $meta_data_list as $meta_data ) {
			$this->save_coupon_meta_data( $meta_data[0], $meta_data[1], $coupon_id ); // finally save the meta values
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method apply_coupon_activation_via_url
	 * @return mixed
	 * Apply coupon automatically after visiting a custom url.
	 */
	public function apply_coupon_activation_via_url()
	{
		if ( isset( $_GET['coupon_code'] ) ) {
			$coupon_code = sanitize_text_field( $_GET['coupon_code'] ); // get coupon code from the url
			$coupon = new \WC_Coupon( $coupon_code );

			$redirect_link = get_post_meta( $coupon->get_id(), 'redirect_link', true );

			$apply_redirect_sharable_link = get_post_meta( $coupon->get_id(), 'apply_redirect_sharable_link', true );

			if ( 'redirect_back_to_origin' === $apply_redirect_sharable_link ) {
				// Get the referring URL
				$redirect_link = wp_get_referer();

				// If there's no referring URL or, it's the current page, redirect to the home page
				if (!$redirect_link || $redirect_link === get_permalink()) {
					$redirect_link = home_url();
				}
			}

			// Check is the coupon valid or not
			$discounts = new \WC_Discounts( WC()->cart );
			$response = $discounts->is_coupon_valid( $coupon );

			// Check if the given url has the right coupon code
			$sharable_url = get_post_meta( $coupon->get_id(), 'sharable_url', true );
			$coupon_code_search = str_contains( $sharable_url, 'coupon_code=' . $coupon_code );

			if ( $coupon_code_search ) {
				if ( $response ) {
					WC()->cart->apply_coupon( $coupon_code );
					wp_safe_redirect( $redirect_link );
					exit();
				}
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method delete_post_meta
	 * @param int $coupon_id
	 * @return void
	 * Delete post meta-data of Sharable url coupon tab.
	 */
	public function delete_post_meta( $coupon_id )
	{
		$apply_redirect_sharable_link = get_post_meta( $coupon_id, 'apply_redirect_sharable_link', true );

		// check if redirect sharable link matches with 'redirect_back_to_origin' or not
		if ( 'redirect_back_to_origin'  === $apply_redirect_sharable_link ) {
			delete_post_meta( $coupon_id, 'redirect_link' );
		}
	}

}
