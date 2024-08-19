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
        <div class="spinToWin">
            <div class="container">
                <div class="spinToWin-wraper">
                    <div class="close">
                        <i class="fa-regular fa-rectangle-xmark"></i>
                    </div>
                    <div class="row g-4">
                        <div class="col-xl-6">
                            <div class="spinner">
                                <div class="spinner-wraper mx-auto">
                                    <div class="wheel mx-auto">
                                        <?php
                                            $spin_wheel_content = get_option( 'spinWheelContent' );
                                            $coupon_type1 = $spin_wheel_content['content1']['couponType'];
                                            $coupon_type2 = $spin_wheel_content['content2']['couponType'];
                                            $coupon_type3 = $spin_wheel_content['content3']['couponType'];
                                            $coupon_type4 = $spin_wheel_content['content4']['couponType'];
                                        ?>
                                        <div class="slice" style="--i: 1">
                                        <p class="value text bankrupt">
                                            <?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $coupon_type1 ) ); ?>
                                        </p>
                                        </div>
                                        <div class="slice" style="--i: 2">
                                        <p class="value"><?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $coupon_type2 ) ); ?></p>
                                        </div>
                                        <div class="slice" style="--i: 3">
                                        <p class="value"><?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $coupon_type3 ) ); ?></p>
                                        </div>
                                        <div class="slice" style="--i: 4">
                                        <p class="value"><?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $coupon_type4 ) ); ?></p>
                                        </div>
                                        <div class="slice" style="--i: 5">
                                        <p class="value"><?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $coupon_type1 ) ); ?></p>
                                        </div>
                                        <div class="slice" style="--i: 6">
                                        <p class="value"><?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $coupon_type2 ) ); ?></p>
                                        </div>
                                        <div class="slice" style="--i: 7">
                                        <p class="value"><?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $coupon_type3 ) ); ?></p>
                                        </div>
                                        <div class="slice" style="--i: 8">
                                        <p class="value"><?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $coupon_type4 ) ); ?></p>
                                        </div>
                                        <div class="slice" style="--i: 9">
                                        <p class="value text lose-turn"><?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $coupon_type1 ) ); ?></p>
                                        </div>
                                        <div class="slice" style="--i: 10">
                                        <p class="value"><?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $coupon_type2 ) ); ?></p>
                                        </div>
                                        <div class="slice" style="--i: 11">
                                        <p class="value"><?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $coupon_type3 ) ); ?></p>
                                        </div>
                                        <div class="slice" style="--i: 12">
                                        <p class="value"><?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $coupon_type4 ) ); ?></p>
                                        </div>
                                    </div>
                                    <div class="svg">
                                        <svg width="103" height="73" viewBox="0 0 103 73" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g filter="url(#filter0_d_4323_23)">
                                            <path d="M1.11994 26.381C-3.51777 44.9818 7.84264 63.8887 26.4435 68.5264C32.0275 69.9187 37.9121 69.903 43.4845 68.4734L100.776 53.1875C101.54 52.9843 102.132 52.3817 102.323 51.6138C102.515 50.8459 102.275 50.0359 101.696 49.4979L58.2728 9.09014C54.0378 5.22615 48.8492 2.4496 43.2652 1.05736C24.6646 -3.58031 5.75766 7.7801 1.11994 26.381ZM51.7216 38.9974C49.4028 48.2978 39.9494 53.9779 30.649 51.6591C21.3486 49.3402 15.6684 39.8869 17.9873 30.5865C20.3062 21.286 29.7595 15.6059 39.0599 17.9248C48.3603 20.2436 54.0405 29.6969 51.7216 38.9974Z" fill="#F70707"/>
                                            </g>
                                            <defs>
                                            <filter id="filter0_d_4323_23" x="0.0839844" y="0.0206299" width="102.305" height="72.5377" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                            <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                            <feOffset dy="3"/>
                                            <feComposite in2="hardAlpha" operator="out"/>
                                            <feColorMatrix type="matrix" values="0 0 0 0 0.820833 0 0 0 0 0.820833 0 0 0 0 0.820833 0 0 0 1 0"/>
                                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_4323_23"/>
                                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_4323_23" result="shape"/>
                                            </filter>
                                            </defs>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="text-part mx-auto">
                                <div class="heading-part">
                                    <h3 class="heading"><?php esc_html_e( 'SPIN TO WIN!', 'hex-coupon-for-woocommerce' ); ?></h3>
                                    <p><?php echo wp_kses( $spin_wheel_wheel['wheelDescription'] , $this->allowed_html ); ?></p>
                                </div>
                                <form action="#" method="get">
                                <?php if ( $spin_wheel_wheel['enableYourName'] == true ) : ?>
                                    <input type="text" class="custom-input mb-3" name="name" id="name" placeholder="<?php printf( esc_attr__( '%s', 'hex-coupon-for-woocommerce' ), esc_attr( $spin_wheel_wheel['yourName'] ) ); ?>" required>
                                <?php endif; ?>
                                <?php if ( $spin_wheel_wheel['enablePhoneNumber'] == true ) : ?>
                                    <input type="email" class="custom-input" name="email" id="email" placeholder="<?php printf( esc_attr__( '%s', 'hex-coupon-for-woocommerce' ), esc_attr( $spin_wheel_wheel['phoneNumber'] ) ); ?>" required>
                                <?php endif; ?>
                                <div class="button-wraper">
                                    <button type="button" class="try-your-luck"><?php printf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $spin_wheel_wheel['buttonText'] ) ); ?></button>
                                </div>
                                    <div class="accept-agree">
                                        <input type="checkbox">
                                        <span><?php esc_html_e( 'I Agree With The', 'hex-coupon-for-woocommerce' ); ?></span>
                                        <a href="#/" class="termCondition"><?php esc_html_e( 'Term And Condition', 'hex-coupon-for-woocommerce' ); ?></a>
                                    </div>
                                    <div class="openion">
                                        <?php echo wp_kses( $spin_wheel_wheel['gdprMessage'], $this->allowed_html ); ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>	
        <?php 
        endif;
    }

}