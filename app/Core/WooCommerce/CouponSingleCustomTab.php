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
	 * @return string
	 * @since 1.0.0
	 * Registers all hooks that are needed to create custom tab on 'Coupon Single' page.
	 */
	public function register()
	{
		add_filter( 'woocommerce_coupon_data_panels', [ $this, 'add_custom_coupon_tab' ] );
		add_action( 'woocommerce_coupon_data_tabs', [ $this, 'add_custom_coupon_tab_content' ] );
		// Hook into the 'save_post' action to save the selected values

//		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'apply_selected_payments_method_to_coupon' ], 10, 2 );
		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'apply_coupon_to_selected_user_roles' ], 10, 2 );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method get_user_role_names
	 * @return array
	 * @since 1.0.0
	 * Retrives all available role names.
	 */
	private function get_user_role_names()
	{
		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		return $wp_roles->get_names();
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
			'label'    => __( 'HexCoupon', 'hexcoupon' ),
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
	 * Retrieve all payment methods of WooCommerce
	 */
	public function get_all_payment_methods() {
		$payment_gateways = WC()->payment_gateways->payment_gateways();
		$payment_options = array();

		foreach ( $payment_gateways as $gateway_id => $gateway ) {
			$payment_options[ $gateway_id ] = $gateway->get_title();
		}

		return $payment_options;
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @method display_payment_method_options
	 * @return string
	 * @since 1.0.0
	 * Displays the payment method options in a Select2 field.
	 */
	public function display_payment_method_options()
	{
		$payment_methods = $this->get_all_payment_methods();
		$selected_payment_methods = get_post_meta( get_the_ID(),'permitted_methods',true );
		if ( ! empty( $payment_methods ) ) {
			echo '<select name="permitted_methods[]" id="permitted_methods" class="short permitted_roles_select2" multiple="multiple">';
			foreach ( $payment_methods as $method_id => $method_label ) {
				$selected = in_array( $method_id, $selected_payment_methods ) ? 'selected' : '';
				echo '<option value="' . esc_attr( $method_id ) . '" ' . esc_attr( $selected ) . ' >' . esc_html( $method_label ) . '</option>';
			}
			echo '</select>';
		} else {
			echo 'No payment methods found.';
		}
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

		$selected_permitted_roles = get_post_meta( get_the_ID(), 'permitted_roles', true );

		$output .= FormHelpers::Init([
			'label' => esc_html__( 'Apply Roles', 'hexcoupon' ),
			'name' => 'permitted_roles',
			'value' => $selected_permitted_roles,
			'type' => 'select',
			'options' => $this->get_user_role_names(),//if the field is select, this param will be here
			'multiple' => true,
			'select2' => true
		]);

		$output .= '</div>';
		echo wp_kses($output, RenderHelpers::getInstance()->Wp_Kses_Allowed_For_Forms());
	?>

	<?php
	}


	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @method apply_coupon_to_selected_payment_methods
	 * @param mixed $valid
	 * @param string $coupon
	 * @since 1.0.0
	 * @return bool
	 * Apply coupon to user selected payment methods only.
	 */
	function apply_selected_payments_method_to_coupon( $valid, $coupon ) : bool
	{
		$selected_permitted_payment_methods = get_post_meta( $coupon->get_id(), 'permitted_methods', true );
		if ( empty( $selected_permitted_payment_methods ) ) {
			return $valid;
		}

		$current_payment_method = WC()->session->get( 'chosen_payment_method' );

		if ( in_array( $current_payment_method, $selected_permitted_payment_methods ) ) {
			return $valid;
		}

		return false;
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @method apply_selected_roles_to_coupon
	 * @param bool $is_valid
	 * @param string $coupon
	 * @since 1.0.0
	 * @return bool
	 * Apply coupon to the selected roles only.
	 */
	function apply_coupon_to_selected_user_roles( $is_valid, $coupon ) : bool
	{
		//check if it is a valid  coupon or not
		if ( ! $is_valid ) return $is_valid;






		//get current role
		$selected_roles = get_post_meta( $coupon->get_id(), 'permitted_roles', true );

		//check it is does not have any role assigned
		if (empty($selected_roles)) return true;





		$user = wp_get_current_user();
		$user_roles = $user->roles;
//		print_r($user_roles);

		dd( $selected_roles,$user_roles);

		foreach ( $user_roles as $user_role ) {
			if ( in_array( $user_role, $selected_roles ) ) {
				return $is_valid;
			}
		}

		return false;
	}
}





