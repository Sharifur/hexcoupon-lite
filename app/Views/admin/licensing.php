<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
	<h2><?php esc_html_e( 'HexCoupon License Activation', 'hex-coupon-for-woocommerce' ); ?></h2>
	<form method="post" action="">
		<input type="hidden" name="hexcoupon_license_action" value="save_license">
		<?php
		wp_nonce_field( 'hexcoupon_nonce', 'hexcoupon_nonce' );
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'License Key', 'hex-coupon-for-woocommerce' ); ?></th>
				<td>
					<input id="hexcoupon_license_key" name="hexcoupon_license_key" type="text" class="regular-text" value="<?php echo esc_attr( get_option( 'hexcoupon_license_key' ) ); ?>" />
					<?php if ( get_option( 'hexcoupon_license_status' ) == 'valid' ) { ?>
						<span class="license-active"><?php esc_html_e( 'Active', 'hex-coupon-for-woocommerce' ); ?></span>
					<?php } else { ?>
						<span class="license-inactive"><?php esc_html_e( 'Inactive', 'hex-coupon-for-woocommerce' ); ?></span>
					<?php } ?>
				</td>
			</tr>
		</table>
		<?php submit_button( esc_html__( 'Save License', 'hexcoupon' ) ); ?>
	</form>
	<form method="post" action="">
		<?php
		wp_nonce_field( 'hexcoupon_nonce', 'hexcoupon_nonce' );
		if ( get_option( 'hexcoupon_license_status' ) == 'valid' ) { ?>
			<input type="hidden" name="hexcoupon_license_action" value="deactivate_license">
			<input type="submit" class="button-secondary" name="hexcoupon_deactivate" value="<?php esc_html_e( 'Deactivate License', 'hex-coupon-for-woocommerce' ); ?>"/>
		<?php } else { ?>
			<input type="hidden" name="hexcoupon_license_action" value="activate_license">
			<input type="submit" class="button-secondary" name="hexcoupon_activate" value="<?php esc_html_e( 'Activate License', 'hex-coupon-for-woocommerce' ); ?>"/>
		<?php } ?>
	</form>
</div>
