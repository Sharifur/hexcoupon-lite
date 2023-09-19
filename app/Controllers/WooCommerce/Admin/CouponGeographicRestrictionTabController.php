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
	 * Add all hooks that are needed for 'Geographic restriction' tab
	 */
	public function register()
	{
		add_action( 'wp_loaded', [ $this, 'get_all_post_meta' ] );
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this, 'save_coupon_all_meta_data' ] );
		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'apply_coupon_meta_data' ], 10, 2 );
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this,'delete_post_meta' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method get_all_post_meta
	 * @param int $coupon
	 * @return array
	 * Get all coupon meta values
	 */
	public function get_all_post_meta( $coupon )
	{
		$all_meta_data = get_post_meta( $coupon, 'geographic_restriction', true );

		return $all_meta_data;
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
	 * Save the coupon geographic restriction all meta-data.
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
	 * @param int $coupon_id.
	 * @return void
	 * Save the coupon geographic restriction meta-field data.
	 */
	public function save_coupon_all_meta_data( $coupon_id )
	{
		// Assign all meta fields key and their data type
		$this->save_coupon_meta_data( 'geographic_restriction', 'array', $coupon_id );
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @since 1.0.0
	 * @method restrict_selected_shipping_zones_to_coupon
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * Apply coupon to selected shipping zones only.
	 */
	private function restrict_selected_shipping_zones_to_coupon( $valid, $coupon )
	{
		global $woocommerce;

		$all_meta_data = $this->get_all_post_meta( $coupon->get_id() ); // get the meta values

		$all_cities = ! empty( $all_meta_data['restricted_shipping_zones'] ) ? $all_meta_data['restricted_shipping_zones'] : [];

		$all_cities = implode( ',', $all_cities );

		$billing_city = $woocommerce->customer->get_billing_city(); // get the current billing city of the user

		if ( ! empty( $all_meta_data['apply_geographic_restriction'] ) && 'restrict_by_shipping_zones' === $all_meta_data['apply_geographic_restriction'] ) {
			if ( empty( $all_meta_data['restricted_shipping_zones'] ) ) {
				echo 'hello true';
				return $valid;
			}

			if ( str_contains( $all_cities, $billing_city ) ) {
				echo 'hello false';
				return false;
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @since 1.0.0
	 * @method restrict_selected_shipping_countries
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * Apply coupon to selected countries only.
	 */
	private function restrict_selected_shipping_countries( $valid, $coupon )
	{
		global $woocommerce;

		$all_meta_data = $this->get_all_post_meta( $coupon->get_id() ); // get all meta values

		$billing_country = $woocommerce->customer->get_billing_country();

		if ( ! empty( $all_meta_data['apply_geographic_restriction'] ) && 'restrict_by_countries' === $all_meta_data['apply_geographic_restriction'] ) {
			if ( empty( $all_meta_data['restricted_countries'] ) ) {
				return $valid;
			}

			if ( in_array( $billing_country, $all_meta_data['restricted_countries'] ) ) {
				return false;
			}
		}
	}


	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @since 1.0.0
	 * @method apply_coupon_meta_data
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * Apply coupon based on all criteria.
	 */
	public function apply_coupon_meta_data( $valid, $coupon )
	{
		$restricted_shipping_zones = $this->restrict_selected_shipping_zones_to_coupon( $valid, $coupon );

		$restrict_shipping_countries = $this->restrict_selected_shipping_countries( $valid, $coupon );


		if ( ! is_null( $restricted_shipping_zones )  ) {
			if ( ! $restricted_shipping_zones ) {
				// display a custom coupon error message if the coupon is invalid
				add_filter( 'woocommerce_coupon_error', [ $this, 'custom_coupon_error_message_for_shipping_zones' ] , 10, 2 );

				return false;
			}
		}

		if ( ! is_null( $restrict_shipping_countries ) ) {
			if ( ! $restrict_shipping_countries ) {
				// display a custom coupon error message if the coupon is invalid
				add_filter( 'woocommerce_coupon_error', [ $this, 'custom_coupon_error_message_for_shipping_countries' ] , 10, 2 );

				return false;
			}
		}

		if ( is_null( $restricted_shipping_zones ) || is_null( $restrict_shipping_countries ) ) {
			return $valid;
		}

		return $valid;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method delete_post_meta
	 * @param int $coupon_id
	 * @return mixed
	 * @since 1.0.0
	 * Delete post meta-data of Geographic restriction tab.
	 */
	public function delete_post_meta( $coupon_id )
	{
		$all_meta_data = $this->get_all_post_meta( $coupon_id ); // get all meta values

		if ( ! empty( $all_meta_data['apply_geographic_restriction'] ) && 'restrict_by_shipping_zones'  === $all_meta_data['apply_geographic_restriction'] ) {
			if( ! empty( $all_meta_data ) && is_array( $all_meta_data ) ) {
				unset( $all_meta_data['restricted_countries'] );

				// Update the post meta with the modified array
				update_post_meta( $coupon_id, 'geographic_restriction', $all_meta_data );
			}
		} else {
			if( ! empty( $all_meta_data ) && is_array( $all_meta_data ) ) {
				unset( $all_meta_data['restricted_shipping_zones'] );
				// Update the post meta with the modified array
				update_post_meta( $coupon_id, 'geographic_restriction', $all_meta_data );
			}
		}

	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @since 1.0.0
	 * @method custom_coupon_error_message_for_shipping_zones
	 * @param string $err
	 * @param int $err_code
	 * @return string
	 * Display custom error message for invalid coupon.
	 */
	public function custom_coupon_error_message_for_shipping_zones( $err, $err_code )
	{
		if ( $err_code === 100 ) {
			// Change the error message for the INVALID_FILTERED error here
			$err = esc_html__( 'Invalid coupon. Your shipping zone does not support this coupon.', 'hexcoupon');
		}

		return $err;
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @since 1.0.0
	 * @method custom_coupon_error_message_for_shipping_countries
	 * @param string $err
	 * @param int $err_code
	 * @return string
	 * Display custom error message for invalid coupon.
	 */
	public function custom_coupon_error_message_for_shipping_countries( $err, $err_code )
	{
		if ( $err_code === 100 ) {
			// Change the error message for the INVALID_FILTERED error here
			$err = esc_html__( 'Invalid coupon. Your country does not support this coupon.', 'hexcoupon');
		}

		return $err;
	}
}
