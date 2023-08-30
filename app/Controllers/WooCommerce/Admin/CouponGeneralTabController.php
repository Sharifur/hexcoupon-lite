<?php

namespace hexcoupon\app\Controllers\WooCommerce\Admin;

use HexCoupon\App\Controllers\BaseController;
use hexcoupon\app\Core\Helpers\ValidationHelper;
use HexCoupon\App\Core\Lib\SingleTon;
use Kathamo\Framework\Lib\Http\Request;
use function Symfony\Component\VarDumper\Dumper\esc;

class CouponGeneralTabController extends BaseController
{
	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return void
	 * @since 1.0.0
	 * Register hook that is needed to validate the coupon according to the starting date.
	 */
	public function register()
	{
		add_action('wp_loaded', [ $this, 'get_all_post_meta' ] );
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this, 'save_coupon_general_tab_meta_field_data' ] );
		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'apply_coupon' ], 10, 2 );
		add_action( 'woocommerce_process_shop_coupon_meta', [ $this, 'delete_meta_value' ] );
		add_action( 'woocommerce_before_calculate_totals', [ $this, 'add_free_items_to_cart' ] );
		add_filter( 'woocommerce_cart_item_price', [ $this, 'replace_price_amount_with_free_text' ], 10, 2 );
		add_action( 'woocommerce_before_calculate_totals', [ $this, 'apply_price_deduction' ] );
		add_action( 'woocommerce_cart_totals_before_order_total', [ $this, 'show_free_items_name_before_total_price' ] );
		add_action('wp_loaded', [ $this, 'display_exceeded_quantity_notice_for_free_item' ] );
		add_filter( 'woocommerce_update_cart_action_cart_updated', [ $this, 'clear_notices_on_cart_update' ], 10, 1 );
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
			'customer_purchases',
			'add_specific_product_to_purchase',
			'add_specific_product_for_free',
			'customer_gets_as_free',
			'add_categories_to_purchase',
			'coupon_starting_date',
			'apply_days_hours_of_week',
			'discount_type',
			'message_for_coupon_starting_date',
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
	 * @method coupon_id
	 * @return int
	 * Get the id of the applied coupon from the cart page.
	 */
	private function coupon_id()
	{
		$applied_coupon = WC()->cart->get_applied_coupons(); // get applied coupon from the cart page

		$coupon_id = ''; // assigning an empty string

		// check if there are applied coupon
		if ( ! empty( $applied_coupon ) ) {
			// Assuming only one coupon is applied; if multiple, you might need to loop through $applied_coupon array
			$coupon_code = reset( $applied_coupon );
			$coupon_id = wc_get_coupon_id_by_code( $coupon_code ); // get the coupon id from the coupon code
		}

		return $coupon_id; // finally return the coupon code id
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method get_product_categories_id_in_cart
	 * @return array
	 * Get product categories id from the cart page.
	 */
	private function get_product_categories_id_in_cart()
	{
		// Initialize an empty array to store product categories
		$product_categories = [];

		// Get the current cart contents
		$cart = WC()->cart->get_cart();

		// Loop through cart items and extract categories
		foreach ( $cart as $cart_item_key => $cart_item ) {
			$product_id = $cart_item['product_id'];
			$product = wc_get_product( $product_id );

			// Get product categories
			$categories = wp_get_post_terms( $product_id, 'product_cat', [ 'fields' => 'ids' ] );

			// Add categories to the array
			$product_categories = array_merge( $product_categories, $categories );
		}

		// Remove duplicate categories
		$product_categories = array_unique( $product_categories );

		return $product_categories; // return all the ids of product categories
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method display_exceeded_quantity_notice_for_free_item
	 * @return void
	 * Display a notice for adding more than one item on free products.
	 */
	public function display_exceeded_quantity_notice_for_free_item()
	{
		if ( isset( $_GET['?exceeded_quantity'] ) && $_GET['?exceeded_quantity'] === 'true' ) {
			wc_add_notice( __( 'Cannot proceed to checkout because you can not add more than "one" item from the free product. Please reduce the product quantity or reduce product to one item.', 'hexcoupon' ),  'error');
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method clear_notices_on_cart_update
	 * @return void
	 * Clear the error notice after the cart page is updated through clicking the update button.
	 */
	public function clear_notices_on_cart_update()
	{
		wc_clear_notices();
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method custom_content_below_coupon_button
	 * @return void
	 * Display the free items below the apply coupon button.
	 */
	public function custom_content_below_coupon_button()
	{
		global $product;

		// Check if we are on the cart page
		if ( is_cart() ) {
			echo '<div class="hexcoupon_select_free_item">';
			// Add content for the free items
			echo '<h3>' . esc_html__( 'Please select any product from below', 'hexcoupon' ) . '</h3>';

			// Get the ids of free items.
			$product_ids = $this->add_free_items_to_cart();

			foreach ( $product_ids as $product_id ) {
				// Output each product
				$product = wc_get_product( $product_id );
				if ( $product ) {
					echo '<div class="custom-product">';
					echo '<a href="' . get_permalink( $product_id ) . '">' . $product->get_image() . '</a>';
					echo '<h3 class="has-text-align-center wp-block-post-title has-medium-font-size"><a href="' . get_permalink ( $product_id ) . '">' . $product->get_name() . '</a></h3>';
					echo '<p class="price has-font-size has-small-font-size has-text-align-center">' . $product->get_price_html() . '</p>';
					echo '<form class="cart" action="' . esc_url( wc_get_cart_url() ) . '" method="post">';
					echo '<div class="has-text-align-center"><button type="submit" name="add-to-cart" value="' . esc_attr( $product_id ) . '" class="button wp-element-button wp-block-button__link">' . esc_html__( 'Add to Cart', 'hexcoupon' ) . '</button></div>';
					echo '</form>';
					echo '</div>';
				}
			}

			echo '</div>';
		}
	}

	/**
	 * @throws \Exception
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_free_items_to_cart
	 * @return array
	 * Add free items to the cart page.
	 */
	public function add_free_items_to_cart()
	{
		$coupon_id = $this->coupon_id(); // get the id of applied coupon from the cart page

		$all_meta_values = $this->get_all_post_meta( $coupon_id );

		$customer_purchases = $all_meta_values['customer_purchases'];

		$selected_products_to_purchase = $all_meta_values['add_specific_product_to_purchase']; // get purchasable selected product
		$selected_products_as_free = $all_meta_values['add_specific_product_for_free']; // get free selected product

		$customer_gets_as_free = $all_meta_values['customer_gets_as_free']; // get meta value of customer gets as free

		$add_categories_to_purchase = $all_meta_values['add_categories_to_purchase']; // get the meta-value of coupon purchasable product categories

		// Product IDs
		$main_product_id = ! empty( $selected_products_to_purchase ) ? $selected_products_to_purchase : []; // product ids that has to be existed in the cart to apply BOGO deals
		$free_item_id = ! empty( $selected_products_as_free ) ? $selected_products_as_free : []; // ids of products that customer will get as free

		$cart_product_ids = [];

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$cart_product_ids[] = $cart_item['product_id']; // assign all ids of products in the cart in an array
		}

		$main_product_in_cart = false; // '$main_product_in_cart' is false if there are no products in the cart that needs to be there to apply BOGO deals.

		// Check if the cart has the exact or any product that the admin has selected to purchase
		if ( 'a_specific_product' === $customer_purchases || 'any_products_listed_below' === $customer_purchases ) {
			foreach ( $main_product_id as $main_product_single ) {
				if ( in_array( $main_product_single, $cart_product_ids ) ) {
					$main_product_in_cart = true; // if the cart has the product it assigns value of '$main_product_in_cart' to 'true'
					break;
				}
			}
		}

		// Check if the cart has all the exact products that the admin has selected to purchase
		if ( 'a_combination_of_products' === $customer_purchases ) {
			foreach ( $main_product_id as $main_product_single ) {
				if ( ! in_array( $main_product_single, $cart_product_ids ) ) {
					$main_product_in_cart = false; // if the cart does not have the product it assigns value of '$main_product_in_cart' to 'false'
					break;
				}
				else {
					$main_product_in_cart = true; // else it becomes 'true'
				}
			}
		}

		if ( 'product_categories'=== $customer_purchases ) {
			$product_categories_id_in_cart = $this->get_product_categories_id_in_cart(); // assign product categories id of the cart page

			foreach ( $add_categories_to_purchase as $add_category_to_purchase ) {
				if ( in_array( $add_category_to_purchase, $product_categories_id_in_cart ) ) {
					$main_product_in_cart = true; // if the cart does have the products from the selected categories then '$main_product_in_cart' becomes 'true'
					break;
				}
			}
		}

		// Add free item to cart if the main product is in the cart
		if ( $main_product_in_cart ) {
			if ( 'a_specific_product' === $customer_gets_as_free || 'a_combination_of_products' === $customer_gets_as_free ) {
				if ( ! empty( $free_item_id ) ) {
					foreach ( $free_item_id as $free_gift_single_id ) {
						if ( ! in_array( $free_gift_single_id, $cart_product_ids ) ) {
							WC()->cart->add_to_cart( $free_gift_single_id);
							break;
						}
					}
				}
			}

			// Give same items as free which was added by the admin as BOGO deal
			if ( 'same_product_added_to_cart' === $customer_gets_as_free ) {
				if ((is_admin() && !defined('DOING_AJAX')))
					return;

				if (did_action('woocommerce_before_calculate_totals') >= 2)
					return;

				foreach ( $main_product_id as $main_product_single ) {
					$main_product_single = intval( $main_product_single );

					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						if ( $cart_item['product_id'] === $main_product_single ) {
							// Get the current quantity
							$current_quantity = $cart_item['quantity'];

							// Increase the quantity by one
							$new_quantity = $current_quantity + 1;

							$generate_free_item_id =  WC()->cart->generate_cart_id( $main_product_single ); // generate id of free item
							$free_single_item_key = WC()->cart->find_product_in_cart( $generate_free_item_id ); // find the item key

							// Update the cart with the new quantity
							WC()->cart->set_quantity( $free_single_item_key, $new_quantity );

							break; // Exit the loop since we have found our product
						}
					}
				}
			}

			if ( 'any_products_listed_below' === $customer_gets_as_free ) {
				add_action( 'woocommerce_after_cart_table', [ $this, 'custom_content_below_coupon_button' ] );


				$matches = array_intersect( $free_item_id, $cart_product_ids );
				$matches = count($matches);

				if ( $matches > 1 ) {
					if ( is_checkout() ) {
						$cart_url = sanitize_url( wc_get_cart_url() . '&?exceeded_quantity=true' );
						wp_safe_redirect( $cart_url );
						exit;
					}
				}
			}

			$quantities = WC()->cart->get_cart_item_quantities();

			if ( ! empty( $free_item_id ) ) {
				foreach ( $free_item_id as $free_gift_single_id ) {
					if( isset( $quantities[$free_gift_single_id] ) && $quantities[$free_gift_single_id] > 1 ) {
						if ( is_checkout() ) {
							$cart_url = sanitize_url( wc_get_cart_url() . '&?exceeded_quantity=true' );
							wp_safe_redirect( $cart_url );
							exit;
						}
						break;
					}
				}
			}

		}
		else {
			// Remove free item from the cart if the main product does not exist in the cart
			foreach ( $free_item_id as $free_single_item_id ) {
				$generate_free_item_id =  WC()->cart->generate_cart_id( $free_single_item_id ); // generate id of free item
				$free_single_item_key = WC()->cart->find_product_in_cart( $generate_free_item_id ); // find the item key
				WC()->cart->remove_cart_item( $free_single_item_key ); // finally remove the item
			}
		}

		return $free_item_id;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method replace_price_amount_with_free_text
	 * @param string $price
	 * @param array $cart_item
	 * @return string
	 * Replace price amount with 'free (BOGO Deal)' text in the price column of product in the cart page.
	 */
	public function replace_price_amount_with_free_text( $price, $cart_item )
	{
		$coupon_id = $this->coupon_id(); // Get the id of applied coupon

		$customer_purchases = get_post_meta(  $coupon_id, 'customer_purchases', true );

		$free_items_id = ! empty( $this->add_free_items_to_cart() ) ? $this->add_free_items_to_cart() : [];

		if ( 'a_specific_product' === $customer_purchases || 'a_combination_of_products' === $customer_purchases || 'any_products_listed_below' === $customer_purchases || 'product_categories' === $customer_purchases ) {
			if ( in_array( $cart_item['product_id'], $free_items_id ) ) {
				$price = '<span class="free_bogo_deal_text">' . esc_html__( 'Free (BOGO Deal)', 'hexcoupon' ) . '</span>';
			}
		}

		return $price;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method apply_price_deduction
	 * @param object $cart
	 * @return void
	 * Deduct the free item price from the cart page.
	 */
	public function apply_price_deduction( $cart )
	{
		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
			return;

		$free_items_id = ! empty( $this->add_free_items_to_cart() ) ? $this->add_free_items_to_cart() : [];

		// Deduct the price of the free items from the cart total
		foreach ( $cart->get_cart() as $cart_item ) {
			if ( in_array( $cart_item['product_id'], $free_items_id ) ) {
				$quantity = $cart_item['quantity'];
				if ( $quantity >= 1 ) {
					$discounted_price = $cart_item['data']->get_price() / $quantity * ( $quantity - 1 );
					$cart_item['data']->set_price( $discounted_price );
				}
				else {
					$cart_item['data']->set_price( 0 );
				}
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method show_free_items_name_before_total_price
	 * @return void
	 * Show BOGO deals free items name in the cart page.
	 */
	public function show_free_items_name_before_total_price()
	{
		$free_items_id = ! empty( $this->add_free_items_to_cart() ) ? $this->add_free_items_to_cart() : []; // get all free items id's

		// Display free item names
		$free_items = '';
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( in_array( $cart_item['product_id'], $free_items_id ) ) {
				$free_items .= esc_html( $cart_item['data']->get_name() ) . ', ';
			}
		}

		if ( ! empty( $free_items ) ) {
			echo '<tr class="free-items-row">';
			echo '<th>' . esc_html__( 'Free Items', 'hexcoupon' ) . '</th><td class="free-items-name">' . esc_html( $free_items ) . '</td>';
			echo '</tr>';
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method save_meta_data
	 * @param string $key
	 * @param string $data_type
	 * @param int $coupon_id
	 * @return mixed
	 * Save the coupon general tab meta-data.
	 */
	private function save_meta_data( $key, $data_type, $coupon_id )
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
	 * @method save_dynamic_meta_data
	 * @param string $day
	 * @param string $key
	 * @param string $data_type
	 * @param int $coupon_id
	 * @return void
	 * Save the coupon dynamic hours meta-data.
	 */
	private function save_dynamic_meta_data( $day, $key, $data_type, $coupon_id )
	{
		if ( isset( $_POST['total_hours_count_'.$day] ) ) {
			$total_hours_count = intval( $_POST['total_hours_count_'.$day] );

			// Loop through the input values and save them as post meta
			for ( $i = 1; $i <= $total_hours_count; $i++ ) {
				$validator = $this->validate( [
					$key.$i => $data_type
				] );
				$error = $validator->error();
				if ( $error ) {

				}
				$data = $validator->getData();

				update_post_meta( $coupon_id, $key.$i, $data[$key.$i] );
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method save_coupon_on_different_days
	 * @param int $coupon_id
	 * @return void
	 * Save the coupon days and hours option applicability on different days.
	 */
	private function save_coupon_on_different_days( $coupon_id )
	{
		$meta_fields_data = [
			[ 'coupon_apply_on_saturday', 'string' ],
			[ 'coupon_apply_on_sunday', 'string' ],
			[ 'coupon_apply_on_monday', 'string' ],
			[ 'coupon_apply_on_tuesday', 'string' ],
			[ 'coupon_apply_on_wednesday', 'string' ],
			[ 'coupon_apply_on_thursday', 'string' ],
			[ 'coupon_apply_on_friday', 'string' ],
			[ 'discount_type', 'string' ],
			[ 'customer_purchases', 'string' ],
			[ 'add_a_specific_product', 'string' ],
			[ 'add_specific_category', 'string' ],
			[ 'customer_gets_as_free', 'string' ],
			[ 'add_specific_product_for_free', 'string' ],
		];

		foreach ( $meta_fields_data as $meta_field_data ) {
			if ( ! empty( $meta_field_data[0] ) && ! empty( $meta_field_data[1] ) ) {
				$this->save_meta_data( $meta_field_data[0], $meta_field_data[1], $coupon_id );
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method save_coupon_start_expiry_time
	 * @param int $coupon_id
	 * @return void
	 * Save coupon start and expiry time on different days.
	 */
	private function save_coupon_start_expiry_time( $coupon_id )
	{
		$meta_fields_data = [
			[ 'sat_coupon_start_time', 'string' ],
			[ 'sat_coupon_expiry_time', 'string' ],
			[ 'sun_coupon_start_time', 'string' ],
			[ 'sun_coupon_expiry_time', 'string' ],
			[ 'mon_coupon_start_time', 'string' ],
			[ 'mon_coupon_expiry_time', 'string' ],
			[ 'tue_coupon_start_time', 'string' ],
			[ 'tue_coupon_expiry_time', 'string' ],
			[ 'wed_coupon_start_time', 'string' ],
			[ 'wed_coupon_expiry_time', 'string' ],
			[ 'thu_coupon_start_time', 'string' ],
			[ 'thu_coupon_expiry_time', 'string' ],
			[ 'fri_coupon_start_time', 'string' ],
			[ 'fri_coupon_expiry_time', 'string' ],
		];

		foreach ( $meta_fields_data as $meta_field_data ) {
			if ( ! empty( $meta_field_data[0] ) && ! empty( $meta_field_data[1] ) ) {
				$this->save_meta_data( $meta_field_data[0], $meta_field_data[1], $coupon_id );
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method save_coupon_sat_dynamic_start_time
	 * @param $coupon_id
	 * @return void
	 * Apply the coupon dynamic start and expiry hours field value for all days.
	 */
	private function save_coupon_dynamic_start_expiry_time( $coupon_id )
	{
		$meta_fields_data = [
			[ 'saturday', 'sat_coupon_start_time_', 'string' ],
			[ 'saturday', 'sat_coupon_expiry_time_', 'string' ],
			[ 'sunday', 'sun_coupon_start_time_', 'string' ],
			[ 'sunday', 'sun_coupon_expiry_time_', 'string' ],
			[ 'monday', 'mon_coupon_start_time_', 'string' ],
			[ 'monday', 'mon_coupon_expiry_time_', 'string' ],
			[ 'tuesday', 'tue_coupon_start_time_', 'string' ],
			[ 'tuesday', 'tue_coupon_expiry_time_', 'string' ],
			[ 'wednesday', 'wed_coupon_start_time_', 'string' ],
			[ 'wednesday', 'wed_coupon_start_time_', 'string' ],
			[ 'thursday', 'thu_coupon_start_time_', 'string' ],
			[ 'thursday', 'thu_coupon_expiry_time_', 'string' ],
			[ 'friday', 'fri_coupon_start_time_', 'string' ],
			[ 'friday', 'fri_coupon_expiry_time_', 'string' ],
		];

		foreach ( $meta_fields_data as $meta_field_data ) {
			if ( ! empty( $meta_field_data[0] ) && ! empty( $meta_field_data[1] ) && ! empty( $meta_field_data[2] ) ) {
				$this->save_dynamic_meta_data( $meta_field_data[0], $meta_field_data[1], $meta_field_data[2], $coupon_id );
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method save_coupon_expiry_date_message
	 * @param $coupon_id
	 * @return void
	 * @since 1.0.0
	 * Save the coupon expiry date message.
	 */
	public function save_coupon_general_tab_meta_field_data( $coupon_id )
	{
		$meta_fields_data = [
			[ 'message_for_coupon_expiry_date', 'string' ],
			[ 'coupon_starting_date', 'string' ],
			[ 'message_for_coupon_starting_date', 'string' ],
			[ 'apply_days_hours_of_week', 'string' ],
		];

		foreach ( $meta_fields_data as $meta_field_data ) {
			if ( ! empty( $meta_field_data[0] ) && ! empty( $meta_field_data[1] ) ) {
				$this->save_meta_data( $meta_field_data[0], $meta_field_data[1], $coupon_id );
			}
		}

		// Save the coupon days and hours option applicability on different days
		$this->save_coupon_on_different_days( $coupon_id );

		// Save coupon start and expiry time on different days
		$this->save_coupon_start_expiry_time( $coupon_id );

		// Save coupon dynamic start and expiry time on different days
		$this->save_coupon_dynamic_start_expiry_time( $coupon_id );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method apply_coupon_starting_date
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * Apply/validate the coupon starting date.
	 */
	private function apply_coupon_starting_date( $valid, $coupon )
	{
		$current_time = time();

		$coupon_starting_date = get_post_meta( $coupon->get_id(), 'coupon_starting_date', true );
		$coupon_converted_starting_date = strtotime( $coupon_starting_date );

		if ( empty( $coupon_starting_date ) || $current_time >= $coupon_converted_starting_date ) {
			return $valid;
		}
		else {
			// display a custom coupon error message if the coupon is invalid
			add_filter( 'woocommerce_coupon_error', [ $this, 'coupon_starting_date_invalid_error_message' ] , 10, 3 );
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method apply_to_single_day
	 * @param bool $valid
	 * @param object $coupon
	 * @param string $full_day
	 * @param string $abbrev
	 * @return bool
	 * Apply/validate the coupon on different days of the week.
	 */
	private function apply_to_single_day( $valid, $coupon, $full_day, $abbrev )
	{
		global $day;

		// get current date
		$current_day = date('l');

		// get current server time
		$current_server_time = current_time( 'timestamp' );

		// get selected name of selected day
		$day = get_post_meta( $coupon->get_id(), 'coupon_apply_on_'.$full_day, true );
		$day = ! empty( $day ) ? '1' : '';
		// convert the day name
		if ( '1' === $day ) $day = ucfirst( $full_day );

		$coupon_start_time = get_post_meta( $coupon->get_id(), $abbrev.'_coupon_start_time', true );
		$coupon_start_time = strtotime( $coupon_start_time );

		$coupon_expiry_time = get_post_meta( $coupon->get_id(), $abbrev.'_coupon_expiry_time', true );
		$coupon_expiry_time = strtotime( $coupon_expiry_time );

		if ( ! empty( $day ) && $current_day == $day ) {
			if ( ( '' == $coupon_start_time && '' == $coupon_expiry_time ) || ( $current_server_time >= $coupon_start_time && $current_server_time <= $coupon_expiry_time ) ) {
				return $valid;
			}

			$total_hours_count = get_post_meta( $coupon->get_id(), 'total_hours_count', true );

			for ( $i = 1; $i <= $total_hours_count; $i++ ) {
				$additional_start_time = get_post_meta( $coupon->get_id(), $abbrev.'_coupon_start_time_'.$i, true );
				$additional_expiry_time = get_post_meta( $coupon->get_id(), $abbrev.'_coupon_expiry_time_'.$i, true );

				$additional_start_time =  strtotime( $additional_start_time );
				$additional_expiry_time =  strtotime( $additional_expiry_time );

				if (  $current_server_time >= $additional_start_time && $current_server_time <= $additional_expiry_time ) {
					return $valid;
				}
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method apply_coupon_on_different_days
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * Apply/validate the coupon on different days of the week.
	 */
	private function apply_coupon_on_different_days( $valid, $coupon )
	{
		$days = [
			'saturday' => 'sat',
			'sunday' => 'sun',
			'monday' => 'mon',
			'tuesday' => 'tue',
			'wednesday' => 'wed',
			'thursday' => 'thu',
			'friday' => 'fri'
		];

		foreach ( $days as $day => $abbrev ) {
			if ( $this->apply_to_single_day( $valid, $coupon, $day, $abbrev ) ) {
				return $valid;
			}
		}

		// If none of the days are valid, add the filter for an invalid coupon message.
		add_filter('woocommerce_coupon_error', [$this, 'coupon_invalid_error_message_for_single_day'], 10, 3);

		return false;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method apply_coupon
	 * @param bool $valid
	 * @param object $coupon
	 * @return bool
	 * Apply/validate the coupon starting date.
	 */
	public function apply_coupon( $valid, $coupon )
	{
		// get 'apply_days_hours_of_week' meta value
		$days_hours_of_week = get_post_meta( $coupon->get_id(), 'apply_days_hours_of_week', true );

		// get 'apply_coupon_starting_date' return value
		$apply_coupon_starting_date = $this->apply_coupon_starting_date( $valid, $coupon );
		// get 'apply_coupon_on_different_days' return value
		$apply_coupon_on_different_days = $this->apply_coupon_on_different_days( $valid, $coupon );

		$coupon_apply_on_every_day = [
			'coupon_apply_on_saturday',
			'coupon_apply_on_sunday',
			'coupon_apply_on_monday',
			'coupon_apply_on_tuesday',
			'coupon_apply_on_wednesday',
			'coupon_apply_on_thursday',
			'coupon_apply_on_friday',
		];

		// Finally apply coupon and check the validity
		if ( $apply_coupon_starting_date ) {
			if ( 'yes' === $days_hours_of_week ) {
				if ( $apply_coupon_on_different_days ) {
					return $valid;
				}

				foreach ( $coupon_apply_on_every_day as $single_day ) {
					$single_day = get_post_meta( $coupon->get_id(), $single_day, true );
					if ( '1' == $single_day ) {
						return false;
					}
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method delete_meta_value
	 * @param int $coupon_id
	 * @return void
	 * Delete meta value.
	 */
	public function delete_meta_value( $coupon_id )
	{
		$days_hours_of_week = get_post_meta( $coupon_id, 'apply_days_hours_of_week', true );

		$coupon_apply_on_day = [
			'coupon_apply_on_saturday',
			'coupon_apply_on_sunday',
			'coupon_apply_on_monday',
			'coupon_apply_on_tuesday',
			'coupon_apply_on_wednesday',
			'coupon_apply_on_thursday',
			'coupon_apply_on_friday',
		];

		$meta_start_key =  [
			'sat_coupon_start_time',
			'sun_coupon_start_time',
			'mon_coupon_start_time',
			'tue_coupon_start_time',
			'wed_coupon_start_time',
			'thu_coupon_start_time',
			'fri_coupon_start_time'
		];

		$meta_expiry_key = [
			'sat_coupon_expiry_time',
			'sun_coupon_expiry_time',
			'mon_coupon_expiry_time',
			'tue_coupon_expiry_time',
			'wed_coupon_expiry_time',
			'thu_coupon_expiry_time',
			'fri_coupon_expiry_time'
		];

		$days = [
			'saturday' => 'sat_coupon',
			'sunday' => 'sun_coupon',
			'monday' => 'mon_coupon',
			'tuesday' => 'tue_coupon',
			'wednesday' => 'wed_coupon',
			'thursday' => 'thu_coupon',
			'friday' => 'fri_coupon'
		];

		if ( empty( $days_hours_of_week ) ) {
			foreach( $meta_start_key as $value ) {
				delete_post_meta( $coupon_id,$value );
			}

			foreach ( $meta_expiry_key as $value ) {
				delete_post_meta( $coupon_id,$value );
			}

			foreach ( $coupon_apply_on_day as $value ) {
				delete_post_meta( $coupon_id, $value );
			}
		}

		foreach ( $days as $day => $coupon_prefix ) {
			$coupon_apply_on_day = get_post_meta( $coupon_id, 'coupon_apply_on_' . $day, true );

			if ( empty( $coupon_apply_on_day ) ) {
				delete_post_meta( $coupon_id,  $coupon_prefix . '_start_time' );
				delete_post_meta( $coupon_id,  $coupon_prefix . '_expiry_time' );
			}
		}

		$customer_gets_as_free = get_post_meta( $coupon_id, 'customer_gets_as_free', true );
		$customer_purchases = get_post_meta( $coupon_id, 'customer_purchases', true );

		if ( 'product_categories' === $customer_purchases ) {
			delete_post_meta( $coupon_id, 'add_specific_product_to_purchase' );
		}

		if ( 'a_specific_product' === $customer_purchases || 'a_combination_of_products' === $customer_purchases || 'any_products_listed_below' === $customer_purchases ) {
			delete_post_meta( $coupon_id, 'add_categories_to_purchase' );
		}

		if ( 'same_product_added_to_cart' === $customer_gets_as_free ) {
			delete_post_meta( $coupon_id,'add_specific_product_for_free' );
		}

		$discount_type = get_post_meta( $coupon_id, 'discount_type', true );

		if ( 'percent' === $discount_type || 'fixed_cart' === $discount_type || 'fixed_product' === $discount_type ) {
			$bogo_meta_values = [
				'customer_purchases',
				'add_specific_product_to_purchase',
				'add_categories_to_purchase',
				'customer_gets_as_free',
				'add_specific_product_for_free',
			];

			foreach ( $bogo_meta_values as $single_value ) {
				delete_post_meta( $coupon_id, $single_value );
			}
		}
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @since 1.0.0
	 * @method coupon_starting_date_invalid_error_message
	 * @param string $err
	 * @param int $err_code
	 * @param object $coupon
	 * @return string
	 * Display custom error message for invalid coupon.
	 */
	public function coupon_starting_date_invalid_error_message( $err, $err_code, $coupon )
	{
		$coupon = new \WC_Coupon( $coupon );

		// Get the ID of the coupon
		$coupon_id = $coupon->get_id();

		$message_for_coupon_starting_date = get_post_meta( $coupon_id, 'message_for_coupon_starting_date', true );

		if ( 100 === $err_code ) {
			// Change the error message for the INVALID_FILTERED error here
			$err = $message_for_coupon_starting_date;
		}

		return $err;
	}

	/**
	 * @package hexcoupon
	 * @author Wphex
	 * @since 1.0.0
	 * @method coupon_invalid_error_message_for_single_day
	 * @param string $err
	 * @param int $err_code
	 * @param object $coupon
	 * @return string
	 * Display custom error message for invalid coupon.
	 */
	public function coupon_invalid_error_message_for_single_day( $err, $err_code, $coupon )
	{
		$coupon = new \WC_Coupon( $coupon );

		// Get the ID of the coupon
		$coupon_id = $coupon->get_id();

		$days = [
			'saturday' => 'coupon_apply_on_saturday',
			'sunday' => 'coupon_apply_on_sunday',
			'monday' => 'coupon_apply_on_monday',
			'tuesday' => 'coupon_apply_on_tuesday',
			'wednesday' => 'coupon_apply_on_wednesday',
			'thursday' => 'coupon_apply_on_thursday',
			'friday' => 'coupon_apply_on_friday',
		];

		$start_time = [
			'sat_coupon_start_time',
			'sun_coupon_start_time',
			'mon_coupon_start_time',
			'tue_coupon_start_time',
			'wed_coupon_start_time',
			'thu_coupon_start_time',
			'fri_coupon_start_time',
		];

		$result = '';

		// get the value of apply coupon on different days
		foreach ( $days as $day => $meta_key ) {
			$value = get_post_meta( $coupon_id, $meta_key, true );
			if ( ! empty( $value ) && '1' === $value ) {
				$result .= ' -' . ucfirst($day);
			}
		}

		// get the values of coupon start time
		foreach ( $start_time as $meta_key ) {
			$value = get_post_meta( $coupon_id, $meta_key, true );

			if ( ! empty( $value ) ) {
				// initialize the message
				$message = 'Coupon is not valid for this hour. Please comeback at another time.';
			}
			else {
				// initialize the message
				$message = 'Coupon is not valid for today. Please comeback on ' . $result . '';
			}
		}

		if ( 100 === $err_code ) {
			// Change the error message for the INVALID_FILTERED error here
			$err = $message;
		}

		return $err;
	}
}
