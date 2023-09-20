<?php

namespace HexCoupon\App\Controllers;

use HexCoupon\App\Core\Lib\SingleTon;
use Kathamo\Framework\Lib\Controller;

class RestApiController extends Controller
{
	use SingleTon;

	private $base_url = 'hexcoupon/v1/';

	/**
	 * Register hooks callback
	 *
	 * @return void
	 */
	public function register()
	{
		add_action('wp_ajax_coupon_data', [$this,'custom_get_post_titles']);
	}


	public function custom_get_post_titles()
	{

		// Check the nonce and action
		if (isset($_GET['nonce']) && !empty($_GET['nonce']) && wp_verify_nonce($_GET['nonce'],'hexCuponData-react_none') ==1) {
			// Nonce is valid, proceed with your code
			// ...

			wp_send_json([
				// Your response data here
				'msg' => __('hello'),
				'type' => 'success',
				'created' => 104

			], 200);
		} else {
			// Nonce verification failed, handle the error
			wp_send_json([
				'error' => 'Nonce verification failed',
			], 403); // 403 Forbidden status code
		}

	}

}
