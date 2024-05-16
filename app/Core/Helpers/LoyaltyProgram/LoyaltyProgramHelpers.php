<?php
namespace HexCoupon\App\Core\Helpers\LoyaltyProgram;
use HexCoupon\App\Core\Lib\SingleTon;

class LoyaltyProgramHelpers
{
	use SingleTon;

	private $wpdb;
	private $table_name;

	private $pointsForSignup;

	/**
	 * Registering hooks that are needed
	 */
	public function register()
	{
		add_action( 'user_register', [ $this, 'add_signup_points_log' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_signup_points_log
	 * @return void
	 * Giving points to the customer after signup
	 */
	public function add_signup_points_log( $user_id )
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'hex_loyalty_program_points';

		// Retrieve the signup points from the options
		$points_for_signup = get_option( 'pointsForSignup' );
		$points_for_signup = ! empty( $points_for_signup['pointAmount'] ) ? intval( $points_for_signup['pointAmount'] ) : 0;

		// Get the total points for the user, defaulting to 0 if no entry exists
		$current_points = $wpdb->get_var( $wpdb->prepare(
			"SELECT points FROM $table_name WHERE user_id = %d",
			$user_id
		) );

		// If no entry exists, default the current points to 0
		$current_points = $current_points !== null ? intval( $current_points ) : 0;

		// Calculate the new points balance
		$new_points_balance = $current_points + $points_for_signup;

		// Prepare the data for insertion or update
		$data = [
			'user_id' => $user_id,
			'points'  => $new_points_balance,
		];

		// Specify the data types for the insert function
		$data_types = [
			'%d',
			'%d',
		];

		// Insert or update the user's points balance in the database
		$wpdb->replace(
			$table_name,
			$data,
			$data_types
		);
	}


}
