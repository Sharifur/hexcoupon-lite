<?php
namespace HexCoupon\App\Core\Helpers\LoyaltyProgram;
use HexCoupon\App\Core\Lib\SingleTon;

class LoyaltyProgramHelpers
{
	use SingleTon;

	private $wpdb;
	private $table_name;

	private $pointsForSignup;

	private $points_on_purchase;

	/**
	 * Registering hooks that are needed
	 */
	public function register()
	{
		$this->points_on_purchase = get_option( 'pointsOnPurchase' );

		add_action( 'user_register', [ $this, 'give_points_on_signup' ] );
		add_action( 'init', [ $this, 'start_session' ] );
		add_action( 'template_redirect', [ $this, 'handle_referral' ] );

		add_action( 'user_register', [ $this, 'update_referrer_points' ] );

		add_action( 'woocommerce_checkout_order_processed', [ $this, 'give_points_after_order_purchase' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method start_session
	 * @return void
	 * Giving points to the customer after signup
	 */
	public function start_session()
	{
		if ( ! session_id() ) {
			session_start();
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method give_points_on_signup
	 * @return void
	 * Giving points to the customer after signup
	 */
	public function give_points_on_signup( $user_id )
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

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method start_session
	 * @return void
	 * Store the referrer ID in a session variable
	 */
	public function handle_referral()
	{
		if ( isset( $_GET['ref'] ) ) {
			$_SESSION['referrer_id'] = intval( $_GET['ref'] );
			wp_redirect( home_url( '/my-account' ) );
			exit;
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method update_referrer_points
	 * @return void
	 * Give points to the referrer user id
	 */
	public function update_referrer_points()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'hex_loyalty_program_points';

		if ( isset( $_SESSION['referrer_id'] ) ) {
			$referrer_id = intval( $_SESSION['referrer_id'] );
			if ( $referrer_id ) {
				// Retrieve the signup points from the options
				$points_for_referral = get_option( 'pointsForReferral' );
				$points_for_referral = ! empty( $points_for_referral['pointAmount'] ) ? intval( $points_for_referral['pointAmount'] ) : 0;

				// Get the total points for the user, defaulting to 0 if no entry exists
				$current_points = $wpdb->get_var( $wpdb->prepare(
					"SELECT points FROM $table_name WHERE user_id = %d",
					$referrer_id
				) );

				// If no entry exists, default the current points to 0
				$current_points = $current_points !== null ? intval( $current_points ) : 0;

				// Calculate the new points balance
				$new_points_balance = $current_points + $points_for_referral;

				// Prepare the data for insertion or update
				$data = [
					'user_id' => $referrer_id,
					'points'  => $new_points_balance,
				];

				// Check if the user ID already exists in the table
				$existing_entry = $wpdb->get_row( $wpdb->prepare(
					"SELECT * FROM $table_name WHERE user_id = %d",
					$referrer_id
				) );

				// If the user ID doesn't exist, insert a new row
				if ( ! $existing_entry ) {
					// Insert the user's points balance into the database
					$wpdb->insert(
						$table_name,
						$data,
						['%d', '%d']
					);
				} else {
					// Update the user's points balance in the database
					$wpdb->update(
						$table_name,
						['points' => $new_points_balance],
						['user_id' => $referrer_id],
						['%d'],
						['%d']
					);
				}

				// Clear the referrer ID from the session
				unset( $_SESSION['referrer_id'] );
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method give_points_after_order_purchase
	 * @return void
	 * Give points to the user on product purchase
	 */
	public function give_points_after_order_purchase( $order_id )
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'hex_loyalty_program_points';

		$order = wc_get_order( $order_id );
		$user_id = $order->get_user_id();
		$order_total = $order->get_total();

		$spending_amount = ! empty( $this->points_on_purchase['spendingAmount'] ) ? $this->points_on_purchase['spendingAmount']: 0;
		$point_amount = ! empty( $this->points_on_purchase['pointAmount'] ) ? $this->points_on_purchase['pointAmount']: 0;

		// Calculating the points for full order
		$convert_point_on_per_spending = $order_total / $spending_amount;
		$total_points = $convert_point_on_per_spending * $point_amount;

		// Get the total points for the user, defaulting to 0 if no entry exists
		$current_points = $wpdb->get_var( $wpdb->prepare(
			"SELECT points FROM $table_name WHERE user_id = %d",
			$user_id
		) );

		// If no entry exists, default the current points to 0
		$current_points = $current_points !== null ? intval( $current_points ) : 0;

		// Calculate the new points balance
		$new_points_balance = $current_points + $total_points;

		// Prepare the data for insertion or update
		$data = [
			'user_id' => $user_id,
			'points'  => $new_points_balance,
		];

		// Check if the user ID already exists in the table
		$existing_entry = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $table_name WHERE user_id = %d",
			$user_id
		) );

		// If the user ID doesn't exist, insert a new row
		if ( ! $existing_entry ) {
			// Insert the user's points balance into the database
			$wpdb->insert(
				$table_name,
				$data,
				['%d', '%d']
			);
		} else {
			// Update the user's points balance in the database
			$wpdb->update(
				$table_name,
				['points' => $new_points_balance],
				['user_id' => $user_id],
				['%d'],
				['%d']
			);
		}

	}
}
