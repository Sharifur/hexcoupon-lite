<?php
namespace HexCoupon\App\Core\WooCommerce;

use HexCoupon\App\Core\Helpers\FormHelpers;
use HexCoupon\App\Core\Helpers\RenderHelpers;
use HexCoupon\App\Core\Lib\SingleTon;

class CouponSingleUsageLimits {

	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return void
	 * @since 1.0.0
	 * Register hook that is needed to validate the coupon.
	 */
	public function register()
	{
		add_action( 'woocommerce_coupon_options_usage_limit', [ $this, 'coupon_usage_limit_meta_fields' ], 10, 1 );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method coupon_usage_limit_meta_fields
	 * @return void
	 * @since 1.0.0
	 * Register hook that is needed to validate the coupon.
	 */
	public function coupon_usage_limit_meta_fields()
	{
		global $post;

		$reset_usage_limit = get_post_meta( $post->ID, 'reset_usage_limit', true );
		$reset_option_value = get_post_meta( $post->ID, 'reset_option_value', true );

		woocommerce_wp_checkbox(
			[
				'id' => 'reset_usage_limit',
				'label' => esc_html__( 'Reset Usage', 'hexcoupon' ),
				'description' => esc_html__( 'Check this box to reset usage limit after a period', 'hexcoupon' ),
				'value' => $reset_usage_limit,
			]
		);

		// Add a hidden input field to store the selected reset option value
		echo '<input type="hidden" id="reset_option_value" name="reset_option_value" value="'. esc_attr( $reset_option_value ) .'" />';

		echo '<div class="options_group reset_limit">';
		?>
			<p data-reset-value="annually">Reset Annually</p>
			<p data-reset-value="monthly">Reset Monthly</p>
			<p data-reset-value="weekly">Reset Weekly</p>
			<p data-reset-value="daily">Reset Daily</p>
		<?php
		echo '</div>';
	}
}
