<?php

namespace hexcoupon\app\Controllers\WooCommerce\Admin;

use HexCoupon\App\Controllers\BaseController;
use hexcoupon\app\Core\Helpers\ValidationHelper;
use HexCoupon\App\Core\Lib\SingleTon;
use Kathamo\Framework\Lib\Http\Request;

class CouponUsageRestrictionTabController extends BaseController
{
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
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this, 'save_coupon_cart_condition' ] );
		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'apply_cart_condition' ], 10, 2 );
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this,'delete_post_meta' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_coupon_usage_restriction_meta_data
	 * @param $key
	 * @param $data_type
	 * @param $coupon_id
	 * @return mixed
	 * @since 1.0.0
	 * Save the coupon usage restriction meta-data.
	 */
	private function save_coupon_usage_restriction_meta_data( $key, $data_type, $coupon_id )
	{
		$validator = $this->validate( [
			$key => $data_type
		] );
		$error = $validator->error();
		if ( $error ) {

		}
		$data = $validator->getData();

		update_post_meta( $coupon_id, $key, $data[$key] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_coupon_cart_condition
	 * @param $coupon_id
	 * @return mixed
	 * @since 1.0.0
	 * Save the coupon cart condition.
	 */
	public function save_coupon_cart_condition( $coupon_id )
	{
		$meta_data_list = [
			[ 'apply_cart_condition_for_customer_on_products', 'string' ],
			[ 'apply_on_listed_product', 'string' ],
			[ 'all_selected_products', 'array' ],
			[ 'product_min_quantity', 'array' ],
			[ 'product_max_quantity', 'array' ],
			[ 'apply_cart_condition_for_customer_on_categories', 'string' ],
			[ 'all_selected_categories', 'array' ],
			[ 'allowed_or_restricted_customer_group', 'string' ],
			[ 'allowed_group_of_customer', 'string' ],
			[ 'selected_customer_group', 'array' ],
			[ 'allowed_or_restricted_individual_customer', 'string' ],
			[ 'allowed_individual_customer', 'string' ],
			[ 'selected_individual_customer', 'array' ],
		];

		foreach ( $meta_data_list as $meta_data ) {
			$this->save_coupon_usage_restriction_meta_data( $meta_data[0], $meta_data[1], $coupon_id );
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method apply_cart_condition_on_product
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * @since 1.0.0
	 * Apply/validate products cart condition.
	 */
	private function apply_cart_condition_on_product( $valid, $coupon )
	{

		$apply_cart_condition_on_products = get_post_meta( $coupon->get_id(), 'apply_cart_condition_for_customer_on_products', true );

		$all_selected_products = get_post_meta( $coupon->get_id(), 'all_selected_products', true );

		$apply_on_listed_product = get_post_meta( $coupon->get_id(), 'apply_on_listed_product', true );

		$cart_items = WC()->cart->get_cart();

		$product_id = [];

		if ( ! empty( $apply_cart_condition_on_products ) && 'yes' === $apply_cart_condition_on_products ) {

			if ( 'all_of_the_product' === $apply_on_listed_product ) {

				foreach ( $cart_items as $item => $key ) {
					$product_id[] = $key['product_id'];
				}

				$diff_result = array_diff( $product_id, $all_selected_products );
				$diff_result2 = array_diff( $all_selected_products, $product_id );

				if ( empty( $diff_result ) && empty( $diff_result2 ) ) {
					return $valid;
				} else {
					return false;
				}
			}

			if ( 'any_of_the_product' === $apply_on_listed_product ) {
				foreach ( $cart_items as $item => $key ) {
					if ( in_array( $key['product_id'], $all_selected_products ) ) {
						return $valid;
					}
				}
				return false;
			}

		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method apply_cart_condition_on_product
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * @since 1.0.0
	 * Apply/validate products categories cart condition.
	 */
	private function apply_cart_condition_on_categories( $valid, $coupon )
	{
		$apply_cart_condition_on_categories = get_post_meta( $coupon->get_id(), 'apply_cart_condition_for_customer_on_categories', true );

		$all_selected_categories = get_post_meta( $coupon->get_id(), 'all_selected_categories', true );

		$cart_items = WC()->cart->get_cart();

		$product_categories_id = [];

		foreach ( $cart_items as $cart_item_key => $cart_item ) {
			$product_id = $cart_item['product_id'];

			$categories = get_the_terms( $product_id, 'product_cat' );

			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				foreach ( $categories as $category ) {
					$product_categories_id[] = $category->term_id;
				}
			}
		}

		$product_categories_id = array_unique( $product_categories_id );

		// Check coupon apply on categories checkbox button is checked and apply on selected categories
		if ( ! empty( $apply_cart_condition_on_categories ) && 'yes' === $apply_cart_condition_on_categories ) {
			foreach ( $product_categories_id as $product_categories_single_id ) {
				if ( in_array( $product_categories_single_id, $all_selected_categories ) ) {
					return $valid;
				}
			}

			return false;

		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method apply_cart_condition_on_customer
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * @since 1.0.0
	 * Apply/validate products cart condition based on customer group or individual customer.
	 */
	private function apply_cart_condition_on_customer_grp( $valid, $coupon )
	{
		$allowed_or_restricted_customer_group = get_post_meta( $coupon->get_id(), 'allowed_or_restricted_customer_group', true );
		$allowed_group_of_customer = get_post_meta( $coupon->get_id(), 'allowed_group_of_customer', true );
		$selected_customer_group = get_post_meta( $coupon->get_id(), 'selected_customer_group', true );

		// Check if coupon allowed for selected customer group
		if ( ! empty( $allowed_or_restricted_customer_group ) && 'yes' === $allowed_or_restricted_customer_group ) {
			if ( 'allowed_for_groups' === $allowed_group_of_customer ) {
				$user = wp_get_current_user();
				$user_roles = $user->roles;

				foreach ( $user_roles as $user_role ) {
					if ( in_array( $user_role, $selected_customer_group ) ) {
						return $valid;
					}
				}
			}

			// Check if coupon restricted for selected customer group
			if ( 'restricted_for_groups' === $allowed_group_of_customer ) {
				$user = wp_get_current_user();
				$user_roles = $user->roles;

				foreach ( $user_roles as $user_role ) {
					if ( in_array( $user_role, $selected_customer_group ) ) {
						return false;
					}
				}
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method show_user_id
	 * @return array
	 * @since 1.0.0
	 * Retrieve the ID of all users.
	 */
	private function show_user_id()
	{
		// Query all users
		$args = [
			'fields' => 'all', // Get all fields of each user.
		];
		$user_query = new \WP_User_Query($args);

		$all_users_id = [];
		// Check if there are users found
		if ( ! empty( $user_query->results ) ) {
			// Loop through the users and retrieve their 'ID'.
			foreach ( $user_query->results as $user ) {
				$all_users_id[] = $user->ID;
			}
		}

		return $all_users_id;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method show_user_id
	 * @return bool
	 * @since 1.0.0
	 * Retrieve the ID of all users.
	 */
	private function apply_cart_condition_on_individual_customer( $valid, $coupon )
	{
		$allowed_or_restricted_individual_customer = get_post_meta( $coupon->get_id(), 'allowed_or_restricted_individual_customer', true );
		$allowed_individual_customer = get_post_meta( $coupon->get_id(), 'allowed_individual_customer', true );
		$selected_individual_customer = get_post_meta( $coupon->get_id(), 'selected_individual_customer', true );

		$all_users_id = $this->show_user_id();

		if ( ! empty( $allowed_or_restricted_individual_customer ) && 'yes' === $allowed_or_restricted_individual_customer ) {
			// Check if coupon allowed for selected customers
			if ( 'allowed_for_customers' === $allowed_individual_customer ) {
				foreach ( $all_users_id as $user_id ) {
					if ( in_array( $user_id, $selected_individual_customer ) ) {
						return $valid;
					}
				}
			}

			// Check if coupon restricted for selected customers
			if ( 'restricted_for_customers' === $allowed_individual_customer ) {
				foreach ( $all_users_id as $user_id ) {
					if ( in_array( $user_id, $selected_individual_customer ) ) {
						return false;
					}
				}
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method apply_cart_condition
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * @since 1.0.0
	 * Apply/validate the coupon cart condition.
	 */
	public function apply_cart_condition( $valid, $coupon )
	{
		$apply_cart_condition_on_product = $this->apply_cart_condition_on_product( $valid, $coupon );
		$apply_cart_condition_on_categories = $this->apply_cart_condition_on_categories( $valid, $coupon );
		$apply_cart_condition_on_customer_grp = $this->apply_cart_condition_on_customer_grp( $valid, $coupon );
		$apply_cart_condition_on_individual_customer = $this->apply_cart_condition_on_individual_customer( $valid, $coupon );

		if ( is_null( $apply_cart_condition_on_product ) || is_null( $apply_cart_condition_on_categories ) || is_null( $apply_cart_condition_on_customer_grp ) || is_null( $apply_cart_condition_on_individual_customer ) ) {

			echo 'product '.$apply_cart_condition_on_product.', categories '.$apply_cart_condition_on_categories.', customer group '.$apply_cart_condition_on_customer_grp.', individual customer '.$apply_cart_condition_on_individual_customer.' are returning null. <br>';

			if ( $apply_cart_condition_on_product || $apply_cart_condition_on_categories || $apply_cart_condition_on_customer_grp || $apply_cart_condition_on_individual_customer )	{

				echo 'apply_cart_condition_on_product is returning '.$apply_cart_condition_on_product.'<br>';
				echo 'apply_cart_condition_on_categories is returning '.$apply_cart_condition_on_categories.'<br>';
				echo 'apply_cart_condition_on_customer_grp is returning '.$apply_cart_condition_on_customer_grp.'<br>';
				echo 'apply_cart_condition_on_individual_customer is returning '.$apply_cart_condition_on_individual_customer.'<br>';

				return $valid;

			}

			echo 'cart condition is returning false. <br>';

			return $valid;
		}

		if ( $apply_cart_condition_on_product && $apply_cart_condition_on_categories && $apply_cart_condition_on_customer_grp && $apply_cart_condition_on_individual_customer )	{

			echo 'product cart condition is returning '.$apply_cart_condition_on_product.' <brs>';
			echo 'categories cart condition is returning '.$apply_cart_condition_on_categories.' <brs>';
			echo 'customer group cart condition is returning '.$apply_cart_condition_on_customer_grp.' <brs>';
			echo 'individual customer cart condition is returning '.$apply_cart_condition_on_individual_customer.' <brs>';

			return $valid;

		}

		echo 'cart condition is returning false. <br>';

		return false;

	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method delete_post_meta
	 * @param int $coupon_id
	 * @return mixed
	 * @since 1.0.0
	 * Delete post meta data.
	 */
	public function delete_post_meta( $coupon_id )
	{
		$meta_keys = [
			'apply_cart_condition_for_customer_on_products' => [
				'apply_on_listed_product',
				'all_selected_products'
			],
			'apply_cart_condition_for_customer_on_categories' => [
				'all_selected_categories'
			],
			'allowed_or_restricted_customer_group' => [
				'allowed_group_of_customer',
				'selected_customer_group'
			],
			'allowed_or_restricted_individual_customer' => [
				'allowed_individual_customer',
				'selected_individual_customer'
			],
		];

		foreach ( $meta_keys as $meta_key => $related_meta_keys ) {
			$meta_value = get_post_meta( $coupon_id, $meta_key, true );

			if ( empty( $meta_value ) ) {
				foreach ( $related_meta_keys as $related_meta_key ) {
					delete_post_meta( $coupon_id, $related_meta_key );
				}
			}
		}


	}


}
