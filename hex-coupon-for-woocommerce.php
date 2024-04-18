<?php
/**
 * @package hexcoupon
 *
 * Plugin Name: HexCoupon: Ultimate WooCommerce Toolkit
 * Plugin URI: https://wordpress.org/plugins/hex-coupon-for-woocommerce
 * Description: Extend coupon functionality in your Woocommerce store.
 * Version: 1.1.5
 * Author: WpHex
 * Requires at least: 5.4
 * Tested up to: 6.4.3
 * Requires PHP: 7.1
 * WC requires at least: 6.0
 * WC tested up to: 8.7.0
 * Author URI: https://wphex.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hex-coupon-for-woocommerce
 * Domain Path: /languages
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use HexCoupon\App\Core\Core;
use HexCoupon\App\Core\Helpers\StoreCreditHelpers;
use HexCoupon\App\Core\Helpers\StoreCreditPaymentHelpers;

if ( ! defined( 'ABSPATH' ) ) die();

define( 'HEXCOUPON_FILE', __FILE__ );

require_once __DIR__ .'/qrcode/qrcode.php';

require_once __DIR__ . '/configs/bootstrap.php';

if ( file_exists( HEXCOUPON_DIR_PATH . '/vendor/autoload.php' ) ) {
	require_once HEXCOUPON_DIR_PATH . '/vendor/autoload.php';
}

/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function appsero_init_tracker_hex_coupon_for_woocommerce() {

	if ( ! class_exists( 'Appsero\Client' ) ) {
		require_once __DIR__ . '/appsero/src/Client.php';
	}

	$client = new Appsero\Client( 'c0ee1555-4851-4d71-8b6d-75b1872dd3d2', 'HexCoupon &#8211; Advance Coupons For WooCommerce(Free)', __FILE__ );

	// Active insights
	$client->insights()->init();

}

appsero_init_tracker_hex_coupon_for_woocommerce();

add_filter( 'plugin_action_links', 'hexcoupon_plugin_page_action_list', 10, 2 );

/**
 * Add custom texts besides deactivate text in the plugin page
 *
 * @return void
 */
function hexcoupon_plugin_page_action_list( $actions, $plugin_file )
{
	// Specify the directory and file name of the specific plugin
	$specific_plugin_directory = 'hex-coupon-for-woocommerce';
	$specific_plugin_file = 'hex-coupon-for-woocommerce.php';

	$support_link = 'https://wordpress.org/support/plugin/hex-coupon-for-woocommerce/';
	$documentation_link = 'https://hexcoupon.com/docs/';

	// Check if the current plugin is the specific one
	if ( strpos( $plugin_file, $specific_plugin_directory . '/' . $specific_plugin_file ) !== false ) {
		// Add custom link(s) beside the "Deactivate" link
		$actions[] = '<a href=" ' . esc_url( $support_link ) . ' " target="_blank">'. __( 'Support', 'hex-coupon-for-woocommerce' ) .'</a>';
		$actions[] = '<a href=" ' . esc_url( $documentation_link ) . ' " target="_blank"><b>'. __( 'Documentation', 'hex-coupon-for-woocommerce' ) .'</b></a>';
	}

	return $actions;
}

/**
 * Plugin compatibility declaration with WooCommerce HPOS - High Performance Order Storage
 *
 * @return void
 */
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( FeaturesUtil::class ) ) {
		FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

/**
 * Redirect users to the dashboard of HexCoupon after activating the plugin
 *
 * @return void
 */
add_action( 'activated_plugin', 'redirect_to_hexcoupon_dashboard_after_plugin_activation' );
function redirect_to_hexcoupon_dashboard_after_plugin_activation( $plugin ) {
	if ( $plugin == 'hex-coupon-for-woocommerce/hex-coupon-for-woocommerce.php' ) {
		// Check if WooCommerce is active and then redirect to HexCoupon menu page
		if ( class_exists( 'WooCommerce' ) ) {
			// Redirect to the specified page after activation
			wp_safe_redirect( admin_url( 'admin.php?page=hexcoupon-page' ) );
			exit;
		}
	}
}

/**
 * Override the cart page and checkout page with the old woocommerce classic pattern content
 *
 * @return void
 */
function alter_cart_page_with_cart_shortcode( $content ) {
	if ( class_exists( 'WooCommerce' ) ) {
		// Check if it's the WooCommerce cart page
		if ( is_cart() ) {
			// Insert the [woocommerce_cart] shortcode in the cart page of the site.
			$content = '[woocommerce_cart]';
		}
	}

	return $content;
}

add_filter( 'the_content', 'alter_cart_page_with_cart_shortcode' );

add_filter( 'woocommerce_coupon_discount_types', 'display_bogo_discount_in_couopon_type_column',10, 1 );

/**
 * Display 'Bogo Discount' text in the 'Coupon type' column in all coupon page
 *
 * @return void
 */
function display_bogo_discount_in_couopon_type_column( $discount_types ) {
	$discount_types[ 'buy_x_get_x_bogo' ] = esc_html__( 'Bogo Discount', 'hex-coupon-for-woocommerce' );

	return $discount_types;
}

// Registering the store credit custom payment gateway
add_filter( 'woocommerce_payment_gateways', 'add_store_credit_custom_payment_method' );

function add_store_credit_custom_payment_method( $methods )
{
	$methods[] = 'WC_Store_Credit';
	return $methods;
}

// Registering store credit class
add_filter( 'woocommerce_payment_gateways', 'hexcoupon_store_credit_class' );

function hexcoupon_store_credit_class( $gateways )
{
	$gateways[] = 'HexCoupon_Store_Credit_Payment_Method';
	return $gateways;
}

// Creating the custom store credit payment class
add_action( 'plugins_loaded', 'hex_store_credit_gateway' );

function hex_store_credit_gateway()
{
	if ( class_exists( 'WC_Payment_Gateway' ) ) {
		class HexCoupon_Store_Credit_Payment_Method extends WC_Payment_Gateway {

			public $order_status;
			public function __construct() {
				$this->id = 'hex_store_credit'; // payment method id
				$this->icon = ''; // payment method icon
				$this->has_fields = true; // in case you need a custom credit card form
				$this->method_title = esc_html__( 'Hex Store Credit', 'hex-coupon-for-woocommerce' ); // Title of the payment method
				$this->method_description = esc_html__( 'Take payments via store credit', 'hex-coupon-for-woocommerce' ); // Description of the payment method

				// Payment method supports
				$this->supports = [
					'products'
				];

				$this->init_form_fields();

				$this->init_settings();
				$this->title = $this->get_option( 'title' );
				$this->description = $this->get_option( 'description' );
				$this->enabled = $this->get_option( 'enabled' );

				// Hook for saving the settings
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
				add_action( 'woocommerce_thankyou_hex_store_credit', [ $this, 'store_credit_update_order_status' ], 10, 1 );
			}

			// All plugin options
			public function init_form_fields()
			{
				$this->form_fields = [
					'enabled' => [
						'title'       => esc_html__( 'Enable/Disable', 'hex-coupon-for-woocommerce' ),
						'label'       => esc_html__( 'Enable Hex Store Credit', 'hex-coupon-for-woocommerce' ),
						'type'        => 'checkbox',
						'description' => '',
						'default'     => 'no'
					],
					'title' => [
						'title'       => esc_html__( 'Title', 'hex-coupon-for-woocommerce' ),
						'type'        => 'text',
						'description' => esc_html__( 'The user will see this during the checkout process.', 'hex-coupon-for-woocommerce' ),
						'default'     => esc_html__( 'Store Credit', 'hex-coupon-for-woocommerce' ),
						'desc_tip'    => true,
					],
					'description' => [
						'title'       => esc_html__( 'Description', 'hex-coupon-for-woocommerce' ),
						'type'        => 'textarea',
						'description' => esc_html__( 'This description will be shown during the checkout process.', 'hex-coupon-for-woocommerce' ),
						'default'     => esc_html__( 'Pay with your hex store credit', 'hex-coupon-for-woocommerce' ),
					],
				];
			}

			// This form will be used during the checkout
			public function payment_fields()
			{
				if( $this->description ) {
					// Displaying the description below the payment option
					echo wpautop( wp_kses_post( $this->description ) );
					$total_available_store_credit = StoreCreditPaymentHelpers::getInstance()->show_total_remaining_amount();
				}
				?>
				<div class="form-row form-row-wide" style="border: 1px solid black; padding: 10px">
					<label><?php echo esc_html__( 'Total credit available', 'hex-coupon-for-woocommerce' ); ?></label>
					<b><?php echo wc_price( $total_available_store_credit ); ?></b>
				</div>
				<div class="clear"></div>
				<?php
			}

			// Processing the payment method
			public function process_payment( $order_id )
			{
				// Getting the order details
				$order = wc_get_order( $order_id );

				// Reducing the product stock level
				wc_reduce_stock_levels( $order_id );

				// Making the cart empty
				WC()->cart->empty_cart();

				// Returning to the thank-you redirection
				return array(
					'result' => 'success',
					'redirect' => $this->get_return_url( $order )
				);
			}

			public function store_credit_update_order_status( $order_id )
			{
				// Updating the order status to 'processing' if paid with 'hex_store_credit'
				$order = wc_get_order( $order_id );
				if ( $order && $order->get_payment_method() === 'hex_store_credit' ) {
					$order->update_status( 'processing' );
				}
			}
		}
	}
}

// Processing the custom payment
add_action( 'woocommerce_checkout_process', 'process_store_credit_payment' );

function process_store_credit_payment()
{
	if( $_POST['payment_method'] != 'hex_store_credit' )
		return;
}

// Updating the order meta
add_action( 'woocommerce_checkout_update_order_meta', 'custom_payment_update_order_meta' );

function custom_payment_update_order_meta( $order_id )
{
	if( $_POST['payment_method'] != 'hex_store_credit' )
		return;
}

// Display the custom payment details
add_action( 'woocommerce_admin_order_data_after_billing_address', 'custom_checkout_field_display_admin_order_meta', 10, 1 );

function custom_checkout_field_display_admin_order_meta( $order )
{
	if( $order->get_payment_method() != 'hex_store_credit' )
		return;
}

// Filter hook to show custom confirmation text about store credit in checkout thankyou page
add_filter( 'woocommerce_thankyou_order_received_text', 'custom_store_credit_thankyou_message', 10, 2 );

function custom_store_credit_thankyou_message( $thankyou_message, $order )
{
	$site_url = get_site_url() . '/my-account/store-credit';

	$payment_method = $order->get_payment_method();

	if ( $payment_method === 'hex_store_credit' ) {
		// Appending store credit thank you message
		$thankyou_message .= '<div class="store-credit">' . esc_html__( 'You have used store credit as payment method.', 'hex-coupon-for-woocommerce' ) . '</div>';
		$thankyou_message .= '<a href="' . esc_url( $site_url ) . '">' . esc_html__( 'Check the store credit logs to see the details' ) . '</a>';
	}

	return $thankyou_message;
}

// Getting the current order id after completing the checkout
add_action( 'woocommerce_thankyou', 'get_the_order_id', 10, 1 );

function get_the_order_id( $order_id )
{
	$order = wc_get_order( $order_id );
	$order_amount = $order->get_total();
	$payment_method = $order->get_payment_method();
	$user_id = $order->get_user_id();

	function is_checkout_block() {
		return WC_Blocks_Utils::has_block_in_page( wc_get_page_id('checkout'), 'woocommerce/checkout' );
	}

	if ( $payment_method === 'hex_store_credit' ) {
		StoreCreditPaymentHelpers::getInstance()->send_log_for_store_credit_order_purchase( $order_id, $order_amount );

		if ( is_checkout_block() ) {
			StoreCreditPaymentHelpers::getInstance()->deduct_store_credit( $order_amount );
		}
	}
}

// show error message to customer if they don't have enough balance to store credit.
add_action( 'woocommerce_checkout_process', 'show_error_message_for_store_credit_balance' );

function show_error_message_for_store_credit_balance()
{
	StoreCreditPaymentHelpers::getInstance()->error_message_for_low_store_credit();
}

// Creating a log for store credit after giving full or partial refund to the customer
add_action( 'woocommerce_order_status_changed', 'refunded_order_data', 10, 4 );

function refunded_order_data( $order_id, $old_status, $new_status, $order )
{
	// Getting 'store_credit_enable_data' data from the option table
	$store_credit_enable_data = get_option( 'store_credit_enable_data' );

	// Checking if the new status is 'refunded'
	if ( 'refunded' === $new_status && $store_credit_enable_data['enable'] ) {
		$user_id = $order->get_user_id(); // getting the user id from the order object
		$amount = $order->get_total();
		// Get the timestamp when the order status changed to 'refunded'
		$status_change_date = current_time( 'timestamp' );
		// Convert timestamp to a human-readable date format
		$formatted_date = date_i18n( get_option( 'date_format' ), $status_change_date );

		$user_data = get_userdata( $user_id );
		$user_email = $user_data->user_email;
		$notification_message = 'You got store credit ' . wc_price( $amount ) . 'for the as refund';
		$status = 1;

		// Get the admin ID who performed the refund
		$admin_id = get_current_user_id();
		$admin_data = get_userdata( $admin_id );
		$admin_name = $admin_data ? $admin_data->display_name : 'Unknown';

		// Invoking data insertion functions
		StoreCreditHelpers::getInstance()->hex_store_credit_logs_initial_insertion( $order_id, $user_id, $admin_id, $admin_name, $amount, $status );
		StoreCreditHelpers::getInstance()->hex_store_credit_initial_data_insertion( $amount, $user_id );
		// Sending notification via email
		StoreCreditHelpers::getInstance()->send_confirmation_email_for_store_credit_activation( $user_id, $order_id, $amount, $notification_message, $user_email, $formatted_date );
	}
	// Checking if all the new status has the partial refunded amount
	if ( ( 'completed' === $new_status || 'processing' === $new_status || 'failed' === $new_status ) && $store_credit_enable_data['enable'] && $order->get_total_refunded() ) {
		$user_id = $order->get_user_id(); // getting the user id from the order object
		$amount = $order->get_total_refunded();
		// Get the timestamp when the order status changed to 'refunded'
		$status_change_date = current_time( 'timestamp' );
		// Convert timestamp to a human-readable date format
		$formatted_date = date_i18n( get_option( 'date_format' ), $status_change_date );

		$status = 1;
		$notification_message = 'You got store credit ' . wc_price( $amount ) . 'for the as refund';

		$user_data = get_userdata( $user_id );
		$user_email = $user_data->user_email;

		// Get the admin ID who performed the refund
		$admin_id = get_current_user_id();
		$admin_data = get_userdata( $admin_id );
		$admin_name = $admin_data ? $admin_data->display_name : 'Unknown';

		// Invoking data insertion functions
		StoreCreditHelpers::getInstance()->hex_store_credit_logs_initial_insertion( $order_id, $user_id, $admin_id, $admin_name, $amount, $status );
		StoreCreditHelpers::getInstance()->hex_store_credit_initial_data_insertion( $amount, $user_id );
		// Sending notification via email
		StoreCreditHelpers::getInstance()->send_confirmation_email_for_store_credit_activation( $user_id, $order_id, $amount, $notification_message, $user_email, $formatted_date );
	}
}

// Showing a admin notice after a log has been created after giving store credit for the refund
add_action( 'woocommerce_admin_order_data_after_order_details', 'refunded_order_data_notice' );

function refunded_order_data_notice( $order )
{
	// Getting 'store_credit_enable_data' data from the option table
	$store_credit_enable_data = get_option( 'store_credit_enable_data' );

	$admin_url = admin_url( 'admin.php?page=hexcoupon-page#/store-credit/store-credit-logs' );

	// Checking if the order status is 'refunded' and store credit is enabled
	if ( in_array( $order->get_status(), array( 'refunded', 'processing', 'failed', 'completed' ) ) && $order->get_total_refunded() > 0 && $store_credit_enable_data['enable'] ) {
		$refund_amount = $order->get_total_refunded();
		if ( $refund_amount ) {
			?>
			<div class="notice notice-info is-dismissible updated">
				<p><?php esc_html_e( 'The refund amount has been converted to store credit of:  ', 'hex-coupon-for-woocommerce' );?><b><?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $refund_amount ) ); ?></b></p>
				<p><a class="button-primary button-large" href="<?php echo esc_url( $admin_url );?>"><?php echo esc_html__( 'Check Store Credit Log' );?></a></p>
			</div>
			<?php
		}
	}
}

// mailtrap email service configuration
//add_action( 'phpmailer_init', 'mailtrap_check' );
//
//function mailtrap_check( $phpmailer )
//{
//	$phpmailer->isSMTP();
//	$phpmailer->Host = 'sandbox.smtp.mailtrap.io';
//	$phpmailer->SMTPAuth = true;
//	$phpmailer->Port = 2525;
//	$phpmailer->Username = '06d811eada3301';
//	$phpmailer->Password = '149556ee9e6445';
//}

add_action( 'woocommerce_blocks_loaded', 'store_credit_block_support' );

function store_credit_block_support() {

	// here we're including our "gateway block support class"
	require_once __DIR__ . '/app/Core/WooCommerce/StoreCredit/StoreCreditBlockSupport.php';

	// registering the PHP class we have just included
	add_action(
		'woocommerce_blocks_payment_method_type_registration',
		function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
			$payment_method_registry->register( new StoreCreditBlockSupport );
		}
	);
}

Core::getInstance();
