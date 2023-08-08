<?php

namespace hexcoupon\app\Controllers\WooCommerce\Admin;

use HexCoupon\App\Controllers\BaseController;
use hexcoupon\app\Core\Helpers\ValidationHelper;
use HexCoupon\App\Core\Lib\SingleTon;
use Kathamo\Framework\Lib\Http\Request;

class CouponGeographicRestrictionTabController extends BaseController
{
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
	 * Save the coupon geographic restriction meta-data.
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
	 * @method save_coupon_all_meta_data
	 * @param int $coupon_id.
	 * @return mixed
	 * @since 1.0.0
	 * Save the coupon user roles custom meta-data when the coupon is updated.
	 */
	public function save_coupon_all_meta_data( $coupon_id )
	{
		// Save coupon restricted shipping zones meta field data
		$meta_fields_data = [
			[ 'apply_geographic_restriction', 'string' ],
			[ 'restricted_shipping_zones', 'array' ],
			[ 'restricted_countries', 'array' ],
		];

		foreach ( $meta_fields_data as $meta_field_data ) {
			$this->save_coupon_meta_data( $meta_field_data[0], $meta_field_data[1], $coupon_id );
		}
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @method apply_selected_payments_method_to_coupon
	 * @param bool $valid
	 * @param object $coupon
	 * @since 1.0.0
	 * @return bool
	 * Apply coupon to user selected payment methods only.
	 */
	private function restrict_selected_shipping_zones_to_coupon( $valid, $coupon )
	{
		$apply_geographic_restriction = get_post_meta( $coupon->get_id(), 'apply_geographic_restriction', true );
		$selected_restricted_shipping_zones = get_post_meta( $coupon->get_id(), 'restricted_shipping_zones', true );

		global $woocommerce;

		$billing_city = $woocommerce->customer->get_billing_city();

		if ( 'restrict_by_shipping_zones' === $apply_geographic_restriction ) {
			if ( empty( $selected_restricted_shipping_zones ) ) {
				return $valid;
			}

			if ( in_array( $billing_city, $selected_restricted_shipping_zones ) ) {
				return false;
			}
		}

	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @method apply_selected_payments_method_to_coupon
	 * @param bool $valid
	 * @param object $coupon
	 * @since 1.0.0
	 * @return bool
	 * Apply coupon to user selected payment methods only.
	 */
	private function restrict_selected_shipping_countries( $valid, $coupon )
	{
		$apply_geographic_restriction = get_post_meta( $coupon->get_id(), 'apply_geographic_restriction', true );
		$selected_restricted_shipping_countries = get_post_meta( $coupon->get_id(), 'restricted_countries', true );

		global $woocommerce;

		$billing_country = $woocommerce->customer->get_billing_country();

		if ( 'restrict_by_countries' === $apply_geographic_restriction ) {
			if ( empty( $selected_restricted_shipping_countries ) ) {
				return $valid;
			}

			if ( in_array( $billing_country, $selected_restricted_shipping_countries ) ) {
				return false;
			}
		}

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
		$restricted_shipping_zones = $this->restrict_selected_shipping_zones_to_coupon( $valid, $coupon );
		$restrict_shipping_countries = $this->restrict_selected_shipping_countries( $valid, $coupon );

		var_dump($restricted_shipping_zones);
		echo '<br>';

		if ( ! is_null( $restricted_shipping_zones )  ) {
			if ( ! $restricted_shipping_zones ) {
				echo 'shipping zone area is returning false because if shipping address is matched with the selected restricted area from coupon single page it will return false.  <br>';
				return false;
			}
		}

		if ( ! is_null( $restrict_shipping_countries ) ) {
			if ( ! $restrict_shipping_countries ) {
				echo 'shipping zone country is returning false because if shipping address is matched with the selected restricted country from coupon single page it will return false.  <br>';
				return false;
			}
		}

		if ( is_null( $restricted_shipping_zones ) || is_null( $restrict_shipping_countries ) ) {
			echo 'coupon is valid coz shipping zone and country is returning null <br>';
			return $valid;
		}

		return $valid;
	}
}
