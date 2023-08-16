<?php

namespace hexcoupon\app\Controllers\WooCommerce\Admin;

use HexCoupon\App\Controllers\BaseController;
use hexcoupon\app\Core\Helpers\ValidationHelper;
use HexCoupon\App\Core\Lib\SingleTon;
use Kathamo\Framework\Lib\Http\Request;

class CouponGeneralTabController extends BaseController
{
	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return void
	 * @since 1.0.0
	 * Register hook that is needed to validate the coupon according to the starting date.
	 */
	public function register()
	{
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this, 'save_coupon_general_tab_meta_field_data' ] );
		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'apply_coupon' ], 10, 2 );
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this, 'delete_meta_value' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_meta_data
	 * @param $key
	 * @param $data_type
	 * @param $coupon_id
	 * @return mixed
	 * @since 1.0.0
	 * Save the general tab meta-data.
	 */
	private function save_meta_data( $key, $data_type, $coupon_id )
	{
		$validator = $this->validate( [
			$key => $data_type
		] );
		$error = $validator->error();
		if ( $error ) {

		}
		$data = $validator->getData();

		update_post_meta( $coupon_id, $key, $data[$key] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_dynamic_meta_data
	 * @param $day
	 * @param $key
	 * @param $data_type
	 * @param $coupon_id
	 * @return void
	 * @since 1.0.0
	 * Save the coupon dynamic hours meta-data.
	 */
	private function save_dynamic_meta_data( $day, $key, $data_type, $coupon_id )
	{
		if ( isset( $_POST['total_hours_count_'.$day] ) ) {
			$total_hours_count = intval( $_POST['total_hours_count_'.$day] );
			$total_hours_count = intval( $_POST['total_hours_count_'.$day] );

			// Loop through the input values and save them as post meta
			for ( $i = 1; $i <= $total_hours_count; $i++ ) {
				$validator = $this->validate( [
					$key.$i => $data_type
				] );
				$error = $validator->error();
				if ( $error ) {

				}
				$data = $validator->getData();

				update_post_meta( $coupon_id, $key.$i, $data[$key.$i] );
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_coupon_on_saturay
	 * @param $coupon_id
	 * @return mixed
	 * @since 1.0.0
	 * Save the coupon days and hours option applicability on different days.
	 */
	private function save_coupon_on_different_days( $coupon_id )
	{
		$meta_fields_data = [
			[ 'coupon_apply_on_saturday', 'string' ],
			[ 'coupon_apply_on_sunday', 'string' ],
			[ 'coupon_apply_on_monday', 'string' ],
			[ 'coupon_apply_on_tuesday', 'string' ],
			[ 'coupon_apply_on_wednesday', 'string' ],
			[ 'coupon_apply_on_thursday', 'string' ],
			[ 'coupon_apply_on_friday', 'string' ],
			[ 'discount_type', 'string' ],
			[ 'customer_purchases', 'string' ],
			[ 'add_a_specific_product', 'string' ],
			[ 'add_specific_category', 'string' ],
			[ 'customer_gets_as_free', 'string' ],
			[ 'add_a_specific_product_for_free', 'string' ],
			[ 'bogo_use_limit', 'string' ],
			[ 'automatically_add_bogo_deal_product', 'string' ],
			[ 'display_bogo_button', 'string' ],
		];

		foreach ( $meta_fields_data as $meta_field_data ) {
			if ( ! empty( $meta_field_data[0] ) && ! empty( $meta_field_data[1] ) ) {
				$this->save_meta_data( $meta_field_data[0], $meta_field_data[1], $coupon_id );
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_coupon_start_expiry_time
	 * @param $coupon_id
	 * @return mixed
	 * @since 1.0.0
	 * Save coupon start and expiry time on different days.
	 */
	private function save_coupon_start_expiry_time( $coupon_id )
	{
		$meta_fields_data = [
			[ 'sat_coupon_start_time', 'string' ],
			[ 'sat_coupon_expiry_time', 'string' ],
			[ 'sun_coupon_start_time', 'string' ],
			[ 'sun_coupon_expiry_time', 'string' ],
			[ 'mon_coupon_start_time', 'string' ],
			[ 'mon_coupon_expiry_time', 'string' ],
			[ 'tue_coupon_start_time', 'string' ],
			[ 'tue_coupon_expiry_time', 'string' ],
			[ 'wed_coupon_start_time', 'string' ],
			[ 'wed_coupon_expiry_time', 'string' ],
			[ 'thu_coupon_start_time', 'string' ],
			[ 'thu_coupon_expiry_time', 'string' ],
			[ 'fri_coupon_start_time', 'string' ],
			[ 'fri_coupon_expiry_time', 'string' ],
		];

		foreach ( $meta_fields_data as $meta_field_data ) {
			if ( ! empty( $meta_field_data[0] ) && ! empty( $meta_field_data[1] ) ) {
				$this->save_meta_data( $meta_field_data[0], $meta_field_data[1], $coupon_id );
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_coupon_sat_dynamic_start_time
	 * @param $coupon_id
	 * @return mixed
	 * @since 1.0.0
	 * Apply the coupon dynamic start and expiry hours field value for all days.
	 */
	private function save_coupon_dynamic_start_expiry_time( $coupon_id )
	{
		$meta_fields_data = [
			[ 'saturday', 'sat_coupon_start_time_', 'string' ],
			[ 'saturday', 'sat_coupon_expiry_time_', 'string' ],
			[ 'sunday', 'sun_coupon_start_time_', 'string' ],
			[ 'sunday', 'sun_coupon_expiry_time_', 'string' ],
			[ 'monday', 'mon_coupon_start_time_', 'string' ],
			[ 'monday', 'mon_coupon_expiry_time_', 'string' ],
			[ 'tuesday', 'tue_coupon_start_time_', 'string' ],
			[ 'tuesday', 'tue_coupon_expiry_time_', 'string' ],
			[ 'wednesday', 'wed_coupon_start_time_', 'string' ],
			[ 'wednesday', 'wed_coupon_start_time_', 'string' ],
			[ 'thursday', 'thu_coupon_start_time_', 'string' ],
			[ 'thursday', 'thu_coupon_expiry_time_', 'string' ],
			[ 'friday', 'fri_coupon_start_time_', 'string' ],
			[ 'friday', 'fri_coupon_expiry_time_', 'string' ],
		];

		foreach ( $meta_fields_data as $meta_field_data ) {
			if ( ! empty( $meta_field_data[0] ) && ! empty( $meta_field_data[1] ) && ! empty( $meta_field_data[2] ) ) {
				$this->save_dynamic_meta_data( $meta_field_data[0], $meta_field_data[1], $meta_field_data[2], $coupon_id );
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_coupon_expiry_date_message
	 * @param $coupon_id
	 * @return mixed
	 * @since 1.0.0
	 * Save the coupon expiry date message.
	 */
	public function save_coupon_general_tab_meta_field_data( $coupon_id )
	{
		$meta_fields_data = [
			[ 'message_for_coupon_expiry_date', 'string' ],
			[ 'coupon_starting_date', 'string' ],
			[ 'message_for_coupon_starting_date', 'string' ],
			[ 'apply_days_hours_of_week', 'string' ],
		];

		foreach ( $meta_fields_data as $meta_field_data ) {
			if ( ! empty( $meta_field_data[0] ) && ! empty( $meta_field_data[1] ) ) {
				$this->save_meta_data( $meta_field_data[0], $meta_field_data[1], $coupon_id );
			}
		}

		// Save the coupon days and hours option applicability on different days
		$this->save_coupon_on_different_days( $coupon_id );

		// Save coupon start and expiry time on different days
		$this->save_coupon_start_expiry_time( $coupon_id );

		// Save coupon dynamic start and expiry time on different days
		$this->save_coupon_dynamic_start_expiry_time( $coupon_id );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method apply_coupon_starting_date
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * @since 1.0.0
	 * Apply/validate the coupon starting date.
	 */
	private function apply_coupon_starting_date( $valid, $coupon )
	{
		$current_time = time();

		$coupon_starting_date = get_post_meta( $coupon->get_id(), 'coupon_starting_date', true );
		$coupon_converted_starting_date = strtotime( $coupon_starting_date );

		if ( empty( $coupon_starting_date ) || $current_time >= $coupon_converted_starting_date ) {
			return $valid;
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method apply_day_hours_of_week
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * @since 1.0.0
	 * Apply/validate the coupon days and hours of the week.
	 */
//	private function apply_day_hours_of_week( $valid, $coupon )
//	{
//		$days_hours_of_week = get_post_meta( $coupon->get_id(), 'apply_days_hours_of_week', true );
//		if ( 'yes' == $days_hours_of_week ) {
//			return $valid;
//		}
//	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method apply_to_single_day
	 * @param bool $valid
	 * @param object $coupon
	 * @param string $full_day
	 * @param string $shortDay
	 * @return bool
	 * @since 1.0.0
	 * Apply/validate the coupon on different days of the week.
	 */
	private function apply_to_single_day( $valid, $coupon, $full_day, $shortDay )
	{
		global $day;

		$current_day = date('l');
		$current_server_time = current_time( 'timestamp' );

		$day = get_post_meta( $coupon->get_id(), 'coupon_apply_on_'.$full_day, true );
		if ( '1' === $day ) {
			$day = ucfirst($full_day);
		}

		$coupon_start_time = get_post_meta( $coupon->get_id(), $shortDay.'_coupon_start_time', true );
		$coupon_start_time = strtotime( $coupon_start_time );

		$coupon_expiry_time = get_post_meta( $coupon->get_id(), $shortDay.'_coupon_expiry_time', true );
		$coupon_expiry_time = strtotime( $coupon_expiry_time );

		if ( ! empty( $day ) && $current_day == $day ) {
			if ( ( '' == $coupon_start_time && '' == $coupon_expiry_time ) || ( $current_server_time >= $coupon_start_time && $current_server_time <= $coupon_expiry_time ) ) {
				return $valid;
			}

			$total_hours_count = get_post_meta( $coupon->get_id(), 'total_hours_count', true );

			for ( $i = 1; $i <= $total_hours_count; $i++ ) {
				$additional_start_time = get_post_meta( $coupon->get_id(), $shortDay.'_coupon_start_time_'.$i, true );
				$additional_expiry_time = get_post_meta( $coupon->get_id(), $shortDay.'_coupon_expiry_time_'.$i, true );

				$additional_start_time =  strtotime( $additional_start_time );
				$additional_expiry_time =  strtotime( $additional_expiry_time );

				if (  $current_server_time >= $additional_start_time && $current_server_time <= $additional_expiry_time ) {
					return $valid;
				}
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method apply_coupon_on_different_days
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * @since 1.0.0
	 * Apply/validate the coupon on different days of the week.
	 */
	private function apply_coupon_on_different_days( $valid, $coupon )
	{
		// Apply/validate the coupon on saturday.
		if ( $this->apply_to_single_day( $valid, $coupon, 'saturday', 'sat' ) ) {
			return $valid;
		}

		// Apply/validate the coupon on sunday.
		if ( $this->apply_to_single_day( $valid, $coupon, 'sunday', 'sun' ) ) {
			return $valid;
		}

		// Apply/validate the coupon on monday.
		if ( $this->apply_to_single_day( $valid, $coupon, 'monday', 'mon' ) ) {
			return $valid;
		}

		// Apply/validate the coupon on tuesday.
		if ( $this->apply_to_single_day( $valid, $coupon, 'tuesday', 'tue' ) ) {
			return $valid;
		}

		// Apply/validate the coupon on wednesday.
		if ( $this->apply_to_single_day( $valid, $coupon, 'wednesday', 'wed' ) ) {
			return $valid;
		}

		// Apply/validate the coupon on thursday.
		if ( $this->apply_to_single_day( $valid, $coupon, 'thursday', 'thu' ) ) {
			return $valid;
		}

		// Apply/validate the coupon on friday.
		if ( $this->apply_to_single_day( $valid, $coupon, 'friday', 'fri' ) ) {
			return $valid;
		}

		return false;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method apply_coupon
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * @since 1.0.0
	 * Apply/validate the coupon starting date.
	 */
	public function apply_coupon( $valid, $coupon )
	{
		$days_hours_of_week = get_post_meta( $coupon->get_id(), 'apply_days_hours_of_week', true );

		$apply_coupon_starting_date = $this->apply_coupon_starting_date( $valid, $coupon );
//		$apply_day_hours_of_week = $this->apply_day_hours_of_week( $valid, $coupon );
		$apply_coupon_on_different_days = $this->apply_coupon_on_different_days( $valid, $coupon );

		// Apply coupon
		if ( $apply_coupon_starting_date ) {
			if ( 'yes' === $days_hours_of_week ) {
				if ( is_null( $apply_coupon_on_different_days ) || $apply_coupon_on_different_days ) {
					echo '## apply coupon on different days is returning true because either they are not set or you are on time on different days. <br>';
					return true;
				} else {
					add_filter('woocommerce_coupon_is_valid',function(){

					});
					echo '## apply coupon on different days is returning false because different days time is not matching. <br>';
					return false;
				}
			}

			echo '## coupon starting date is returning '.$apply_coupon_starting_date.'. <br>';
			return true;
		}
		echo '## apply coupon on different days is returning false. <br>';

		return false;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method delete_meta_value
	 * @param int $coupon_id
	 * @return mixed
	 * @since 1.0.0
	 * Delete meta value.
	 */
	public function delete_meta_value( $coupon_id )
	{
		$days_hours_of_week = get_post_meta( $coupon_id, 'apply_days_hours_of_week', true );

		$meta_start_key =  [
			'sat_coupon_start_time',
			'sun_coupon_start_time',
			'mon_coupon_start_time',
			'tue_coupon_start_time',
			'wed_coupon_start_time',
			'thu_coupon_start_time',
			'fri_coupon_start_time'
		];

		$meta_expiry_key = [
			'sat_coupon_expiry_time',
			'sun_coupon_expiry_time',
			'mon_coupon_expiry_time',
			'tue_coupon_expiry_time',
			'wed_coupon_expiry_time',
			'thu_coupon_expiry_time',
			'fri_coupon_expiry_time'
		];

		$days = [
			'saturday' => 'sat_coupon',
			'sunday' => 'sun_coupon',
			'monday' => 'mon_coupon',
			'tuesday' => 'tue_coupon',
			'wednesday' => 'wed_coupon',
			'thursday' => 'thu_coupon',
			'friday' => 'fri_coupon'
		];

		if ( empty( $days_hours_of_week ) ) {
			foreach( $meta_start_key as $value ) {
				delete_post_meta( $coupon_id,$value );
			}

			foreach ( $meta_expiry_key as $value ) {
				delete_post_meta( $coupon_id,$value );
			}
		}

		foreach ( $days as $day => $coupon_prefix ) {
			$coupon_apply_on_day = get_post_meta( $coupon_id, 'coupon_apply_on_' . $day, true );

			if ( empty( $coupon_apply_on_day ) ) {
				delete_post_meta( $coupon_id,  $coupon_prefix . '_start_time' );
				delete_post_meta( $coupon_id,  $coupon_prefix . '_expiry_time' );
			}
		}
	}
}
