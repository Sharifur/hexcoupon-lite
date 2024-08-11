<?php
namespace HexCoupon\App\Controllers\Api;

use HexCoupon\App\Core\Lib\SingleTon;
use HexCoupon\App\Traits\NonceVerify;
use Kathamo\Framework\Lib\Controller;

class SpinWheelSettingsApiController extends Controller
{

	use SingleTon, NonceVerify;

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
				'enableSpinWheel' => isset($dataArray['settings']['enableSpinWheel']) ? rest_sanitize_boolean($dataArray['settings']['enableSpinWheel']) : '',
				'spinPerEmail' => isset($dataArray['settings']['spinPerEmail']) ? sanitize_text_field($dataArray['settings']['spinPerEmail']) : '',
				'delayBetweenSpins' => isset($dataArray['settings']['delayBetweenSpins']) ? sanitize_text_field($dataArray['settings']['delayBetweenSpins']) : '',
			];
			update_option( 'spinWheelGeneral', $spin_wheel_general );


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
				'iconColor' => isset($dataArray['settings']['iconColor']) ? sanitize_text_field($dataArray['settings']['iconColor']) : '',
				'alignment' => isset($dataArray['settings']['alignment']) ? sanitize_text_field($dataArray['settings']['alignment']) : '',
				'popupInterval' => isset($dataArray['settings']['popupInterval']) ? sanitize_text_field($dataArray['settings']['popupInterval']) : '',
				'showOnlyHomepage' => isset($dataArray['settings']['showOnlyHomepage']) ? rest_sanitize_boolean($dataArray['settings']['showOnlyHomepage']) : '',
				'showOnlyBlogPage' => isset($dataArray['settings']['showOnlyBlogPage']) ? rest_sanitize_boolean($dataArray['settings']['showOnlyBlogPage']) : '',
				'showOnlyShopPage' => isset($dataArray['settings']['showOnlyShopPage']) ? rest_sanitize_boolean($dataArray['settings']['showOnlyShopPage']) : '',
			];
			update_option( 'spinWheelPopup', $spin_popup_settings );


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
				'textColor' => isset($dataArray['settings']['textColor']) ? sanitize_text_field($dataArray['settings']['textColor']) : '',
				'wheelDescription' => isset($dataArray['settings']['wheelDescription']) ? sanitize_text_field($dataArray['settings']['wheelDescription']) : '',
				'buttonText' => isset($dataArray['settings']['buttonText']) ? sanitize_text_field($dataArray['settings']['buttonText']) : '',
				'yourName' => isset($dataArray['settings']['yourName']) ? sanitize_text_field($dataArray['settings']['yourName']) : '',
				'phoneNumber' => isset($dataArray['settings']['phoneNumber']) ? sanitize_text_field($dataArray['settings']['phoneNumber']) : '',
				'emailAddress' => isset($dataArray['settings']['emailAddress']) ? sanitize_text_field($dataArray['settings']['emailAddress']) : '',
				'gdprMessage' => isset($dataArray['settings']['gdprMessage']) ? sanitize_text_field($dataArray['settings']['gdprMessage']) : '',
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
	public function spin_wheel_content_settings_save()
	{
		if ($this->verify_nonce('POST')) {
			$formData = json_encode($_POST);
			$dataArray = json_decode($formData, true);

			$spin_wheel_content_settings = [];

			// Loop over each setting and build the array
			foreach ($dataArray['settings'] as $key => $setting) {
				$contentKey = "content" . ($key + 1);
				$spin_wheel_content_settings[$contentKey] = [
					'couponType'  => isset($setting['couponType']) ? sanitize_text_field($setting['couponType']) : '',
					'label'       => isset($setting['label']) ? sanitize_text_field($setting['label']) : '',
					'value'       => isset($setting['value']) ? sanitize_text_field($setting['value']) : '',
					'probability' => isset($setting['probability']) ? sanitize_text_field($setting['probability']) : '',
					'color'       => isset($setting['color']) ? sanitize_text_field($setting['color']) : '',
				];
			}

			error_log(print_r($spin_wheel_content_settings,true));

			// Save the settings to the options table
			update_option('spinWheelContent', $spin_wheel_content_settings);

			wp_send_json_success($spin_wheel_content_settings);
		} else {
			wp_send_json_error(['error' => 'Nonce verification failed'], 403);
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
				'emailSubject' => isset($dataArray['settings']['emailSubject']) ? sanitize_text_field($dataArray['settings']['emailSubject']) : '',
				'emailContent' => isset($dataArray['settings']['emailContent']) ? sanitize_text_field($dataArray['settings']['emailContent']) : '',
				'frontendMessageIfWin' => isset($dataArray['settings']['frontendMessageIfWin']) ? sanitize_text_field($dataArray['settings']['frontendMessageIfWin']) : '',
				'frontendMessageIfLost' => isset($dataArray['settings']['frontendMessageIfLost']) ? sanitize_text_field($dataArray['settings']['frontendMessageIfLost']) : '',
			];
			update_option( 'spinWheelText', $spin_wheel_text_settings );


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
				'spinAllowFreeShipping' => isset($dataArray['settings']['spinAllowFreeShipping']) ? rest_sanitize_boolean($dataArray['settings']['spinAllowFreeShipping']) : '',
				'spinMinimumSpend' => isset($dataArray['settings']['spinMinimumSpend']) ? sanitize_text_field($dataArray['settings']['spinMinimumSpend']) : '',
				'spinMaximumSpend' => isset($dataArray['settings']['spinMaximumSpend']) ? sanitize_text_field($dataArray['settings']['spinMaximumSpend']) : '',
				'spinIndividualSpendOnly' => isset($dataArray['settings']['spinIndividualSpendOnly']) ? sanitize_text_field($dataArray['settings']['spinIndividualSpendOnly']) : '',
				'spinExcludeSaleItem' => isset($dataArray['settings']['spinExcludeSaleItem']) ? sanitize_text_field($dataArray['settings']['spinExcludeSaleItem']) : '',
				'spinIncludeProducts' => isset($dataArray['settings']['spinIncludeProducts']) ? sanitize_text_field($dataArray['settings']['spinIncludeProducts']) : '',
				'spinIncludeCategories' => isset($dataArray['settings']['spinIncludeCategories']) ? sanitize_text_field($dataArray['settings']['spinIncludeCategories']) : '',
				'spinExcludeCategories' => isset($dataArray['settings']['spinExcludeCategories']) ? sanitize_text_field($dataArray['settings']['spinExcludeCategories']) : '',
				'spinUsageLimitPerCoupon' => isset($dataArray['settings']['spinUsageLimitPerCoupon']) ? sanitize_text_field($dataArray['settings']['spinUsageLimitPerCoupon']) : '',
				'spinLimitUsageToXItems' => isset($dataArray['settings']['spinLimitUsageToXItems']) ? sanitize_text_field($dataArray['settings']['spinLimitUsageToXItems']) : '',
				'spinUsageLimitPerUser' => isset($dataArray['settings']['spinUsageLimitPerUser']) ? sanitize_text_field($dataArray['settings']['spinUsageLimitPerUser']) : '',
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
