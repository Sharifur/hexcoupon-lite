<?php
namespace HexCoupon\App\Controllers\WooCommerce\LoyaltyProgram;

use HexCoupon\App\Core\Lib\SingleTon;

class EnablePointsOnSocialShare
{
	use SingleTon;

	/**
	 * Registering hooks that are needed
	 */
	public function register()
	{
		add_action( 'woocommerce_after_single_product_summary', [ $this, 'add_social_share_buttons' ], 20 );
		add_action( 'wp_ajax_award_points_for_share', [ $this, 'award_points_for_share' ] );
		add_action( 'wp_ajax_nopriv_award_points_for_share', [ $this, 'award_points_for_share' ] );
	}

	public function add_social_share_buttons()
	{
		if ( is_product() ) {
			global $product;
			$product_id = $product->get_id();
			$product_url = get_permalink( $product_id );
			$product_title = get_the_title( $product_id );

			echo '<p>Share on Social Media:</p>';
			echo '<div class="social-share-buttons">';
			// Facebook Share Button
			echo '<a href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode( $product_url ) . '&t=' . urlencode( $product_title ) . '" target="_blank" data-product-id="' . $product_id . '">';
			echo '<img width="40" height="40" src="https://www.pikpng.com/pngl/m/222-2220526_like-share-follow-and-subscribe-transparent-background-facebook.png" alt="Share on Facebook" />';
			echo '<i class="fa-brands fa-facebook-f"></i>';
			echo '</a>';
			// Twitter Share Button
			echo '<a href="https://twitter.com/intent/tweet?url=' . urlencode( $product_url ) . '&text=' . urlencode( $product_title ) . '" target="_blank" data-product-id="' . $product_id . '">';
			echo '<img width="40" height="40" src="https://www.clipartmax.com/png/middle/206-2067679_icon-twitter%403x-twitter-share-button.png" alt="Share on Twitter" />';
			echo '<i class="lab la-twitter"></i>';
			echo '</a>';
			// LinkedIn Share Button
			echo '<a href="https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode( $product_url ) . '&title=' . urlencode( $product_title ) . '" target="_blank" data-product-id="' . $product_id . '">';
			echo '<img width="40" height="40" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSgp1b6kjWUD04Wq-sj6aFLLqj_I_pDepYQ4A&s" alt="Share on LinkedIn" />';
			echo '<i class="lab la-linkedin"></i>';
			echo '</a>';
			echo '</div>';
		}
	}

	public function award_points_for_share() {
		if ( isset( $_POST['product_id'] ) ) {
			$product_id = intval( $_POST['product_id'] );
			$user_id = get_current_user_id();

			if ( $user_id ) {
				$shared_products = get_user_meta( $user_id, 'shared_products', true );
				$shared_products = $shared_products ? $shared_products : [];

				if ( !in_array( $product_id, $shared_products ) ) {

					$points = 7;
					EnablePointsOnReview::getInstance()->give_loyalty_points( $user_id, $points, 6 ); // here 6 represents social share reason

					$shared_products[] = $product_id;
					update_user_meta( $user_id, 'shared_products', $shared_products );

					wp_send_json_success( 'Points awarded.' );
				} else {
					wp_send_json_error( 'You have already shared this product.' );
				}
			} else {
				wp_send_json_error( 'User not logged in.' );
			}
		} else {
			wp_send_json_error( 'Invalid product ID.' );
		}

		wp_die();
	}
}
