<?php

namespace hexcoupon\app\Controllers\WooCommerce\Admin;

use HexCoupon\App\Controllers\BaseController;
use hexcoupon\app\Core\Helpers\ValidationHelper;
use HexCoupon\App\Core\Lib\SingleTon;
use Kathamo\Framework\Lib\Http\Request;
class CouponColumTabController extends BaseController
{
	use SingleTon;

	public function register(){
		add_action( 'save_post', [ $this, 'save_coupon_meta_data' ] );
	}


	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_coupon_meta_data	 *
	 * @param int $post_id Coupon post ID.
	 * @return void
	 *  Save the coupon custom meta-data when the coupon is updated.
	 */
	public function save_coupon_meta_data( $post_id )
	{

		//permitted_roles will not present if no data selected
		$validator = $this->validate([
			'permitted_roles' => 'array'
		]);
		$error = $validator->error();
		if ($error) {
			//todo show flash validation message
		}
		$data = $validator->getData();
		update_post_meta( $post_id, 'permitted_roles', $data['permitted_roles']);
	}
}
