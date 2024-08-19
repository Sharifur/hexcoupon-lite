<?php
namespace HexCoupon\App\Controllers\Api;

use HexCoupon\App\Core\Lib\SingleTon;
use HexCoupon\App\Traits\NonceVerify;
use Kathamo\Framework\Lib\Controller;

class SpinWheelSettingsApiController extends Controller
{
	use SingleTon, NonceVerify;

	private $allowed_html = [
		'a'      => [
			'href'  => [],
			'title' => [],
		],
		'u'      => [],
		'br'     => [],
		'em'     => [],
		'strong' => [],
		'p'      => [],
		'ul'     => [],
		'ol'     => [],
		'li'     => [],
		'h1'     => [],
		'h2'     => [],
		'h3'     => [],
		'h4'     => [],
		'h5'     => [],
		'h6'     => [],
		'img'    => [
			'src'   => [],
			'alt'   => [],
			'width' => [],
			'height'=> [],
		],
		'blockquote' => [],
		'code'   => [],
		'pre'    => [],
		'div'    => [
			'class' => [],
			'id'    => [],
		],
		'span'   => [
			'class' => [],
			'id'    => [],
		],
	];

	/**
	 * Register hooks callback
	 *
	 * @return void
	 */
	public function register()
	{
		add_action( 'admin_post_spin_wheel_general_settings_save', [ $this, 'spin_wheel_general_settings_save' ] );
		add_action( 'admin_post_spin_wheel_popup_settings_save', [ $this, 'spin_wheel_popup_settings_save' ] );
		add_action( 'admin_post_spin_wheel_wheel_settings_save', [ $this, 'spin_wheel_wheel_settings_save' ] );
		add_action( 'admin_post_spin_wheel_content_settings_save', [ $this, 'spin_wheel_content_settings_save' ] );
		add_action( 'admin_post_spin_wheel_text_settings_save', [ $this, 'spin_wheel_text_settings_save' ] );
		add_action( 'admin_post_spin_wheel_coupon_settings_save', [ $this, 'spin_wheel_coupon_settings_save' ] );
		add_action( 'wp_ajax_update_spin_count', [ $this, 'update_spin_count' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method update_spin_count
	 * @return void
	 * Sending spin count to the userMeta table of each user
	 */
	public function update_spin_count() 
	{
		// Get the current user ID
		$user_id = get_current_user_id();

		// Ensure the user is logged in
		if ( $user_id == 0 ) {
			wp_send_json_error( 'User not logged in' );
		}

		// Get the current spin count from user meta
		$spin_count = get_user_meta( $user_id, 'user_spin_count', true );

		// If there is no spin count yet, initialize it to 0
		if ( $spin_count === '' ) {
			$spin_count = 0;
		}

		// Increment the spin count
		$spin_count++;

		// Update the spin count in user meta
		update_user_meta( $user_id, 'user_spin_count', $spin_count );

		// Return the updated spin count as a JSON response
		wp_send_json_success( $spin_count );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method spin_wheel_general_settings_save
	 * @return void
	 * Saving all the settings of spin wheel general settings
	 */
	public function spin_wheel_general_settings_save()
	{
		if ( $this->verify_nonce('POST') ) {
			$formData = json_encode( $_POST );

			$dataArray = json_decode( $formData, true );

			$spin_wheel_general = [
				'enableSpinWheel' => isset( $dataArray['settings']['enableSpinWheel'] ) ? rest_sanitize_boolean( $dataArray['settings']['enableSpinWheel'] ) : '',
				'spinPerEmail' => isset( $dataArray['settings']['spinPerEmail'] ) ? sanitize_text_field( $dataArray['settings']['spinPerEmail'] ) : '',
				'delayBetweenSpins' => isset( $dataArray['settings']['delayBetweenSpins'] ) ? sanitize_text_field( $dataArray['settings']['delayBetweenSpins'] ) : '',
			];
			update_option( 'spinWheelGeneral', $spin_wheel_general );


			wp_send_json( $_POST );
		} else {
			wp_send_json( [
				'error' => 'Nonce verification failed',
			], 403 ); // 403 Forbidden status code
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method spin_wheel_popup_settings_save
	 * @return void
	 * Saving all the settings of spin wheel popup settings
	 */
	public function spin_wheel_popup_settings_save()
	{
		if ( $this->verify_nonce('POST') ) {
			$formData = json_encode( $_POST );

			$dataArray = json_decode( $formData, true );

			$spin_popup_settings = [
				'iconColor' => isset( $dataArray['settings']['iconColor'] ) ? sanitize_text_field( $dataArray['settings']['iconColor'] ) : '',
				'alignment' => isset( $dataArray['settings']['alignment'] ) ? sanitize_text_field( $dataArray['settings']['alignment'] ) : '',
				'popupInterval' => isset( $dataArray['settings']['popupInterval'] ) ? sanitize_text_field( $dataArray['settings']['popupInterval'] ) : '',
				'showOnlyHomepage' => isset( $dataArray['settings']['showOnlyHomepage'] ) ? rest_sanitize_boolean( $dataArray['settings']['showOnlyHomepage'] ) : '',
				'showOnlyBlogPage' => isset( $dataArray['settings']['showOnlyBlogPage'] ) ? rest_sanitize_boolean( $dataArray['settings']['showOnlyBlogPage'] ) : '',
				'showOnlyShopPage' => isset( $dataArray['settings']['showOnlyShopPage'] ) ? rest_sanitize_boolean( $dataArray['settings']['showOnlyShopPage'] ) : '',
			];
			update_option( 'spinWheelPopup', $spin_popup_settings );


			wp_send_json( $_POST );
		} else {
			wp_send_json( [
				'error' => 'Nonce verification failed',
			], 403 ); // 403 Forbidden status code
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method spin_wheel_wheel_settings_save
	 * @return void
	 * Saving all the settings of spin wheel wheel settings
	 */
	public function spin_wheel_wheel_settings_save()
	{


		if ( $this->verify_nonce('POST') ) {
			$formData = json_encode( $_POST );

			$dataArray = json_decode( $formData, true );

			$spin_wheel_wheel_settings = [
				'textColor' => isset( $dataArray['settings']['textColor'] ) ? sanitize_text_field( $dataArray['settings']['textColor'] ) : '',
				'wheelDescription' => isset( $dataArray['settings']['wheelDescription'] ) ? wp_kses( $dataArray['settings']['wheelDescription'], $this->allowed_html ) : '',
				'buttonText' => isset( $dataArray['settings']['buttonText'] ) ? sanitize_text_field( $dataArray['settings']['buttonText'] ) : '',
				'buttonColor' => isset( $dataArray['settings']['buttonColor']) ? sanitize_text_field( $dataArray['settings']['buttonColor'] ) : '',
				'enableYourName' => isset( $dataArray['settings']['enableYourName'] ) ? rest_sanitize_boolean( $dataArray['settings']['enableYourName'] ) : '',
				'yourName' => isset( $dataArray['settings']['yourName'] ) ? sanitize_text_field( $dataArray['settings']['yourName'] ) : '',
				'enablePhoneNumber' => isset( $dataArray['settings']['enablePhoneNumber'] ) ? sanitize_text_field( $dataArray['settings']['enablePhoneNumber'] ) : '',
				'phoneNumber' => isset( $dataArray['settings']['phoneNumber'] ) ? sanitize_text_field( $dataArray['settings']['phoneNumber'] ) : '',
				'enableEmailAddress' => isset( $dataArray['settings']['enableEmailAddress'] ) ? sanitize_text_field( $dataArray['settings']['enableEmailAddress'] ) : '',
				'emailAddress' => isset( $dataArray['settings']['emailAddress'] ) ? sanitize_text_field( $dataArray['settings']['emailAddress'] ) : '',
				'gdprMessage' => isset( $dataArray['settings']['gdprMessage'] ) ? wp_kses( $dataArray['settings']['gdprMessage'], $this->allowed_html ) : '',
			];
			update_option( 'spinWheelWheel', $spin_wheel_wheel_settings );

			wp_send_json( $_POST );
		} else {
			wp_send_json([
				'error' => 'Nonce verification failed',
			], 403); // 403 Forbidden status code
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method spin_wheel_content_settings_save
	 * @return void
	 * Saving all the settings of spin wheel content settings
	 */
	public function spin_wheel_content_settings_save() {
		if ( $this->verify_nonce( 'POST' ) ) {
			$formData = json_encode( $_POST );
			$dataArray = json_decode( $formData, true );
	
			$spin_wheel_content_settings = [];
	
			// Loop over each setting and build the array
			foreach ( $dataArray['settings'] as $index => $setting ) {
				$contentKey = "content" . ($index + 1); // Corrected index usage
				$spin_wheel_content_settings[$contentKey] = [
					'couponType'  => isset( $setting['couponType'] ) ? sanitize_text_field( $setting['couponType'] ) : '',
					'label'       => isset( $setting['label'] ) ? sanitize_text_field( $setting['label'] ) : '',
					'value'       => isset( $setting['value'] ) ? sanitize_text_field( $setting['value'] ) : '',
					'probability' => isset( $setting['probability'] ) ? sanitize_text_field( $setting['probability'] ) : '',
					'color'       => isset( $setting['color'] ) ? sanitize_text_field( $setting['color'] ) : '',
				];
			}
	
			// Save the settings to the options table
			update_option( 'spinWheelContent', $spin_wheel_content_settings );
	
			wp_send_json_success( $spin_wheel_content_settings );
		} else {
			wp_send_json_error( ['error' => 'Nonce verification failed'], 403 );
		}
	}	

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method spin_wheel_text_settings_save
	 * @return void
	 * Saving all the settings of spin wheel text settings
	 */
	public function spin_wheel_text_settings_save()
	{
		if ( $this->verify_nonce('POST') ) {
			$formData = json_encode( $_POST );

			$dataArray = json_decode( $formData, true );

			$spin_wheel_text_settings = [
				'emailSubject' => isset( $dataArray['settings']['emailSubject'] ) ? sanitize_text_field( $dataArray['settings']['emailSubject'] ) : '',
				'emailContent' => isset( $dataArray['settings']['emailContent'] ) ? wp_kses( $dataArray['settings']['emailContent'], $this->allowed_html ) : '',
				'frontendMessageIfWin' => isset( $dataArray['settings']['frontendMessageIfWin'] ) ? wp_kses( $dataArray['settings']['frontendMessageIfWin'], $this->allowed_html ) : '',
				'frontendMessageIfLost' => isset( $dataArray['settings']['frontendMessageIfLost'] ) ? wp_kses( $dataArray['settings']['frontendMessageIfLost'], $this->allowed_html ) : '',
			];
			update_option( 'spinWheelText', $spin_wheel_text_settings );

			wp_send_json( $_POST );
		} else {
			wp_send_json( [
				'error' => 'Nonce verification failed',
			], 403 ); // 403 Forbidden status code
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method spin_wheel_coupon_settings_save
	 * @return void
	 * Saving all the settings of spin wheel coupon settings
	 */
	public function spin_wheel_coupon_settings_save()
	{
		if ( $this->verify_nonce('POST') ) {
			$formData = json_encode( $_POST );

			$dataArray = json_decode( $formData, true );

			$spin_wheel_coupon_settings = [
				'spinAllowFreeShipping' => isset( $dataArray['settings']['spinAllowFreeShipping'] ) ? rest_sanitize_boolean( $dataArray['settings']['spinAllowFreeShipping'] ) : '',
				'spinMinimumSpend' => isset( $dataArray['settings']['spinMinimumSpend'] ) ? sanitize_text_field( $dataArray['settings']['spinMinimumSpend'] ) : '',
				'spinMaximumSpend' => isset( $dataArray['settings']['spinMaximumSpend'] ) ? sanitize_text_field( $dataArray['settings']['spinMaximumSpend'] ) : '',
				'spinIndividualSpendOnly' => isset( $dataArray['settings']['spinIndividualSpendOnly'] ) ? rest_sanitize_boolean( $dataArray['settings']['spinIndividualSpendOnly'] ) : '',
				'spinExcludeSaleItem' => isset( $dataArray['settings']['spinExcludeSaleItem'] ) ? rest_sanitize_boolean( $dataArray['settings']['spinExcludeSaleItem'] ) : '',

				'spinIncludeProducts' => isset( $dataArray['settings']['spinIncludeProducts'] ) ? $dataArray['settings']['spinIncludeProducts'] : '',
				'spinExcludeProducts' => isset( $dataArray['settings']['spinExcludeProducts'] ) ? $dataArray['settings']['spinExcludeProducts'] : '',
				'spinIncludeCategories' => isset( $dataArray['settings']['spinIncludeCategories'] ) ? $dataArray['settings']['spinIncludeCategories'] : '',
				'spinExcludeCategories' => isset( $dataArray['settings']['spinExcludeCategories'] ) ? $dataArray['settings']['spinExcludeCategories'] : '',
				'spinUsageLimitPerCoupon' => isset( $dataArray['settings']['spinUsageLimitPerCoupon'] ) ? sanitize_text_field( $dataArray['settings']['spinUsageLimitPerCoupon'] ) : '',
				'spinLimitUsageToXItems' => isset( $dataArray['settings']['spinLimitUsageToXItems'] ) ? sanitize_text_field( $dataArray['settings']['spinLimitUsageToXItems'] ) : '',
				'spinUsageLimitPerUser' => isset( $dataArray['settings']['spinUsageLimitPerUser'] ) ? sanitize_text_field( $dataArray['settings']['spinUsageLimitPerUser'] ) : '',
			];
			update_option( 'spinWheelCoupon', $spin_wheel_coupon_settings );
			

			wp_send_json( $_POST );
		} else {
			wp_send_json([
				'error' => 'Nonce verification failed',
			], 403); // 403 Forbidden status code
		}
	}
}
