<?php
namespace HexCoupon\App\Core\WooCommerce;

use HexCoupon\App\Core\Lib\SingleTon;

class CouponDuplicatePost
{
	use singleton;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return mixed
	 * @since 1.0.0
	 * Registers all hooks that are needed to create 'Coupon' duplicate post button functionality.
	 */
	public function register()
	{
		add_filter( 'post_row_actions', [ $this, 'coupon_duplicate_post_link' ], 10, 2 );
		add_action( 'admin_action_coupon_duplicate_post_draft', [ $this, 'coupon_duplicate_post_draft' ] );
		add_action( 'admin_notices', [ $this, 'coupon_duplication_admin_notice' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return mixed
	 * @param $actions
	 * @param $post
	 * @since 1.0.0
	 * Adds a duplicate button.
	 */
	public function coupon_duplicate_post_link( $actions, $post )
	{

		if ( ! current_user_can( 'edit_posts' ) ) {
			return $actions;
		}

		// Check if the post type is 'shop_coupon'
		if ( $post->post_type !== 'shop_coupon' ) {
			return $actions;
		}

		$url = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'coupon_duplicate_post_draft',
					'shop_coupon' => $post->ID,
				),
				'admin.php'
			),
			basename( __FILE__ ),
			'duplicate_nonce'
		);

		$actions['duplicate'] = '<a href="' . esc_url( $url ) . '" title="Duplicate this item" rel="permalink">' . esc_html__( 'Duplicate Coupon', 'hex-coupon-for-woocommerce' ) . '</a>';

		return $actions;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method coupon_duplicate_post_draft
	 * @return mixed
	 * @since 1.0.0
	 * Duplicates a coupon after clicking the duplicate button.
	 */
	public function coupon_duplicate_post_draft()
	{
		$duplicate_nonce = sanitize_text_field( $_GET['duplicate_nonce'] );
		$shop_coupon = sanitize_text_field( $_GET['shop_coupon'] );

		// check if post ID and action has been provided
		if ( empty ( $shop_coupon ) ) {
			wp_die( 'No post to duplicate has been provided!' );
		}

		// Verify nonce
		if ( ! isset( $duplicate_nonce ) || ! wp_verify_nonce( $duplicate_nonce, basename( __FILE__ ) ) ) {
			return;
		}

		// Get original post id
		$post_id = absint( $shop_coupon );

		// Get all the original post data
		$post = get_post( $post_id );

		/*
		 * if you don't want current user to be the new post author,
		 * then change next couple of lines to this: $new_post_author = $post->post_author;
		 */
		$current_user = wp_get_current_user();
		$new_post_author = $current_user->ID;

		// If post data exists, create the post duplicate
		if ( $post ) {
			// New post data array
			$args = [
				'comment_status' => $post->comment_status,
				'ping_status' => $post->ping_status,
				'post_author' => $new_post_author,
				'post_content' => $post->post_content,
				'post_excerpt' => $post->post_excerpt,
				'post_name' => $post->post_name,
				'post_parent' => $post->post_parent,
				'post_password' => $post->post_password,
				'post_status' => 'draft',
				'post_title' => $post->post_title,
				'post_type' => $post->post_type,
				'to_ping' => $post->to_ping,
				'menu_order' => $post->menu_order,
			];

			// Insert the post by wp_insert_post() function
			$new_post_id = wp_insert_post( $args );

			// Get all current post terms ad set them to the new post draft
			$taxonomies = get_object_taxonomies( get_post_type($post) ); // returns array of taxonomy names for post type, ex array("category", "post_tag");
			if ( $taxonomies ) {
				foreach ( $taxonomies as $taxonomy ) {
					$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
					wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
				}
			}

			// Duplicate all post meta
			$post_meta = get_post_meta( $post_id );
			if ( $post_meta ) {
				foreach ( $post_meta as $meta_key => $meta_values ) {
					if ( '_wp_old_slug' == $meta_key ) {
						continue;
					}

					foreach ( $meta_values as $meta_value ) {
						update_post_meta( $new_post_id, $meta_key, $meta_value );
					}
				}
			}

			// Safely redirect to the 'All Coupons' page.
			wp_safe_redirect(
				add_query_arg(
					array(
						'post_type' => 'shop_coupon',
						'saved' => 'post_duplication_created' // just a custom slug here
					),
					admin_url( 'edit.php' )
				)
			);
			exit;

		} else {
			wp_die( 'Post creation failed, could not find original post.' );
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method coupon_duplication_admin_notice
	 * @return mixed
	 * @since 1.0.0
	 * Displays an admin notice after a coupon is duplicated.
	 */
	public function coupon_duplication_admin_notice()
	{
		// Get the current screen
		$screen = get_current_screen();

		if ( 'edit' !== $screen->base ) {
			return;
		}
		$saved = isset( $_GET[ 'saved' ] ) ? sanitize_text_field( $_GET[ 'saved' ] ) : '';
		// Checks if settings updated
		if ( isset( $saved ) && 'post_duplication_created' == $saved ) {
			?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html__( 'Duplicate Coupon Created.', 'hex-coupon-for-woocommerce' ); ?></p></div>
			<?php
		}
	}
}




