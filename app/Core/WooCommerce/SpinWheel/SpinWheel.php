<?php
namespace HexCoupon\App\Core\WooCommerce\SpinWheel;

use HexCoupon\App\Core\Lib\SingleTon;

class SpinWheel
{
	use SingleTon;

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
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return void
	 * @since 1.0.0
	 * Add all hooks that are needed.
	 */
	public function register()
	{
        // Hook into wp_footer to insert the popup HTML at the end of the page
        add_action( 'wp_footer', [ $this, 'hexcoupon_spin_wheel' ] );
	}
    
    /**
	 * @package hexcoupon
	 * @author WpHex
	 * @method hexcoupon_spin_wheel
	 * @return void
	 * @since 1.0.0
	 * Markup for spin wheel
	 */
    public function hexcoupon_spin_wheel()
    {
        $spin_wheel_popup = get_option( 'spinWheelPopup' );
        $spin_wheel_wheel = get_option( 'spinWheelWheel' );

        function is_blog () {
            return ( is_archive() || is_author() || is_category() || is_home() || is_single() || is_tag()) && 'post' == get_post_type();
        }

        if ( is_home() && $spin_wheel_popup['showOnlyHomepage'] == 1 || is_blog() && $spin_wheel_popup['showOnlyBlogPage'] == 1 || is_shop() && $spin_wheel_popup['showOnlyShopPage'] == 1 ) :
        ?>
         <!-- Popup Modal -->
        <div id="popup" class="popup-container">
            <div class="popup-content">
                <span id="closePopup" class="close">&times;</span>
                <div class="spin-wheel-container">
                    <div class="wheel" id="wheel">
                        <div class="wheel-segment segment-1"><?php esc_html_e( '20% OFF', 'hex-coupon-for-woocommerce' )?></div>
                        <div class="wheel-segment segment-2"><?php esc_html_e( 'Not Lucky', 'hex-coupon-for-woocommerce' )?></div>
                        <div class="wheel-segment segment-3"><?php esc_html_e( '$10', 'hex-coupon-for-woocommerce' ); ?></div>
                        <div class="wheel-segment segment-4"><?php esc_html_e( 'Not Lucky', 'hex-coupon-for-woocommerce' )?></div>
                        <div class="wheel-segment segment-5"><?php esc_html_e( '10% OFF', 'hex-coupon-for-woocommerce' ); ?></div>
                        <div class="wheel-segment segment-6"><?php esc_html_e( 'Not Lucky', 'hex-coupon-for-woocommerce' )?></div>
                        <div class="wheel-segment segment-7"><?php esc_html_e( '$5', 'hex-coupon-for-woocommerce' ); ?></div>
                        <div class="wheel-segment segment-8"><?php esc_html_e( '10% OFF', 'hex-coupon-for-woocommerce' ); ?></div>
                    </div>
                    <div class="wheel-pointer"></div>
                    <div class="spin-button" id="spinButton">
                        <div class="inner-circle"></div>
                    </div>
                </div>
                <div class="form-container">
                    <h2><?php esc_html_e( 'SPIN TO WIN!', 'hex-coupon-for-woocommerce' ); ?></h2>
                    <p><?php echo wp_kses( $spin_wheel_wheel['wheelDescription'] , $this->allowed_html ); ?></p>
                    <form>
                        <?php if ( $spin_wheel_wheel['enableYourName'] == true ) : ?>
                        <input type="text" placeholder="<?php printf( esc_attr__( '%s', 'hex-coupon-for-woocommerce' ), esc_attr( $spin_wheel_wheel['yourName'] ) ); ?>" required>
                        <?php endif; ?>
                        <?php if ( $spin_wheel_wheel['enablePhoneNumber'] == true ) : ?>
                        <input type="tel" placeholder="<?php printf( esc_attr__( '%s', 'hex-coupon-for-woocommerce' ), esc_attr( $spin_wheel_wheel['phoneNumber'] ) ); ?>" required>
                        <?php endif; ?>
                        <?php if ( $spin_wheel_wheel['enableEmailAddress'] == true ) : ?>
                        <input type="email" placeholder="<?php printf( esc_attr__( '%s', 'hex-coupon-for-woocommerce' ), esc_attr( $spin_wheel_wheel['emailAddress'] ) ); ?>" required>
                        <?php endif; ?>
                        <button type="submit"><?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $spin_wheel_wheel['buttonText'] ) ); ?></button>
                        <label><input type="checkbox" required> <?php esc_html_e( 'I agree with the', 'hex-coupon-for-woocommerce' ); ?> <a href="#"><?php esc_html_e( 'terms and conditions', 'hex-cooupon-for-woocommerce' ); ?></a></label>
                    </form>
                    <div class="reminder-options">
                        <?php echo wp_kses( $spin_wheel_wheel['gdprMessage'], $this->allowed_html ); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php 
        endif;
    }

}