<?php
namespace HexCoupon\App\Core\WooCommerce;

use HexCoupon\App\Core\Lib\SingleTon;

class CouponEmailSMS {

	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method register
	 * @return mixed
	 * Registers all hooks that are needed to create custom tab 'SMS/Email Tab' on 'Coupon Single' page.
	 */
	public function register()
	{
		add_action( 'woocommerce_coupon_data_tabs', [ $this, 'add_sms_email_tab' ] );
		add_filter( 'woocommerce_coupon_data_panels', [ $this, 'add_sms_email_tab_content' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_geographic_restriction_tab
	 * @return string
	 * Displays the new tab in the coupon single page called 'SMS/Email'.
	 */
	public function add_sms_email_tab( $tabs )
	{
		$tabs['sms_email_tab'] = array(
			'label'    => esc_html__( 'SMS/Email Coupon', 'hex-coupon-for-woocommerce' ),
			'target'   => 'sms_email_tab',
			'class'    => array( 'sma_email_coupon' ),
		);
		return $tabs;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_geographic_restriction_tab_content
	 * @return void
	 * Displays the content of custom tab 'SMS/Email'.
	 */
	public function add_sms_email_tab_content()
	{
		echo '<div id="sms_email_tab" class="panel woocommerce_options_panel sms_email_tab">';
			echo '<h2 class="form-field sms_email_pro_text">' . esc_html__( 'This is a premium feature, will be available very soon!', 'hex-coupon-for-woocommerce' ) . '</h2>';
		echo '</div>';
	}
}
