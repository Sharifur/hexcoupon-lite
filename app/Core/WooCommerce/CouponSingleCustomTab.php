<?php
namespace HexCoupon\App\Core\WooCommerce;

use HexCoupon\App\Core\Helpers\FormHelpers;
use HexCoupon\App\Core\Helpers\RenderHelpers;
use HexCoupon\App\Core\Lib\SingleTon;

class CouponSingleCustomTab
{
	use singleton;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return mixed
	 * @since 1.0.0
	 * Registers all hooks that are needed to create custom tab on 'Coupon Single' page.
	 */
	public function register()
	{
		add_filter( 'woocommerce_coupon_data_panels', [ $this, 'add_custom_coupon_tab' ] );
		add_action( 'woocommerce_coupon_data_tabs', [ $this, 'add_custom_coupon_tab_content' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_custom_coupon_tab_content
	 * @return string
	 * @since 1.0.0
	 * Displays the new tab in the coupon single page called 'Hexcoupon'.
	 */
	public function add_custom_coupon_tab_content( $tabs )
	{
		$tabs['custom_coupon_tab'] = array(
			'label'    => esc_html__( 'Payment & shipping method', 'hexcoupon' ),
			'target'   => 'custom_coupon_tab',
			'class'    => array( 'show_if_coupon_usage_limits' ),
		);
		return $tabs;
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @method get_all_payment_methods
	 * @return array
	 * @since 1.0.0
	 * Retrieve all payment methods of 'WooCommerce' that are enabled
	 */
	public function get_all_payment_methods()
	{
		$payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
		$payment_options = [];

		foreach ( $payment_gateways as $gateway_id => $gateway ) {
			$payment_options[ $gateway_id ] = $gateway->get_title();
		}

		return $payment_options;
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @method get_all_shipping_methods
	 * @return mixed
	 * @since 1.0.0
	 * Retrieve all shipping methods of 'WooCommerce' that are enabled.
	 */
	public function get_all_shipping_methods()
	{
		$shipping_methods = \WC_Shipping_Zones::get_zones();

		// Show the names of the enabled shipping methods only.
		$shipping_method_names = [];
		foreach ( $shipping_methods as $shipping_method ) {
			foreach ( $shipping_method['shipping_methods'] as $shipping_method ) {
				if ( 'Free shipping' === $shipping_method->get_method_title() ) {
					$shipping_method_index = 1;
					$shipping_method_id = $shipping_method->id . ':' . $shipping_method_index;
				}
				if ( 'Flat rate' === $shipping_method->get_method_title() ) {
					$shipping_method_index = 2;
					$shipping_method_id = $shipping_method->id . ':' . $shipping_method_index;
				}
				if ( 'Local pickup' === $shipping_method->get_method_title() ) {
					$shipping_method_index = 3;
					$shipping_method_id = $shipping_method->id . ':' . $shipping_method_index;
				}

				if ( 'yes' === $shipping_method->enabled ) {
					$shipping_method_names[$shipping_method_id] = $shipping_method->title;
				}
			}
		}

		return $shipping_method_names;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_custom_coupon_tab
	 * @return string
	 * @since 1.0.0
	 * Displays the content of custom tab 'Hexcoupon'.
	 */
	public function add_custom_coupon_tab()
	{
		$output ='<div id="custom_coupon_tab" class="panel woocommerce_options_panel">';

		$selected_payment_methods = get_post_meta( get_the_ID(),'permitted_payment_methods',true );

		$output .= FormHelpers::Init( [
			'label' => esc_html__( 'Apply Payment Methods', 'hexcoupon' ),
			'name' => 'permitted_payment_methods',
			'value' => $selected_payment_methods,
			'type' => 'select',
			'options' => $this->get_all_payment_methods(), //if the field is select, this param will be here
			'multiple' => true,
			'select2' => true,
			'class' => 'permitted_payment_methods',
			'placeholder' => __('Apply Payment Methods')
		] );

		$selected_shipping_methods = get_post_meta( get_the_ID(),'permitted_shipping_methods',true );

		$output .= FormHelpers::Init( [
			'label' => esc_html__( 'Apply Shipping Methods', 'hexcoupon' ),
			'name' => 'permitted_shipping_methods',
			'value' => $selected_shipping_methods,
			'type' => 'select',
			'options' => $this->get_all_shipping_methods(), //if the field is select, this param will be here
			'multiple' => true,
			'select2' => true,
			'class' => 'permitted_shipping_methods',
			'placeholder' => __('Apply Shipping Methods')
		] );

		$output .= '</div>';
		echo wp_kses( $output, RenderHelpers::getInstance()->Wp_Kses_Allowed_For_Forms() );

	}
}
