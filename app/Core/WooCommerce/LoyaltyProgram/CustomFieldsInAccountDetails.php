<?php
namespace HexCoupon\App\Core\WooCommerce\LoyaltyProgram;

use HexCoupon\App\Controllers\WooCommerce\LoyaltyProgram\EnablePointsOnReview;
use HexCoupon\App\Core\Lib\SingleTon;

class CustomFieldsInAccountDetails
{
	use SingleTon;

	/**
	 * @return void
	 * @author WpHex
	 * @method register
	 * @package hexcoupon
	 * @since 1.0.0
	 * Add all hooks that are needed.
	 */
	public function register()
	{
		// Add the date of birth field to the account details form
		add_action( 'woocommerce_edit_account_form_start', [ $this, 'add_dob_field_to_account_details' ] );
		// Save the date of birth field
		add_action( 'woocommerce_save_account_details', [ $this, 'save_dob_field_in_account_details' ] );
		// Hook the function to run daily (you may want to set up a cron job for this)
		add_action( 'wp_loaded', [ $this, 'schedule_daily_event' ] );
		// Hook the function to our scheduled event
		add_action( 'daily_gift_points_event_for_bd', [ $this, 'gift_points_on_birthday' ] );
		add_filter( 'cron_schedules', [ $this, 'custom_schedule' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_dob_field_to_account_details
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function add_dob_field_to_account_details()
	{
		woocommerce_form_field(
			'date_of_birth',
			[
				'type' => 'date',
				'required' => false,
				'label' => 'Date of Birth',
				'class' => ['form-row-wide']
			],
			get_user_meta( get_current_user_id(), 'date_of_birth', true )
		);
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_dob_field_in_account_details
	 * @param int $user_id
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function save_dob_field_in_account_details( $user_id )
	{
		if ( isset( $_POST['date_of_birth'] ) ) {
			update_user_meta( $user_id, 'date_of_birth', sanitize_text_field( $_POST['date_of_birth'] ) );
		}
	}

	/**
	 * Function to gift points on birthdays
	 *
	 * @package hexcoupon
	 * @since 1.0.0
	 */
	public function gift_points_on_birthday()
	{
		$today = date('m-d' ); // We use 'm-d' to match the month and day

		$args = [
			'meta_key' => 'date_of_birth',
			'meta_value' => '-' . $today, // This ensures the month and day match, regardless of year
			'meta_compare' => 'LIKE'
		];

		$user_query = new \WP_User_Query( $args );
		$users = $user_query->get_results();

		$points_for_bd = 20;

		foreach ( $users as $user ) {
			error_log( $user->ID . ' <br>' );
//			EnablePointsOnReview::getInstance()->give_loyalty_points( $user->ID, $points_for_bd, 5 );
		}
	}

	/**
	 * Schedule the daily event if not already scheduled
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function schedule_daily_event()
	{
		$bd_points_giving_on_of = 1;

		if ( ! wp_next_scheduled( 'daily_gift_points_event_for_bd' ) ) {
			wp_schedule_event( time(), 'per_minute', 'daily_gift_points_event_for_bd' );
		}

		if ( $bd_points_giving_on_of == 0 ) {
			// wp_clear_scheduled_hook('daily_gift_points_event_for_bd');
		}
	}

	public function custom_schedule()
	{
		$schedules['per_minute'] = array(
			'interval' => MINUTE_IN_SECONDS,
			'display' => __( 'One Minute' )
		);
		return $schedules;
	}
}
