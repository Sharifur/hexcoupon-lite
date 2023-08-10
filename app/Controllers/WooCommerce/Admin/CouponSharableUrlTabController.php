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
	 * @method register
	 * @return mixed
	 * @since 1.0.0
	 * Add all hooks that are needed to save the coupon meta-data and apply it on products
	 */
	public function register()
	{
		add_action( 'save_post', [ $this, 'save_coupon_all_meta_data' ] );
		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'apply_coupon_meta_data' ], 10, 2 );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_coupon_meta_data
	 * @param $key
	 * @param $data_type
	 * @param $post_id
	 * @return mixed
	 * @since 1.0.0
	 * Save the coupon usage restriction meta-data.
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
	 * @method save_coupon_role_meta_data
	 * @param int $post_id post ID of Coupon.
	 * @return mixed
	 * @since 1.0.0
	 * Save the coupon user roles custom meta-data when the coupon is updated.
	 */
	public function save_coupon_all_meta_data( $post_id )
	{
		// Save coupon permitted payment method meta field data
		$this->save_coupon_meta_data( 'apply_automatic_coupon_by_url', 'string', $post_id );

		// Save coupon permitted payment method meta field data
		$this->save_coupon_meta_data( 'sharable_url', 'string', $post_id );

		$this->save_coupon_meta_data( 'message_for_coupon_discount_url', 'string', $post_id );

		$this->save_coupon_meta_data( 'apply_redirect_sharable_link', 'string', $post_id );

		$this->save_coupon_meta_data( 'redirect_link', 'string', $post_id );
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @method apply_coupon_meta_data
	 * @param bool $valid
	 * @param object $coupon
	 * @since 1.0.0
	 * @return bool
	 * Apply coupon based on all criteria.
	 */
	public function apply_coupon_meta_data( $valid, $coupon )
	{

	}
}
