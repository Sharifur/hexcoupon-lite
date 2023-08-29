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
	 * @since 1.0.0
	 * @method register
	 * @return void
	 * Register hook that is needed to validate the coupon.
	 */
	public function register()
	{
		add_action( 'wp_loaded', [ $this, 'get_all_post_meta' ] );
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this, 'save_coupon_cart_condition' ] );
		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'apply_cart_condition' ], 10, 2 );
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this,'delete_post_meta' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method get_all_post_meta
	 * @return array
	 * Get all coupon meta values
	 */
	public function get_all_post_meta( $coupon )
	{
		$all_meta_data = [];

		$meta_fields_data = [
			'apply_cart_condition_for_customer_on_products',
			'all_selected_products',
			'apply_on_listed_product',
			'apply_cart_condition_for_customer_on_categories',
			'all_selected_categories',
			'allowed_or_restricted_customer_group',
			'allowed_group_of_customer',
			'selected_customer_group',
			'allowed_or_restricted_individual_customer',
			'allowed_individual_customer',
			'selected_individual_customer',
		];

		foreach( $meta_fields_data as $meta_value ) {
			$all_meta_data[$meta_value] = get_post_meta( $coupon, $meta_value, true );
		}

		return $all_meta_data;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method save_coupon_usage_restriction_meta_data
	 * @param string $key
	 * @param string $data_type
	 * @param int $coupon_id
	 * @return void
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
	 * @since 1.0.0
	 * @method save_coupon_cart_condition
	 * @param int $coupon_id
	 * @return void
	 * Save the coupon cart condition.
	 */
	public function save_coupon_cart_condition( $coupon_id )
	{
		// initialize the array
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

		// save coupon usage restriction meta field values
		foreach ( $meta_data_list as $meta_data ) {
			$this->save_coupon_usage_restriction_meta_data( $meta_data[0], $meta_data[1], $coupon_id );
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method apply_cart_condition_on_product
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * Apply/validate products cart condition.
	 */
	private function apply_cart_condition_on_product( $valid, $coupon )
	{
		$all_meta_values = $this->get_all_post_meta( $coupon ); // get coupon all meta values

		// get value of 'apply_cart_condition_for_customer_on_products' meta field
		$apply_cart_condition_on_products = $all_meta_values['apply_cart_condition_for_customer_on_products'];

		// get value of 'all_selected_products' meta field
		$all_selected_products = $all_meta_values['all_selected_products'];

		// get value of 'apply_on_listed_product' meta field
		$apply_on_listed_product = $all_meta_values['apply_on_listed_product'];

		// get all cart items
		$cart_items = WC()->cart->get_cart();

		$product_id = []; // initialize an empty array

		if ( ! empty( $apply_cart_condition_on_products ) && 'yes' === $apply_cart_condition_on_products ) {
			if ( 'all_of_the_product' === $apply_on_listed_product ) {
				foreach ( $cart_items as $item => $key ) {
					$product_id[] = $key['product_id'];
				}

				$diff_result = array_diff( $product_id, $all_selected_products );
				$diff_result2 = array_diff( $all_selected_products, $product_id );

				if ( empty( $diff_result ) && empty( $diff_result2 ) ) {
					return $valid;
				}
				else {
					// display a custom coupon error message if the coupon is invalid
					add_filter( 'woocommerce_coupon_error', [ $this, 'invalid_error_message_for_not_matching_all_products' ] , 10, 3 );
					return false;
				}
			}

			if ( 'any_of_the_product' === $apply_on_listed_product ) {
				foreach ( $cart_items as $item => $key ) {
					if ( ! empty( $all_selected_products ) ) {
						if ( in_array( $key['product_id'], $all_selected_products ) ) {
							return $valid;
						}
					}

					else {
						// display a custom coupon error message if the coupon is invalid
						add_filter( 'woocommerce_coupon_error', [ $this, 'invalid_error_message_for_not_matching_any_of_the_products' ] , 10, 3 );
					}
				}
				return false;
			}

		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method apply_cart_condition_on_categories
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * Apply/validate products categories cart condition.
	 */
	private function apply_cart_condition_on_categories( $valid, $coupon )
	{
		$all_meta_values = $this->get_all_post_meta( $coupon );

		// get the value of 'apply_cart_condition_for_customer_on_categories' meta field
		$apply_cart_condition_on_categories = $all_meta_values['apply_cart_condition_for_customer_on_categories'];

		// get the value of 'all_selected_categories'
		$all_selected_categories = $all_meta_values['all_selected_categories'];

		// get all cart items
		$cart_items = WC()->cart->get_cart();

		$product_categories_id = []; // initialize an empty array

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
				else {
					// display a custom coupon error message if the coupon is invalid
					add_filter( 'woocommerce_coupon_error', [ $this, 'invalid_error_message_for_not_matching_with_product_categories' ] , 10, 3 );
				}
			}

			return false;
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method apply_cart_condition_on_customer_grp
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * Apply/validate products cart condition based on customer group or individual customer.
	 */
	private function apply_cart_condition_on_customer_grp( $valid, $coupon )
	{
		$all_meta_values = $this->get_all_post_meta( $coupon );

		$allowed_or_restricted_customer_group = $all_meta_values['allowed_or_restricted_customer_group'];
		$allowed_group_of_customer = $all_meta_values['allowed_group_of_customer'];
		$selected_customer_group = $all_meta_values['selected_customer_group'];

		// Check if coupon allowed for selected customer group
		if ( ! empty( $allowed_or_restricted_customer_group ) && 'yes' === $allowed_or_restricted_customer_group ) {
			if ( 'allowed_for_groups' === $allowed_group_of_customer ) {
				$user = wp_get_current_user();
				$user_roles = $user->roles;

				foreach ( $user_roles as $user_role ) {
					if ( in_array( $user_role, $selected_customer_group ) ) {
						return $valid;
					}
					else {
						// display a custom coupon error message if the coupon is invalid
						add_filter( 'woocommerce_coupon_error', [ $this, 'invalid_error_message_for_allowed_grp_of_user' ] , 10, 3 );
					}
				}
			}

			// Check if coupon restricted for selected customer group
			if ( 'restricted_for_groups' === $allowed_group_of_customer ) {
				$user = wp_get_current_user();
				$user_roles = $user->roles;

				foreach ( $user_roles as $user_role ) {
					if ( in_array( $user_role, $selected_customer_group ) ) {
						// display a custom coupon error message if the coupon is invalid
						add_filter( 'woocommerce_coupon_error', [ $this, 'invalid_error_message_for_disallowed_grp_of_user' ] , 10, 3 );

						return false;
					}
				}
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method apply_cart_condition_on_individual_customer
	 * @return bool
	 * @since 1.0.0
	 * Apply/validate cart condition on individual customer
	 */
	private function apply_cart_condition_on_individual_customer( $valid, $coupon )
	{
		$all_meta_values = $this->get_all_post_meta( $coupon );

		$allowed_or_restricted_individual_customer = $all_meta_values['allowed_or_restricted_individual_customer'];
		$allowed_individual_customer = $all_meta_values['allowed_individual_customer'];
		$selected_individual_customer = $all_meta_values['selected_individual_customer'];

		$current_user_id = get_current_user_id(); // get current logged-in user id

		if ( ! empty( $allowed_or_restricted_individual_customer ) && 'yes' === $allowed_or_restricted_individual_customer ) {
			// Check if coupon allowed for selected customers
			if ( 'allowed_for_customers' === $allowed_individual_customer ) {
				if ( in_array( $current_user_id, $selected_individual_customer ) ) {
					return $valid;
				}
				else {
					// display a custom coupon error message if the coupon is invalid
					add_filter( 'woocommerce_coupon_error', [ $this, 'invalid_error_message_for_individual_user' ] , 10, 2 );
				}
			}

			// Check if coupon restricted for selected customers
			if ( 'restricted_for_customers' === $allowed_individual_customer ) {
				if ( in_array( $current_user_id, $selected_individual_customer ) ) {
					// display a custom coupon error message if the coupon is invalid
					add_filter( 'woocommerce_coupon_error', [ $this, 'invalid_error_message_for_individual_user' ] , 10, 2 );
					return false;
				}
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method apply_cart_condition
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * Apply/validate the coupon cart condition.
	 */
	public function apply_cart_condition( $valid, $coupon )
	{
		$apply_cart_condition_on_product = $this->apply_cart_condition_on_product( $valid, $coupon );
		$apply_cart_condition_on_categories = $this->apply_cart_condition_on_categories( $valid, $coupon );
		$apply_cart_condition_on_customer_grp = $this->apply_cart_condition_on_customer_grp( $valid, $coupon );
		$apply_cart_condition_on_individual_customer = $this->apply_cart_condition_on_individual_customer( $valid, $coupon );

		if ( is_null( $apply_cart_condition_on_product ) && is_null( $apply_cart_condition_on_categories ) && is_null( $apply_cart_condition_on_customer_grp ) && is_null( $apply_cart_condition_on_individual_customer ) ) {
			return $valid;
		}

		elseif ( false === $apply_cart_condition_on_product || false === $apply_cart_condition_on_categories || false === $apply_cart_condition_on_customer_grp || false === $apply_cart_condition_on_individual_customer ) {
			return false;
		}

		else {
			return $valid;
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method delete_post_meta
	 * @param int $coupon_id
	 * @return void
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

		// loop through all the meta_keys and get their value
		foreach ( $meta_keys as $meta_key => $related_meta_keys ) {
			$meta_value = get_post_meta( $coupon_id, $meta_key, true );

			// if any of the meta_key's value is empty then delete another meta_key's values which are co-related
			if ( empty( $meta_value ) ) {
				foreach ( $related_meta_keys as $related_meta_key ) {
					delete_post_meta( $coupon_id, $related_meta_key );
				}
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @since 1.0.0
	 * @method invalid_error_message_for_not_matching_all_products
	 * @param string $err
	 * @param int $err_code
	 * @param int $coupon
	 * @return string
	 * Display custom error message for invalid coupon.
	 */
	public function invalid_error_message_for_not_matching_all_products( $err, $err_code, $coupon )
	{
		$all_meta_values = $this->get_all_post_meta( $coupon );

		// get value of 'all_selected_products' meta field
		$all_selected_products = $all_meta_values['all_selected_products'];

		$all_product_single_string = '';

		if ( ! empty( $all_selected_products ) ) {
			foreach ( $all_selected_products as $product ) {
				$all_product_single_string .= get_the_title( $product ) . ', ';
			}
		}

		$all_product_single_string = rtrim( $all_product_single_string, ', ' );

		if ( $err_code === 100 ) {
			// Change the error message for the INVALID_FILTERED error here
			$err = esc_html__( 'Invalid coupon. to apply this coupon please add all of these products to your cart "' . $all_product_single_string . '".', 'hexcoupon');
		}

		return $err;
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @since 1.0.0
	 * @method invalid_error_message_for_not_matching_any_of_the_products
	 * @param string $err
	 * @param int $err_code
	 * @param int $coupon
	 * @return string
	 * Display custom error message for invalid coupon.
	 */
	public function invalid_error_message_for_not_matching_any_of_the_products( $err, $err_code, $coupon )
	{
		$all_meta_values = $this->get_all_post_meta( $coupon );

		// get value of 'all_selected_products' meta field
		$all_selected_products = $all_meta_values['all_selected_products'];

		$all_product_single_string = '';

		if ( ! empty( $all_selected_products ) ) {
			foreach ( $all_selected_products as $product ) {
				$all_product_single_string .= get_the_title( $product ) . ', ';
			}
		}

		$all_product_single_string = rtrim( $all_product_single_string, ', ' );

		if ( $err_code === 100 ) {
			// Change the error message for the INVALID_FILTERED error here
			$err = esc_html__( 'Invalid coupon. To apply this coupon please add any of these products to your cart "' . $all_product_single_string . '".', 'hexcoupon');
		}

		return $err;
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @since 1.0.0
	 * @method invalid_error_message_for_not_matching_with_product_categories
	 * @param string $err
	 * @param int $err_code
	 * @param $coupon
	 * @return string
	 * Display custom error message for invalid coupon.
	 */
	public function invalid_error_message_for_not_matching_with_product_categories( $err, $err_code, $coupon )
	{
		$all_meta_values = $this->get_all_post_meta( $coupon );

		// get the value of 'all_selected_categories'
		$all_selected_categories = $all_meta_values['all_selected_categories'];

		$category_string = ''; // initialize an empty string

		if ( ! empty( $all_selected_categories ) ) {
			foreach ( $all_selected_categories as $category ) {
				$category_string .= get_the_category_by_ID( $category ) . ', ';
			}
		}

		$category_string = rtrim( $category_string, ', ' );

		if ( $err_code === 100 ) {
			// Change the error message for the INVALID_FILTERED error here
			$err = esc_html__( 'Invalid coupon. To apply this coupon please add products from any of these categories to your cart "' . $category_string . '".', 'hexcoupon');
		}

		return $err;
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @since 1.0.0
	 * @method invalid_error_message_for_disallowed_grp_of_user
	 * @param string $err
	 * @param int $err_code
	 * @param int $coupon
	 * @return string
	 * Display custom error message for invalid coupon.
	 */
	public function invalid_error_message_for_disallowed_grp_of_user( $err, $err_code, $coupon )
	{
		$all_meta_values = $this->get_all_post_meta( $coupon );

		// get the value of 'all_selected_categories'
		$selected_customer_group = $all_meta_values['selected_customer_group'];

		$customer_string = ''; // initialize an empty string

		if ( ! empty( $selected_customer_group ) ) {
			foreach ( $selected_customer_group as $customer ) {
				$customer_string .= $customer . ', ';
			}
		}

		$customer_string = rtrim( $customer_string, ', ' );

		if ( $err_code === 100 ) {
			// Change the error message for the INVALID_FILTERED error here
			$err = esc_html__( 'Invalid coupon, this coupon is not valid for these group of users "' . $customer_string . '".', 'hexcoupon');
		}

		return $err;
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @since 1.0.0
	 * @method invalid_error_message_for_individual_user
	 * @param string $err
	 * @param int $err_code
	 * @return string
	 * Display custom error message for invalid coupon.
	 */
	public function invalid_error_message_for_individual_user( $err, $err_code )
	{
		if ( $err_code === 100 ) {
			// Change the error message for the INVALID_FILTERED error here
			$err = esc_html__( 'Invalid coupon, sorry you are not allowed to use this coupon.', 'hexcoupon');
		}

		return $err;
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @since 1.0.0
	 * @method invalid_error_message_for_allowed_grp_of_user
	 * @param string $err
	 * @param int $err_code
	 * @param $coupon
	 * @return string
	 * Display custom error message for invalid coupon.
	 */
	public function invalid_error_message_for_allowed_grp_of_user( $err, $err_code, $coupon )
	{
		$all_meta_values = $this->get_all_post_meta( $coupon );

		// get the value of 'all_selected_categories'
		$selected_customer_group = $all_meta_values['selected_customer_group'];

		$customer_string = ''; // initialize an empty string

		if ( ! empty( $selected_customer_group ) ) {
			foreach ( $selected_customer_group as $customer ) {
				$customer_string .= $customer . ', ';
			}
		}

		$customer_string = rtrim( $customer_string, ', ' );

		if ( $err_code === 100 ) {
			// Change the error message for the INVALID_FILTERED error here
			$err = esc_html__( 'Invalid coupon, this coupon is only valid for these group of users "' . $customer_string . '".', 'hexcoupon');
		}

		return $err;
	}
}
