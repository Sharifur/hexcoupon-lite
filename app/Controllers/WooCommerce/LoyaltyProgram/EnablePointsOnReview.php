<?php
namespace HexCoupon\App\Controllers\WooCommerce\LoyaltyProgram;

use HexCoupon\App\Core\Helpers\LoyaltyProgram\LoyaltyProgramHelpers;
use HexCoupon\App\Core\Lib\SingleTon;

class EnablePointsOnReview
{
	use SingleTon;

	private $wpdb;
	private $table_name;
	private $store_credit_table;
	private $store_credit_logs_table;
	private $loyalty_points_log_table;
	private $conversion_rate;
	private $loyalty_program_enable_settings;

	/**
	 * Registering hooks that are needed
	 */
	public function register()
	{
		global $wpdb;

		$this->wpdb = $wpdb;
		$this->table_name = $wpdb->prefix . 'hex_loyalty_program_points';
		$this->loyalty_points_log_table = $wpdb->prefix . 'hex_loyalty_points_log';

		$this->store_credit_table = $wpdb->prefix . 'hex_store_credit';
		$this->store_credit_logs_table = $wpdb->prefix . 'hex_store_credit_logs';

		$this->loyalty_program_enable_settings = get_option( 'loyalty_program_enable_settings' );
		$this->points_on_purchase = get_option( 'pointsOnPurchase' );
		$this->conversion_rate = get_option( 'conversionRate' );

		add_action( 'comment_post', [ $this, 'add_points_for_product_review' ], 10, 3 );
		add_action( 'wp_set_comment_status', [ $this, 'add_points_for_comment_approval' ], 10, 2 );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_points_for_comment_approval
	 * @return void
	 * Mechanism for points for order post comment ( This will be triggered when comment are manually approved )
	 */
	public function add_points_for_comment_approval( $comment_id, $comment_status )
	{
		// Check if the comment has been approved
		if ( $comment_status == 'approve' ) {
			$comment = get_comment( $comment_id );
			$comment_author_id = $comment->user_id; // Get the commenter's user ID
			$post_id = $comment->comment_post_ID; // Get the ID of the post the comment is on
			$post_type = get_post_type($post_id); // Get the post type

			// Only proceed if the user is registered (user ID is not 0)
			if ( $comment_author_id != 0 ) {
				// Retrieve the comment points from the options
				$pointsForComment = get_option( 'pointsForComment' );
				$pointsForComment = ! empty( $pointsForComment['pointAmount'] ) ? intval( $pointsForComment['pointAmount'] ) : 0;

				if ( $post_type == 'product' ) {
					$reason = 3;
					$this->give_loyalty_points( $comment_author_id, $pointsForComment, $reason );
				} elseif ( $post_type == 'post' ) {
					$reason = 4;
					$this->give_loyalty_points( $comment_author_id, $pointsForComment, $reason );
				}
			}

		}
	}


	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_points_for_product_review
	 * @return void
	 * Mechanism for points for order product review or post comment
	 */
	public function add_points_for_product_review( $comment_id, $comment_approved, $commentdata )
	{
		if ( $comment_approved === 1 ) {
			// Get the post ID from the comment data
			$post_id = $commentdata['comment_post_ID'];

			// Check if the post is a product
			if ( get_post_type( $post_id ) === 'product' ) {
				// Get the user ID from the comment data
				$user_id = $commentdata['user_id'];

				// Only proceed if the user is registered (user ID is not 0)
				if ( $user_id != 0 ) {
					// Retrieve the review points from the options
					$pointsForReview = get_option( 'pointsForReview' );
					$pointsForReview = ! empty( $pointsForReview['pointAmount'] ) ? intval( $pointsForReview['pointAmount'] ) : 0;
					$reason = 3;

					$this->give_loyalty_points( $user_id, $pointsForReview, $reason );
				}
			}
			else {
				// Get the user ID from the comment data
				$user_id = $commentdata['user_id'];

				// Only proceed if the user is registered (user ID is not 0)
				if ( $user_id != 0 ) {
					// Retrieve the comment points from the options
					$pointsForComment = get_option( 'pointsForComment' );
					$pointsForComment = ! empty( $pointsForComment['pointAmount'] ) ? intval( $pointsForComment['pointAmount'] ) : 0;
					$reason = 4;
					$this->give_loyalty_points( $user_id, $pointsForComment, $reason );
				}
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method give_loyalty_points
	 * @return void
	 * Giving points for order review or post commnent
	 */
	public function give_loyalty_points( $user_id, $pointsForAction, $reason )
	{
		$wpdb = $this->wpdb;

		$table_name = $this->table_name;
		$store_credit_table = $this->store_credit_table;
		$loyalty_points_log_table = $this->loyalty_points_log_table;

		// Get the total points for the user, defaulting to 0 if no entry exists
		$current_points = $wpdb->get_var( $wpdb->prepare(
			"SELECT points FROM $table_name WHERE user_id = %d",
			$user_id
		) );

		// If no entry exists, default the current points to 0
		$current_points = $current_points !== null ? intval( $current_points ) : 0;

		if ( $current_points === 0 ) {
			// Calculate the new points balance
			$new_points_balance = $current_points + $pointsForAction;

			// Prepare the data for insertion or update
			$data = [
				'user_id' => $user_id,
				'points'  => $new_points_balance,
			];

			// Specify the data types for the insert function
			$data_types = [	'%d', '%d' ];

			// Insert or update the user's points balance in the database
			$wpdb->insert(
				$table_name,
				$data,
				$data_types
			);
		} else {
			// Calculate the new points balance
			$new_points_balance = $current_points + $pointsForAction;

			// Prepare the data for insertion or update
			$data = [
				'points' => $new_points_balance,
			];

			$where = [
				'user_id' => $user_id,
			];

			// Specify the data types for the update function
			$data_format = ['%d'];
			$where_format = ['%d'];

			// Insert or update the user's points balance in the database
			$wpdb->update(
				$table_name,
				$data,
				$where,
				$data_format,
				$where_format
			);
		}

		/**
		 * Mechanism to send converting points to store credit and sending it to the database 'store_credit_table'
		 */
		// Getting current store credit amount
		// Get the total amount for the user, defaulting to 0 if no entry exists
		$current_credit = $wpdb->get_var( $wpdb->prepare(
			"SELECT amount FROM $store_credit_table WHERE user_id = %d",
			$user_id
		) );

		// If no entry exists, default the current points to 0
		$current_credit = $current_credit !== null ? intval( $current_credit ) : 0;

		$points_to_be_converted = $this->conversion_rate['points'] ?? 0;

		if ( $current_credit === 0 ) {
			// Calculate the new credit balance
			$new_credit_balance = round( $pointsForAction / $points_to_be_converted, 2 );

			// Prepare the data for insertion or update
			$store_credit_data = [
				'user_id' => $user_id,
				'amount'  => $new_credit_balance,
			];

			// Insert the user's points balance into the database
			$wpdb->insert(
				$store_credit_table,
				$store_credit_data,
				[ '%d', '%f' ]
			);
		} else {
			// Calculate the new points
			$new_credit_balance = round( $pointsForAction / $points_to_be_converted, 2 );
			$updated_credit_balance = round( $current_credit + $new_credit_balance );

			// Prepare the data for insertion or update
			$data = [
				'amount' => $updated_credit_balance,
			];

			$where = [
				'user_id' => $user_id,
			];

			// Specify the data types for the update function
			$data_format = ['%d'];
			$where_format = ['%d'];

			// Insert or update the user's points balance in the database
			$wpdb->update(
				$store_credit_table,
				$data,
				$where,
				$data_format,
				$where_format
			);
		}

		/**
		 * Mechanism to send logs in the 'hex_loyalty_points_log' table
		 */
		$loyalty_points_log_data = [
			'user_id' => intval( $user_id ),
			'points'  => floatval( $pointsForAction ),
			'reason'  => intval( $reason ),
			'converted_credit'  => floatval( $new_credit_balance ),
			'conversion_rate'  => floatval( $points_to_be_converted ),
		];

		$wpdb->insert(
			$loyalty_points_log_table,
			$loyalty_points_log_data,
			[ '%d', '%f', '%d', '%f', '%f' ],
		);

		$loyalty_points_primary_key = $wpdb->insert_id;

		/**
		 * Mechanism to send logs for loyalty points in the 'hex_store_credit_log' table
		 */
		LoyaltyProgramHelpers::getInstance()->send_logs_to_the_store_credit_log_table( $user_id, $new_credit_balance, $loyalty_points_primary_key );
	}
}
