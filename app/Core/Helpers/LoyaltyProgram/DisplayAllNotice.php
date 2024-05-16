<?php
namespace HexCoupon\App\Core\Helpers\LoyaltyProgram;
use HexCoupon\App\Core\Lib\SingleTon;

class DisplayAllNotice
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
		$this->pointsForSignup = get_option( 'pointsForSignup' );

		add_action( 'woocommerce_register_form_start', [ $this, 'display_signup_points_notice' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method display_signup_points_notice
	 * @return void
	 * Creating table called 'hex_store_credit_logs_table'
	 */
	public function display_signup_points_notice() {
		if ( ! is_account_page() ) {
			return;
		}

		$points_for_signup = ! empty( $this->pointsForSignup['pointAmount'] ) ? $this->pointsForSignup['pointAmount'] : '';

		echo '<div class="woocommerce-info">';
		printf( esc_html__( 'Sign up now and receive %s reward points!', 'hex-coupon-for-woocommerce' ), esc_html( $points_for_signup ) );
		echo '</div>';
	}

}
