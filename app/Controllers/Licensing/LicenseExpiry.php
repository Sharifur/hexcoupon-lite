<?php
namespace HexCoupon\App\Controllers\Licensing;

use HexCoupon\App\Core\Lib\SingleTon;

class LicenseExpiry
{
	use SingleTon;

	public function register()
	{
		// Hook into init
		add_action( 'init', [ $this, 'check_license_expiry_on_init' ] );
		add_action( 'wp_init', [ $this, 'check_license_expiry_on_init' ] );
	}

	public function check_license_expiry_on_init()
	{
		$hexcoupon_license_key = get_option( 'hexcoupon_license_key' );

		// Your EDD site URL
		$edd_site_url = 'https://wphex.com';
		// License key to check
		$license_key = $hexcoupon_license_key;
		// Item ID (if required)
		$item_id = 2810;
		// URL of the site
		$site_url = home_url();

		// Build the URL for the API request
		$api_url = add_query_arg( [
			'edd_action' => 'check_license',
			'item_id' => $item_id,
			'license' => $license_key,
			'url' => $site_url
		], $edd_site_url );

		// Make the API request
		$response = wp_remote_get( $api_url );

		// Check for errors
		if ( is_wp_error( $response ) ) {
			error_log( 'License check failed: ' . $response->get_error_message() );
			return;
		}

		// Parse the response
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// Check if the license is expired
		if ( $license_data && isset( $license_data->license ) && $license_data->license === 'expired' ) {
			update_option(  'hexcoupon_license_status', $license_data->license );
			// Handle expired license
			add_action( 'admin_notices', function() {
				echo '<div class="notice notice-error"><p>Your license is expired. Please renew to continue using the product.</p></div>';
			} );
		} elseif ( $license_data && isset( $license_data->license ) && $license_data->license !== 'expired' ) {
			// Handle active license
			// You can add additional logic for active licenses if needed
		} else {
			error_log('License check failed: Invalid response.');
		}
	}

}
