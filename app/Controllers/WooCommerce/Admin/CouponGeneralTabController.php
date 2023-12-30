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

	private $error_message = 'An error occured while saving the coupon general tab meta data value';

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
		add_action( 'woocommerce_cart_totals_before_order_total', [ $this, 'show_free_items_name_before_total_price' ] );
		add_filter( 'woocommerce_cart_product_subtotal', [ $this, 'alter_product_subtotal_in_cart_for_bogo' ], 10, 4 );
		add_action( 'woocommerce_cart_calculate_fees', [ $this, 'custom_fee_for_bogo_deal' ], 10, 1 );
		add_filter( 'woocommerce_cart_subtotal', [ $this, 'deduct_bogo_discount_amount_from_subtotal' ] , 99, 3 );
	}

	public function specific_products_against_specific_products( $customer_purchases, $customer_gets_as_free, $main_product_min_purchased_quantity, $cart_item_quantity, $free_item_id, $string_to_be_replaced, $coupon_id, $main_product_single_id, $cart_product_ids )
	{
		if ( 'a_specific_product' === $customer_gets_as_free && 'a_specific_product' === $customer_purchases ) {
			if ( $cart_item_quantity < $main_product_min_purchased_quantity ) {
				// Remove free item from cart, if '$main_product_min_purchased_quantity' is less than the '$cart_item_quantity'
				$this->remove_cart_product( $free_item_id );

				// Show error message to the user if main product quantity is not sufficient to get the offer
				add_action( 'woocommerce_before_cart', [ $this, 'cart_custom_error_message' ] );
			}
			// check if free items is not empty and cart item quantity is bigger than the main product min purchases quantity
			if ( ! empty( $free_item_id ) && $cart_item_quantity >= $main_product_min_purchased_quantity ) {
				// loop through all the free products
				foreach ( $free_item_id as $free_gift_single_id ) {
					// Get the title of product
					$free_product_title = get_the_title( $free_gift_single_id );
					// Replace the unnecessary strings from the title
					$free_product_title_lowercase = str_replace( $string_to_be_replaced, '_', strtolower( $free_product_title ) );
					// Get the quantity of free products
					$free_product_quantity = get_post_meta( $coupon_id, $free_product_title_lowercase . '-free_product_quantity', true );
					$free_product_quantity = ! empty( $free_product_quantity ) ? $free_product_quantity : 1;

					// If the main purchased product and the free product is not the same product
					if( $free_gift_single_id != $main_product_single_id ) {
						// If free item is not in the cart then add free items to the cart with 'add_to_cart()'
						if ( ! in_array( $free_gift_single_id, $cart_product_ids ) ) {
							WC()->cart->add_to_cart( $free_gift_single_id, $free_product_quantity );
							add_action( 'woocommerce_before_cart', [ $this, 'cart_custom_success_message' ] );
							break;
						}
						// If the free item is already in the cart then update the quantity
						if ( in_array( $free_gift_single_id, $cart_product_ids ) ) {
							if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) )
								return;

							if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
								return;

							// generate free item cart key
							$generate_free_item_id =  WC()->cart->generate_cart_id( $free_gift_single_id );
							WC()->cart->set_quantity( $generate_free_item_id, $free_product_quantity );
							add_action( 'woocommerce_before_cart', [ $this, 'cart_custom_success_message' ] );
							break;
						}
					}
				}
			}
		}
	}

	public function specific_products_against_same_product( $customer_purchases, $customer_gets_as_free, $main_product_min_purchased_quantity, $cart_item_quantity, $free_item_id, $coupon_id, $wc_cart )
	{
		if ( 'same_product_as_free' === $customer_gets_as_free && 'a_specific_product' === $customer_purchases ) {
			if ( $cart_item_quantity < $main_product_min_purchased_quantity ) {
				// Show error message to the user if main product quantity is not sufficient to get the offer
				add_action( 'woocommerce_before_cart', [ $this, 'cart_custom_error_message' ] );
			}
			else {
				foreach ( $free_item_id as $free_single_item ) {
					$free_converted_title = $this->convert_and_replace_unnecessary_string( $free_single_item );
					$free_quantity = get_post_meta( $coupon_id, $free_converted_title . '-free_product_quantity', true );
					$purchased_min_quantity = get_post_meta( $coupon_id, $free_converted_title . '-purchased_min_quantity', true );
					$free_single_key = $wc_cart->generate_cart_id( $free_single_item );
					if ( ! $wc_cart->find_product_in_cart( $free_single_key ) ) {
						if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) )
							return;

						if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
							return;

						$wc_cart->add_to_cart( $free_single_item, $free_quantity );
					}
					if ( $wc_cart->find_product_in_cart( $free_single_key ) ) {
						if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) )
							return;

						if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
							return;

						$final_quantity = $free_quantity + $purchased_min_quantity;

						$wc_cart->set_quantity( $free_single_key, $final_quantity );
					}
				}
			}
		}
	}

	public function specific_products_against_a_combination_of_products( $customer_purchases, $customer_gets_as_free, $main_product_min_purchased_quantity, $cart_item_quantity, $free_item_id, $coupon_id, $wc_cart, $main_product_id )
	{
		// Add product in the case of customer purchases 'a specific product' and getting 'a combination of product' as free
		if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) )
			return;

		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
			return;

		if ( 'a_combination_of_products' === $customer_gets_as_free && 'a_specific_product' === $customer_purchases ) {
			if ( $cart_item_quantity < $main_product_min_purchased_quantity ) {
				// Remove free item from cart, if '$main_product_min_purchased_quantity' is less than the '$cart_item_quantity'
				$this->remove_cart_product( $free_item_id );

				// Show error message to the user if main product quantity is less than the store owner has selected
				add_action( 'woocommerce_before_cart', [ $this, 'cart_custom_error_message' ] );
			}

			foreach ( $free_item_id as $single_id ) {
				$free_single_title = $this->convert_and_replace_unnecessary_string( $single_id );

				$single_free_quantity = get_post_meta( $coupon_id, $free_single_title . '-free_product_quantity', true );
				$single_free_quantity = ! empty( $single_free_quantity ) ? $single_free_quantity : 1;

				$single_free_key = $wc_cart->generate_cart_id( $single_id );

				// If the cart item quantity is equal to main purchased product minimum quantity
				if ( ! empty( $free_item_id ) && $cart_item_quantity >= $main_product_min_purchased_quantity ) {
					// If free products does not already exist in the cart page
					if ( ! $wc_cart->find_product_in_cart( $single_free_key ) ) {
						$customer_gets = $single_free_quantity;

						// Finally add the free products in the cart
						$wc_cart->add_to_cart( $single_id, $customer_gets );

						add_action( 'woocommerce_before_cart', [ $this, 'cart_custom_success_message' ] );
					}
					// If free products does already exist in the cart page
					if ( $wc_cart->find_product_in_cart( $single_free_key )  && ! in_array( $single_id , $main_product_id ) ) {
						$customer_gets = $single_free_quantity;

						// Finally update the quantity of the free products
						$wc_cart->set_quantity( $single_free_key, $customer_gets );

						add_action( 'woocommerce_before_cart', [ $this, 'cart_custom_success_message' ] );
					}
				}
			}
		}
	}

	public function specific_products_against_any_product_listed_below( $customer_purchases, $customer_gets_as_free, $main_product_min_purchased_quantity, $cart_item_quantity, $free_item_id, $wc_cart, $selected_products_as_free, $coupon_id, $main_product_id )
	{
		if ( 'any_products_listed_below' === $customer_gets_as_free && 'a_specific_product' === $customer_purchases ) {
			if ( $cart_item_quantity < $main_product_min_purchased_quantity ) {
				// Remove free item from cart, if '$main_product_min_purchased_quantity' is less than the '$cart_item_quantity'
				$this->remove_cart_product( $free_item_id );

				// Show error message to the user if main product quantity is less than the store owner has selected
				add_action( 'woocommerce_before_cart', [ $this, 'cart_custom_error_message' ] );
			}

			add_action( 'woocommerce_after_cart_table', [ $this, 'custom_content_below_coupon_button' ] );

			$this->remove_product_in_case_of_any_product_listed_below( $wc_cart, $selected_products_as_free );

			$this->update_quantity_after_updating_cart( $coupon_id, $free_item_id, $main_product_id, $customer_purchases );
		}
	}

	public function combination_of_product_against_specific_product( $customer_purchases, $customer_gets_as_free, $main_product_id, $coupon_id, $free_item_id, $wc_cart )
	{
		if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) )
			return;

		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
			return;

		if ( 'a_specific_product' === $customer_gets_as_free && 'a_combination_of_products' === $customer_purchases ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				// Checking if the cart has all products that the store owner has selected to purchase
				if ( in_array( $cart_item['product_id'], $main_product_id ) ) {
					$product_title = $this->convert_and_replace_unnecessary_string( $cart_item['product_id'] );

					$main_product_min_quantity = get_post_meta( $coupon_id, $product_title . '-purchased_min_quantity', true );

					if ( $cart_item['quantity'] >= $main_product_min_quantity ) {
						$is_main_product_greater_or_equal_to_min = true;
					}
					else {
						$is_main_product_greater_or_equal_to_min = false;
						// Show error message to the user if main product quantity is less than the store owner has selected
						add_action( 'woocommerce_before_cart', [ $this, 'cart_custom_error_message' ] );
						break;
					}
				}
			}

			foreach ( $free_item_id as $free_single ) {
				$free_single_key = $wc_cart->generate_cart_id( $free_single );
				if ( $is_main_product_greater_or_equal_to_min ) {
					// Add product to the cart if the product does not already exist in the cart
					if ( ! $wc_cart->find_product_in_cart( $free_single_key ) ) {
						$free_single_title = $this->convert_and_replace_unnecessary_string( $free_single );
						$free_single_quantity = get_post_meta( $coupon_id, $free_single_title . '-free_product_quantity', true );
						$free_single_quantity = ! empty( $free_single_quantity ) ? $free_single_quantity : 1;
						$wc_cart->add_to_cart( $free_single, $free_single_quantity );
					}
					// Increase the product quantity if it already exists in the cart
					if ( $wc_cart->find_product_in_cart( $free_single_key ) ) {
						$free_single_title = $this->convert_and_replace_unnecessary_string( $free_single );
						$free_single_quantity = get_post_meta( $coupon_id, $free_single_title . '-free_product_quantity', true );
						$free_single_quantity = ! empty( $free_single_quantity ) ? $free_single_quantity : 1;
						$wc_cart->set_quantity( $free_single_key, $free_single_quantity );
					}
				}
			}

		}
	}

	public function combination_of_product_against_combination_of_product( $customer_purchases, $customer_gets_as_free, $main_product_id, $coupon_id, $free_item_id, $wc_cart )
	{
		if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) )
			return;

		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
			return;

		if ( 'a_combination_of_products' === $customer_gets_as_free && 'a_combination_of_products' === $customer_purchases ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				// Checking if the cart has all products that the store owner has selected to purchase
				if ( in_array( $cart_item['product_id'], $main_product_id ) ) {
					$product_title = $this->convert_and_replace_unnecessary_string( $cart_item['product_id'] );

					$main_product_min_quantity = get_post_meta( $coupon_id, $product_title . '-purchased_min_quantity', true );

					if ( $cart_item['quantity'] >= $main_product_min_quantity ) {
						$is_main_product_greater_or_equal_to_min = true;
					}
					else {
						$is_main_product_greater_or_equal_to_min = false;
						// Show error message to the user if main product quantity is less than the store owner has selected
						add_action( 'woocommerce_before_cart', [ $this, 'cart_custom_error_message' ] );
						break;
					}
				}
			}

			foreach ( $free_item_id as $free_single ) {
				$free_single_key = $wc_cart->generate_cart_id( $free_single );
				if ( $is_main_product_greater_or_equal_to_min ) {
					// Add product to the cart if the product does not already exist in the cart
					if ( ! $wc_cart->find_product_in_cart( $free_single_key ) ) {
						$free_single_title = $this->convert_and_replace_unnecessary_string( $free_single );
						$free_single_quantity = get_post_meta( $coupon_id, $free_single_title . '-free_product_quantity', true );
						$free_single_quantity = ! empty( $free_single_quantity ) ? $free_single_quantity : 1;
						$wc_cart->add_to_cart( $free_single, $free_single_quantity );
					}
					// Increase the product quantity if it already exists in the cart
					if ( $wc_cart->find_product_in_cart( $free_single_key ) ) {
						$free_single_title = $this->convert_and_replace_unnecessary_string( $free_single );
						$free_single_quantity = get_post_meta( $coupon_id, $free_single_title . '-free_product_quantity', true );
						$free_single_quantity = ! empty( $free_single_quantity ) ? $free_single_quantity : 1;
						$wc_cart->set_quantity( $free_single_key, $free_single_quantity );
					}
				}
			}
		}
	}

	public function combination_of_product_against_any_product_listed_below( $customer_purchases, $customer_gets_as_free, $main_product_id, $coupon_id, $free_item_id, $wc_cart, $selected_products_as_free )
	{
		if ( 'any_products_listed_below' === $customer_gets_as_free && 'a_combination_of_products' === $customer_purchases ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				// Checking if the cart has all products that the store owner has selected to purchase
				if ( in_array( $cart_item['product_id'], $main_product_id ) ) {
					$product_title = $this->convert_and_replace_unnecessary_string( $cart_item['product_id'] );

					$main_product_min_quantity = get_post_meta( $coupon_id, $product_title . '-purchased_min_quantity', true );

					if ( $cart_item['quantity'] >= $main_product_min_quantity ) {
						$is_main_product_greater_or_equal_to_min = true;
					}
					else {
						$is_main_product_greater_or_equal_to_min = false;
						// Show error message to the user if main product quantity is less than the store owner has selected
						add_action( 'woocommerce_before_cart', [ $this, 'cart_custom_error_message' ] );
						break;
					}
				}
			}

			if ( $is_main_product_greater_or_equal_to_min ) {
				add_action( 'woocommerce_after_cart_table', [ $this, 'custom_content_below_coupon_button' ] );

				$this->update_quantity_after_updating_cart( $coupon_id, $free_item_id, $main_product_id, $customer_purchases );
			}

			$this->remove_product_in_case_of_any_product_listed_below( $wc_cart, $selected_products_as_free );
		}
	}

	public function any_product_listed_below_against_specific_product( $customer_purchases, $customer_gets_as_free, $wc_cart, $main_product_id, $coupon_id, $free_item_id )
	{
		if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) )
			return;

		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
			return;

		if ( 'a_specific_product' === $customer_gets_as_free && 'any_products_listed_below' === $customer_purchases ) {
			$main_product_in_cart = false;
			$quantities = $wc_cart->get_cart_item_quantities();

			foreach ( $main_product_id as $main_single_id ) {
				$main_single_key = $wc_cart->generate_cart_id( $main_single_id );

				$main_single_converted_title = $this->convert_and_replace_unnecessary_string( $main_single_id );
				$main_single_min_quantity = get_post_meta( $coupon_id, $main_single_converted_title . '-purchased_min_quantity', true );

				if ( $wc_cart->find_product_in_cart( $main_single_key ) && $quantities[$main_single_id] >= $main_single_min_quantity ) {
					$main_product_in_cart = true;
					break;
				}
			}
			if ( $main_product_in_cart ) {
				foreach ( $free_item_id as $free_single_id ) {
					$free_single_converted_title = $this->convert_and_replace_unnecessary_string( $free_single_id );
					$free_single_quantity = get_post_meta( $coupon_id, $free_single_converted_title . '-free_product_quantity', true );
					$free_single_quantity = ! empty( $free_single_quantity ) ? $free_single_quantity : 1;
					$free_single_key = $wc_cart->generate_cart_id( $free_single_id );
					// If the free product does not already exist in the cart, then add to cart
					if ( ! $wc_cart->find_product_in_cart( $free_single_key ) ) {
						$wc_cart->add_to_cart( $free_single_id, $free_single_quantity );
					}
					// If the free product does already exist in the cart, then update product quantity in the cart
					if ( $wc_cart->find_product_in_cart( $free_single_key ) ) {
						$wc_cart->set_quantity( $free_single_key, $free_single_quantity );
					}
				}
			}
			else {
				$this->remove_cart_product( $free_item_id );
			}
		}
	}

	public function any_product_listed_below_against_a_combination_of_product( $customer_purchases, $customer_gets_as_free, $wc_cart, $main_product_id, $coupon_id, $free_item_id )
	{
		if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) )
			return;

		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
			return;

		if ( 'a_combination_of_products' === $customer_gets_as_free && 'any_products_listed_below' === $customer_purchases ) {
			$main_product_in_cart = false;
			$quantities = $wc_cart->get_cart_item_quantities();

			foreach ( $main_product_id as $main_single_id ) {
				$main_single_key = $wc_cart->generate_cart_id( $main_single_id );

				$main_single_converted_title = $this->convert_and_replace_unnecessary_string( $main_single_id );
				$main_single_min_quantity = get_post_meta( $coupon_id, $main_single_converted_title . '-purchased_min_quantity', true );

				if ( $wc_cart->find_product_in_cart( $main_single_key ) && $quantities[$main_single_id] >= $main_single_min_quantity ) {
					$main_product_in_cart = true;
					break;
				}
			}

			if ( $main_product_in_cart ) {
				foreach ( $free_item_id as $free_single_id ) {
					$free_single_converted_title = $this->convert_and_replace_unnecessary_string( $free_single_id );
					$free_single_quantity = get_post_meta( $coupon_id, $free_single_converted_title . '-free_product_quantity', true );
					$free_single_quantity = ! empty( $free_single_quantity ) ? $free_single_quantity : 1;
					$free_single_key = $wc_cart->generate_cart_id( $free_single_id );
					// If the free product does not already exist in the cart, then add to cart
					if ( ! $wc_cart->find_product_in_cart( $free_single_key ) ) {
						$wc_cart->add_to_cart( $free_single_id, $free_single_quantity );
					}
					// If the free product does already exist in the cart, then update product quantity in the cart
					if ( $wc_cart->find_product_in_cart( $free_single_key ) ) {
						$wc_cart->set_quantity( $free_single_key, $free_single_quantity );
					}
				}
			}
			else {
				$this->remove_cart_product( $free_item_id );
			}
		}
	}

	public function any_product_listed_below_against_any_product_listed_below( $customer_purchases, $customer_gets_as_free, $wc_cart, $main_product_id, $coupon_id, $free_item_id, $selected_products_as_free )
	{
		if ( 'any_products_listed_below' === $customer_gets_as_free && 'any_products_listed_below' === $customer_purchases ) {
			$main_product_in_cart = false;
			$quantities = $wc_cart->get_cart_item_quantities();

			foreach ( $main_product_id as $main_single_id ) {
				$main_single_key = $wc_cart->generate_cart_id( $main_single_id );

				$main_single_converted_title = $this->convert_and_replace_unnecessary_string( $main_single_id );
				$main_single_min_quantity = get_post_meta( $coupon_id, $main_single_converted_title . '-purchased_min_quantity', true );

				if ( $wc_cart->find_product_in_cart( $main_single_key ) && $quantities[$main_single_id] >= $main_single_min_quantity ) {
					$main_product_in_cart = true;
					break;
				}
			}

			if ( $main_product_in_cart ) {
				add_action( 'woocommerce_after_cart_table', [ $this, 'custom_content_below_coupon_button' ] );

				$this->update_quantity_after_updating_cart( $coupon_id, $free_item_id, $main_product_id, $customer_purchases );

				$this->remove_product_in_case_of_any_product_listed_below( $wc_cart, $selected_products_as_free );
			}
			else {
				$this->remove_cart_product( $free_item_id );
			}
		}
	}

	public function product_categories_against_specific_product_and_combination_of_product( $customer_purchases, $customer_gets_as_free, $free_item_id, $wc_cart, $coupon_id )
	{
		if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) )
			return;

		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
			return;

		if ( ( 'a_specific_product' === $customer_gets_as_free || 'a_combination_of_products' === $customer_gets_as_free ) && 'product_categories' === $customer_purchases ) {
			foreach ( $free_item_id as $free_single ) {
				$free_single_key = $wc_cart->generate_cart_id( $free_single );
				$free_single_converted_title = $this->convert_and_replace_unnecessary_string( $free_single );
				$free_single_quantity = get_post_meta( $coupon_id, $free_single_converted_title . '-free_product_quantity', true );
				$free_single_quantity = ! empty( $free_single_quantity ) ? $free_single_quantity : 1;

				// Add the product to the cart if it's not already been added in the cart
				if ( ! $wc_cart->find_product_in_cart( $free_single_key ) ) {
					$wc_cart->add_to_cart( $free_single, $free_single_quantity );
				}
				// Update the product quantity if it's already been added in the cart
				if ( $wc_cart->find_product_in_cart( $free_single_key ) ) {
					$wc_cart->set_quantity( $free_single_key, $free_single_quantity );
				}
			}
		}
	}

	public function product_categories_against_any_product_listed_below( $customer_purchases, $customer_gets_as_free, $coupon_id, $free_item_id, $main_product_id, $wc_cart, $selected_products_as_free )
	{
		if ( 'any_products_listed_below' === $customer_gets_as_free && 'product_categories' === $customer_purchases ) {
			add_action( 'woocommerce_after_cart_table', [ $this, 'custom_content_below_coupon_button_for_categories' ] );

			$this->update_quantity_after_updating_cart( $coupon_id, $free_item_id, $main_product_id, $customer_purchases );

			$this->remove_product_in_case_of_any_product_listed_below( $wc_cart, $selected_products_as_free );
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method deduct_bogo_discount_amount_from_subtotal
	 * @return string
	 * Deduct bogo discount amount from the cart subtotal
	 */
	public function deduct_bogo_discount_amount_from_subtotal( $cart_subtotal, $compound, $obj )
	{
		$price_to_be_deducted = $this->custom_fee_for_bogo_deal( $obj );

		$final_subtotal_price = wc_price( (float)$obj->get_subtotal() - (float)$price_to_be_deducted );

		return $final_subtotal_price;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method remove_product_in_case_of_any_product_listed_below
	 * @return mixed
	 * Remove product from cart if more than one product is selected in the cart from the list.
	 */
	public function remove_product_in_case_of_any_product_listed_below( $wc_cart, $selected_products_as_free )
	{
		// Get the cart contents
		$cart_contents = $wc_cart->get_cart();

		$product_ids = [];

		foreach ( $cart_contents as $cart_item ) {
			$product_ids[] = $cart_item['product_id'];
		}

		$common_elements  = array_intersect( $product_ids, $selected_products_as_free );

		// removing the first element from the array
		array_shift( $common_elements );

		// removing the products from the cart if more than one product is added to the cart
		$this->remove_cart_product( $common_elements );

		add_action( 'woocommerce_before_cart', [ $this, 'cart_custom_error_message_two' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method custom_fee_for_bogo_deal
	 * @return mixed
	 * Add discount fee based on bogo deal
	 */
	public function custom_fee_for_bogo_deal( $cart )
	{
		$coupon_id = $this->coupon_id(); // Get the id of applied coupon

		$all_meta_values = $this->get_all_post_meta( $coupon_id );

		$free_items_id = ! empty( $all_meta_values['add_specific_product_for_free'] ) ? $all_meta_values['add_specific_product_for_free'] : [];
		$customer_gets_as_free = ! empty( $all_meta_values['customer_gets_as_free'] ) ? $all_meta_values['customer_gets_as_free'] : [];

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;

		$total_subtotal = 0;

		// Loop through cart items to calculate total subtotal
//		if ( 'same_product_as_free' === $customer_gets_as_free ) {
			foreach ( $cart->get_cart() as $cart_item ) {
				if ( in_array( $cart_item['product_id'], $free_items_id ) ) {
					$product_title = $this->convert_and_replace_unnecessary_string( $cart_item['product_id'] );
					$product_free_quantity = get_post_meta( $coupon_id, $product_title . '-free_product_quantity', true );
					$product_free_amount = get_post_meta( $coupon_id, $product_title . '-free_amount', true );
					$product_discount_type = get_post_meta( $coupon_id, $product_title . '-hexcoupon_bogo_discount_type', true );
					if ( 'fixed' === $product_discount_type && $cart_item['quantity'] >= $product_free_quantity ) {
						if ( $product_free_amount > $cart_item['data']->get_price() ) {
							$product_free_amount = $cart_item['data']->get_price();
						}
						if ( $product_free_amount <= 0 ) {
							$product_free_amount = 0;
						}

						$total_subtotal += $product_free_quantity * $product_free_amount;
					}
					if ( 'percent' === $product_discount_type && $cart_item['quantity'] >= $product_free_quantity ) {
						if ( $product_free_amount > 100 ) {
							$product_free_amount = 100;
						}
						if ( $product_free_amount <= 0 ) {
							$product_free_amount = 0;
						}
						$total_subtotal += ($product_free_amount / 100) * $cart_item['data']->get_price() * $product_free_quantity;
					}
				}
			}


			if ( $cart->get_applied_coupons() )
				$cart->add_fee( __( 'Total Bogo Discount', 'hex-coupon-for-woocommerce' ), -$total_subtotal );
//		}

		return $total_subtotal;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method alter_product_subtotal_in_cart_for_bogo
	 * @return mixed
	 * Show product new subtotal in cart according to the Bogo discounts
	 */
	public function alter_product_subtotal_in_cart_for_bogo( $product_subtotal, $product, $quantity, $cart )
	{
		$coupon_id = $this->coupon_id(); // Get the id of applied coupon

		$all_meta_values = $this->get_all_post_meta( $coupon_id );

		$customer_purchases = ! empty( $all_meta_values['customer_purchases'] ) ? $all_meta_values['customer_purchases'] : '';
		$customer_gets_as_free = ! empty( $all_meta_values['customer_gets_as_free'] ) ? $all_meta_values['customer_gets_as_free'] : '';

		$main_product_id = ! empty( $all_meta_values['add_specific_product_to_purchase'] ) ? $all_meta_values['add_specific_product_to_purchase'] : [];
		$free_items_id = ! empty( $all_meta_values['add_specific_product_for_free'] ) ? $all_meta_values['add_specific_product_for_free'] : [];

		$main_product_array_to_string = implode( '', $main_product_id );

		$main_product_converted_string = $this->convert_and_replace_unnecessary_string( $main_product_array_to_string );

		$main_product_min_quantity = get_post_meta( $coupon_id, $main_product_converted_string . '-purchased_min_quantity', true );

		if ( 'a_specific_product' === $customer_gets_as_free || 'a_combination_of_products' === $customer_gets_as_free || 'any_products_listed_below' === $customer_gets_as_free || 'same_product_as_free' === $customer_gets_as_free ) {
			if ( in_array( $product->get_id(), $free_items_id ) ) {
				$string_to_be_replaced = [ ' ', '_' ];

				$product_title = get_the_title( $product->get_id() );
				$converted_string = strtolower( str_replace( $string_to_be_replaced, '_', $product_title ) );

				$free_quantity = get_post_meta( $coupon_id, $converted_string . '-free_product_quantity', true );

				$free_amount = get_post_meta( $coupon_id, $converted_string . '-free_amount', true );
				$free_amount = ! empty( $free_amount ) ? $free_amount : 0;
				$hexcoupon_bogo_discount_type = get_post_meta( $coupon_id, $converted_string . '-hexcoupon_bogo_discount_type', true );

				$original_subtotal = floatval($product->get_price() * $quantity );

				$custom_subtotal = $original_subtotal;

				if ( 'fixed' === $hexcoupon_bogo_discount_type && array_diff( $main_product_id, $free_items_id ) ) {
					if ( $free_amount > $product->get_price() ) {
						$free_amount = $product->get_price();
					}
					if ( $free_amount <= 0 ) {
						$free_amount = 0;
					}
					$custom_subtotal = $original_subtotal - ( $free_amount * $quantity );
				}
				if ( 'percent' === $hexcoupon_bogo_discount_type && array_diff( $main_product_id, $free_items_id ) ) {
					if ( $free_amount > 100 ) {
						$free_amount = 100;
					}
					if ( $free_amount <= 0 ) {
						$free_amount = 0;
					}
					$custom_subtotal = $original_subtotal * ( ( 100 - $free_amount ) / 100 );
				}

				if ( 'fixed' === $hexcoupon_bogo_discount_type && ! array_diff( $main_product_id, $free_items_id ) && $quantity >= $main_product_min_quantity ) {
					if ( $free_amount > $product->get_price() ) {
						$free_amount = $product->get_price();
					}
					if ( $free_amount <= 0 ) {
						$free_amount = 0;
					}
					$custom_subtotal = $original_subtotal - ( $free_amount * $free_quantity );
				}
				if ( 'percent' === $hexcoupon_bogo_discount_type && ! array_diff( $main_product_id, $free_items_id ) && $quantity >= $main_product_min_quantity ) {
					if ( $free_amount > 100 ) {
						$free_amount = 100 * $free_quantity;
					}
					if ( $free_amount <= 0 ) {
						$free_amount = 0 * $free_quantity;
					}

					$free_amount = $free_amount * $free_quantity;

					// Calculate the amount to subtract
					$amount_to_substract = ( $free_amount / 100 ) * $product->get_price();

					// Subtract the amount
					$custom_subtotal = $original_subtotal - $amount_to_substract;
				}

				// Format the custom subtotal for display
				$formatted_subtotal = wc_price( $custom_subtotal );

				return $formatted_subtotal;
			}
		}

		// Other than the Bogo free products, prices of all other products will be the same.
		return $product_subtotal;
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

		// assigning an empty string
		$coupon_id = '';

		// check if there are applied coupon
		if ( ! empty( $applied_coupon ) ) {
			// Assuming only one coupon is applied; if multiple, you might need to loop through $applied_coupon array
			$coupon_code = reset( $applied_coupon );
			$coupon_id = wc_get_coupon_id_by_code( $coupon_code ); // get the coupon id from the coupon code
		}

		// finally return the coupon code id
		return $coupon_id;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method remove_cart_product
	 * @return mixed
	 * remove cart items/products from cart page
	 */
	public function remove_cart_product( $free_item_id )
	{
		foreach ( $free_item_id as $free_item_single ) {
			$free_single_key = WC()->cart->generate_cart_id( $free_item_single );
			WC()->cart->remove_cart_item( $free_single_key );
		}
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
		// Get the WooCommerce cart
		$cart = WC()->cart;

		// Initialize an empty array to store category IDs and their occurrences
		$category_occurrences = [];

		// Loop through cart items
		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			// Get product ID
			$product_id = $cart_item['product_id'];

			// Get product quantity
			$quantity = $cart_item['quantity'];

			// Get product categories
			$categories = get_the_terms( $product_id, 'product_cat' );

			// Loop through categories
			if ( $categories && ! is_wp_error( $categories ) ) {
				foreach ( $categories as $category ) {
					// Get category ID
					$category_id = $category->term_id;

					// Check if category ID already exists in the array
					if ( array_key_exists( $category_id, $category_occurrences ) ) {
						// Add quantity to existing category occurrence
						$category_occurrences[ $category_id ] += $quantity;
					} else {
						// Add new category occurrence
						$category_occurrences[ $category_id ] = $quantity;
					}
				}
			}
		}

		return $category_occurrences;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method update_quantity_after_updating_cart
	 * @return void
	 * Updating product quantity after clicking on update cart button
	 */
	public function update_quantity_after_updating_cart( $coupon_id, $free_item_id, $main_product_id, $customer_purchases )
	{
		// Initialize the cart object
		$wc_cart = WC()->cart;

		$main_product_ids = $main_product_id;
		$main_product_single_title_lower_case = '';
		$main_product_id = '';

		foreach ( $main_product_ids as $main_single_id ) {
			$main_product_id = $main_single_id;
			$main_product_single_title_lower_case = $this->convert_and_replace_unnecessary_string( $main_single_id );
		}

		// get main purchased product minimum quantity
		$main_product_min_purchased_quantity = get_post_meta( $coupon_id, $main_product_single_title_lower_case . '-purchased_min_quantity', true );
		$main_product_min_purchased_quantity = ! empty( $main_product_min_purchased_quantity ) ? $main_product_min_purchased_quantity : 1;

		$cart_item_quantity = 0;

		foreach ( $wc_cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( $main_product_id  == $cart_item['product_id'] ) {
				$cart_item_quantity = $cart_item['quantity'];
				break;
			}
		}

		if ( 'a_specific_product' === $customer_purchases || 'a_combination_of_products' === $customer_purchases || 'any_products_listed_below' == $customer_purchases ) {
			if ( $cart_item_quantity >= $main_product_min_purchased_quantity ) {
				foreach ( $free_item_id as $free_single_id ) {
					$free_single_product_key = $wc_cart->generate_cart_id( $free_single_id );
					// Find and search if the free product exists in the cart page.
					if ( $wc_cart->find_product_in_cart( $free_single_product_key ) ) {
						$free_product_title_lowercase = $this->convert_and_replace_unnecessary_string( $free_single_id );
						$free_product_quantity = get_post_meta( $coupon_id, $free_product_title_lowercase . '-free_product_quantity', true );
						// Executes the below code, if the cart item quantity is equals to the main product min purchased quantity
						if ( $cart_item_quantity >= $main_product_min_purchased_quantity ) {
							if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) )
								return;

							if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
								return;

							$customer_gets = $free_product_quantity;
							$wc_cart->set_quantity( $free_single_product_key, $customer_gets );
							break;
						}
					}
				}
			}
			add_action( 'woocommerce_before_cart', [ $this, 'cart_custom_success_message' ] );
		}

		// If customer purchases from 'product_categories'
		if ( 'product_categories' === $customer_purchases ) {
			foreach ( $free_item_id as $free_single_id ) {
				$free_single_product_key = $wc_cart->generate_cart_id( $free_single_id );
				// Find and search if the free product exists in the cart page.
				if ( $wc_cart->find_product_in_cart( $free_single_product_key ) ) {
					$free_product_title_lowercase = $this->convert_and_replace_unnecessary_string( $free_single_id );
					$free_product_quantity = get_post_meta( $coupon_id, $free_product_title_lowercase . '-free_product_quantity', true );

					if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) )
						return;

					if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
						return;

					$customer_gets = $free_product_quantity;
					$wc_cart->set_quantity( $free_single_product_key, $customer_gets );
					break;
				}
			}
			add_action( 'woocommerce_before_cart', [ $this, 'cart_custom_success_message' ] );
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method cart_custom_success_message
	 * @return void
	 * @since 1.0.0
	 * Show success message after adding free product in the cart.
	 */
	public function cart_custom_success_message()
	{
		$message = __( 'Free Bogo products added successfully!', 'hex-coupon-for-woocommerce' );

		wc_print_notice( $message );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method cart_custom_error_message
	 * @return void
	 * @since 1.0.0
	 * Show error message if customer does not have enough main item to get the bogo deal.
	 */
	public function cart_custom_error_message()
	{
		$message = __( 'You do not have enough item or enough quantity to avail the Bogo offer.', 'hex-coupon-for-woocommerce' );

		wc_print_notice( $message, 'error' );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method cart_custom_error_message_two
	 * @return void
	 * @since 1.0.0
	 * Show error message if customer tries to add more than one product from the list below.
	 */
	public function cart_custom_error_message_two()
	{
		$message = __( 'You can not add more than one product from the below list.', 'hex-coupon-for-woocommerce' );

		wc_print_notice( $message, 'error' );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method convert_and_replace_unnecessary_string
	 * @param int $post_id
	 * @return string
	 * @since 1.0.0
	 * Replace space ' ', and hyphen '-' from the string with '_' underscore, and convert the uppercase letters to lowercase.
	 */
	public function convert_and_replace_unnecessary_string( $post_id )
	{
		$string = get_the_title( $post_id );
		$string_to_be_replaced = [ ' ', '-' ];
		$replaced_string = strtolower( str_replace( $string_to_be_replaced, '_', $string ) );
		return $replaced_string;
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

		$coupon_id = $this->coupon_id(); // get the id of applied coupon from the cart page

		$all_meta_values = $this->get_all_post_meta( $coupon_id );

		// get all the products ids that has to be purchased
		$selected_products_as_to_be_purchased = $all_meta_values['add_specific_product_to_purchase'];

		// get the product id's of all free items that customer will get
		$selected_products_as_free = $all_meta_values['add_specific_product_for_free'];

		$main_product_min_quantity = 1;

		foreach ( $selected_products_as_to_be_purchased as $single_main_product ) {
			$main_product_id = $single_main_product;
			$main_product_title = $this->convert_and_replace_unnecessary_string( $single_main_product );
			$main_product_min_quantity = get_post_meta( $coupon_id, $main_product_title . '-purchased_min_quantity', true );
		}

		$cart_product_ids = [];

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$cart_product_ids[] = $cart_item['product_id']; // assign all ids of products in the cart in an array
		}

		$matched_product_id = array_intersect( $selected_products_as_to_be_purchased, $cart_product_ids );

		$cart_product_quantity = 1;

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( ! empty( $matched_product_id[0] ) && $matched_product_id[0] == $cart_item['product_id'] ) {
				$cart_product_quantity = $cart_item['quantity'];
			}
		}

		// Checking if we are on the cart page
		if ( ( $cart_product_quantity >= $main_product_min_quantity ) && is_cart() ) {
			echo '<div class="hexcoupon_select_free_item">';
			// Add content for the free items
			echo '<h3>' . esc_html__( 'Select any product from below list', 'hex-coupon-for-woocommerce' ) . '</h3>';

			foreach ( $selected_products_as_free as $product_id ) {
				$free_product_title = $this->convert_and_replace_unnecessary_string( $product_id );

				$free_product_quantity = get_post_meta( $coupon_id, $free_product_title . '-free_product_quantity', true );
				$free_product_quantity = ! empty( $free_product_quantity ) ? $free_product_quantity : 1;

				// Output each product
				$product = wc_get_product( $product_id );
				if ( $product ) {
					echo '<div class="custom-product">';
					echo '<a href="' . get_permalink( $product_id ) . '">' . $product->get_image() . '</a>';
					echo '<h3 class="has-text-align-center wp-block-post-title has-medium-font-size"><a href="' . get_permalink ( $product_id ) . '">' . $product->get_name() . '</a></h3>';
					echo '<p class="price has-font-size has-small-font-size has-text-align-center">' . $product->get_price_html() . '</p>';
					echo '<form class="cart" action="" method="post">';
					echo '<input type="hidden" name="quantity" value="' . esc_attr( $free_product_quantity ) . '">';
					echo '<div class="has-text-align-center"><button type="submit" name="add-to-cart" value="' . esc_attr( $product_id ) . '" class="button wp-element-button wp-block-button__link">' . esc_html__( 'Add to Cart', 'hex-coupon-for-woocommerce' ) . '</button></div>';
					echo '</form>';
					echo '</div>';
				}
			}

			echo '</div>';
		}
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method custom_content_below_coupon_button_for_categories
	 * @return void
	 * Display the free items below the apply coupon button.
	 */
	public function custom_content_below_coupon_button_for_categories()
	{
		global $product;

		$coupon_id = $this->coupon_id(); // get the id of applied coupon from the cart page

		$all_meta_values = $this->get_all_post_meta( $coupon_id );

		$customer_purchases = $all_meta_values['customer_purchases'];

		// get the product id's of all free items that customer will get
		$selected_products_as_free = $all_meta_values['add_specific_product_for_free'];

		// Checking if we are on the cart page
		if ( 'product_categories' === $customer_purchases && is_cart() ) {
			echo '<div class="hexcoupon_select_free_item">';
			// Add content for the free items
			echo '<h3>' . esc_html__( 'Select any product from below list', 'hex-coupon-for-woocommerce' ) . '</h3>';

			foreach ( $selected_products_as_free as $product_id ) {
				$free_product_title = $this->convert_and_replace_unnecessary_string( $product_id );

				$free_product_quantity = get_post_meta( $coupon_id, $free_product_title . '-free_product_quantity', true );
				$free_product_quantity = ! empty( $free_product_quantity ) ? $free_product_quantity : 1;

				// Output each product
				$product = wc_get_product( $product_id );
				if ( $product ) {
					echo '<div class="custom-product">';
					echo '<a href="' . get_permalink( $product_id ) . '">' . $product->get_image() . '</a>';
					echo '<h3 class="has-text-align-center wp-block-post-title has-medium-font-size"><a href="' . get_permalink ( $product_id ) . '">' . $product->get_name() . '</a></h3>';
					echo '<p class="price has-font-size has-small-font-size has-text-align-center">' . $product->get_price_html() . '</p>';
					echo '<form class="cart" action="" method="post">';
					echo '<input type="hidden" name="quantity" value="' . esc_attr( $free_product_quantity ) . '">';
					echo '<div class="has-text-align-center"><button type="submit" name="add-to-cart" value="' . esc_attr( $product_id ) . '" class="button wp-element-button wp-block-button__link">' . esc_html__( 'Add to Cart', 'hex-coupon-for-woocommerce' ) . '</button></div>';
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
		$wc_cart = WC()->cart;

		$coupon_id = $this->coupon_id(); // get the id of applied coupon from the cart page

		$all_meta_values = $this->get_all_post_meta( $coupon_id );

		$customer_purchases = $all_meta_values['customer_purchases'];

		$selected_products_to_purchase = $all_meta_values['add_specific_product_to_purchase']; // get purchasable selected product
		$selected_products_as_free = $all_meta_values['add_specific_product_for_free']; // get free selected product

		$customer_gets_as_free = $all_meta_values['customer_gets_as_free']; // get meta value of customer gets as free

		$add_categories_to_purchase = ! empty( $all_meta_values['add_categories_to_purchase'] ) ? $all_meta_values['add_categories_to_purchase'] : []; // get the meta-value of coupon purchasable product categories

		// Product IDs
		$main_product_id = ! empty( $selected_products_to_purchase ) ? $selected_products_to_purchase : []; // product ids that has to be existed in the cart to apply BOGO deals
		$free_item_id = ! empty( $selected_products_as_free ) ? $selected_products_as_free : []; // ids of products that customer will get as free

		// Initializing '$cart_product_ids' variable for all cart products ids
		$cart_product_ids = [];

		// Assigning all product ids of cart page into the '$cart_product_ids' variable
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$cart_product_ids[] = $cart_item['product_id']; // assign all ids of products in the cart in an array
		}

		$main_product_in_cart = false; // '$main_product_in_cart' is false if there are no products in the cart that needs to be there to apply BOGO deals.

		$main_product_single_title = '';
		$main_product_single_id = 0;
		$cart_item_quantity = 0;

		// Check if the cart has the exact or any product from the list that the admin has selected to purchase
		if ( 'a_specific_product' === $customer_purchases || 'any_products_listed_below' === $customer_purchases ) {
			foreach ( $main_product_id as $main_product_single ) {
				if ( in_array( $main_product_single, $cart_product_ids ) ) {
					$main_product_single_id = $main_product_single;

					$main_product_single_title = get_the_title( $main_product_single );

					$main_product_in_cart = true; // if the cart has the product it assigns value of '$main_product_in_cart' to 'true'
					break;
				}
			}
		}

		// Check if the cart has all the exact products that the admin has selected to purchase
		if ( 'a_combination_of_products' === $customer_purchases ) {
			foreach ( $main_product_id as $main_product_single ) {
				if ( in_array( $main_product_single, $cart_product_ids ) ) {
					$main_product_in_cart = true; // if the cart does not have the product it assigns value of '$main_product_in_cart' to 'false'
				}
				else {
					$main_product_in_cart = false; // else it becomes true
					break;
				}
			}
		}

		// Define strings that need to be replaced in the title
		$string_to_be_replaced = [ ' ', '-' ];

		$main_product_single_title_lower_case = str_replace( $string_to_be_replaced, '_', strtolower( $main_product_single_title ) );

		// get main purchased product minimum quantity
		$main_product_min_purchased_quantity = get_post_meta( $coupon_id, $main_product_single_title_lower_case.'-purchased_min_quantity', true );
		$main_product_min_purchased_quantity = ! empty( $main_product_min_purchased_quantity ) ? $main_product_min_purchased_quantity : 1;

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( $main_product_single_id  == $cart_item['product_id'] ) {
				$cart_item_quantity = $cart_item['quantity'];
				break;
			}
		}

		if ( 'product_categories' === $customer_purchases ) {
			$string_tobe_converted = [ ' ', '-' ];

			$cart_product_cat_occurances = $this->get_product_categories_id_in_cart();

			foreach ( $add_categories_to_purchase as $category_single ) {
				if ( array_key_exists( $category_single, $cart_product_cat_occurances ) ) {
					$category = get_term( $category_single, 'product_cat' );

					if ( $category && ! is_wp_error( $category ) ) {
						$category_name = $category->name;
						$category_converted_name = str_replace( $string_tobe_converted, '_', strtolower( $category_name ) );
						$category_purchased_min_category = get_post_meta( $coupon_id, $category_converted_name . '-purchased_category_min_quantity', true );

						$cart_cat_quantity = $cart_product_cat_occurances[$category_single];

						if ( $cart_cat_quantity >= $category_purchased_min_category ) {
							$main_product_in_cart = true;
						}
					}
				}
			}
		}

		$is_main_product_greater_or_equal_to_min = false;
		// Add free item to cart if the main product is in the cart
		if ( $main_product_in_cart ) {
			// Add product in the case of customer purchases 'a specific product' and getting 'a specific product' as free
			$this->specific_products_against_specific_products( $customer_purchases, $customer_gets_as_free, $main_product_min_purchased_quantity, $cart_item_quantity, $free_item_id, $string_to_be_replaced, $coupon_id, $main_product_single_id, $cart_product_ids );

			// add product in the case of customer purchases 'a specific product' and getting 'same product as free'
			$this->specific_products_against_same_product( $customer_purchases, $customer_gets_as_free, $main_product_min_purchased_quantity, $cart_item_quantity, $free_item_id, $coupon_id, $wc_cart );

			// add product in the case of customer purchases 'a specific product' and getting 'a combination of products'
			$this->specific_products_against_a_combination_of_products( $customer_purchases, $customer_gets_as_free, $main_product_min_purchased_quantity, $cart_item_quantity, $free_item_id, $coupon_id, $wc_cart, $main_product_id );

			// Add product in the case of customer purchases 'a specific product' and gets 'any products listed from a list' as free
			$this->specific_products_against_any_product_listed_below( $customer_purchases, $customer_gets_as_free, $main_product_min_purchased_quantity, $cart_item_quantity, $free_item_id, $wc_cart, $selected_products_as_free, $coupon_id, $main_product_id );

			if ( 'same_product_as_free' === $customer_gets_as_free && 'a_specific_product'  === $customer_purchases ) {
				if ( ! array_diff( $main_product_id, $free_item_id ) ) {
					foreach ( $free_item_id as $free_single_item ) {
						if ( is_admin() && ! defined('DOING_AJAX') ) {
							return;
						}
						if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
							return;
						}

						$free_item_title = get_the_title( $free_single_item );

						wc_print_notice('Add at least ' . $main_product_min_purchased_quantity . ' "' . $free_item_title . '" to get the discount.');
					}
				}
			}

			// Add product in the case of customer  purchases 'a combination of products' and gets 'a specific product' as free
			$this->combination_of_product_against_specific_product( $customer_purchases, $customer_gets_as_free, $main_product_id, $coupon_id, $free_item_id, $wc_cart );


			// Add product in the case of customer  purchases 'a combination of products' and gets 'a combination product' as free
			$this->combination_of_product_against_combination_of_product( $customer_purchases, $customer_gets_as_free, $main_product_id, $coupon_id, $free_item_id, $wc_cart );

			// Add product in the case of customer  purchases 'a combination of products' and gets 'any_products_listed_below' as free
			$this->combination_of_product_against_any_product_listed_below( $customer_purchases, $customer_gets_as_free, $main_product_id, $coupon_id, $free_item_id, $wc_cart, $selected_products_as_free );

			// Add product in the case of customer  purchases 'any_products_listed_below' and gets 'a_specific_product' as free
			$this->any_product_listed_below_against_specific_product( $customer_purchases, $customer_gets_as_free, $wc_cart, $main_product_id, $coupon_id, $free_item_id );


			// Add product in the case of customer  purchases 'any_products_listed_below' and gets 'a_combination_of_products' as free
			$this->any_product_listed_below_against_a_combination_of_product( $customer_purchases, $customer_gets_as_free, $wc_cart, $main_product_id, $coupon_id, $free_item_id );


			// Add product in the case of customer  purchases 'any_products_listed_below' and gets 'any_products_listed_below' as free
			$this->any_product_listed_below_against_any_product_listed_below( $customer_purchases, $customer_gets_as_free, $wc_cart, $main_product_id, $coupon_id, $free_item_id, $selected_products_as_free );

			// Add product in the case of customer  purchases from 'product_categories' and gets 'a_specific_product' and 'a_combination_of_products' as free
			$this->product_categories_against_specific_product_and_combination_of_product( $customer_purchases, $customer_gets_as_free, $free_item_id, $wc_cart, $coupon_id );

			// Add product in the case of customer  purchases 'product_categories' and gets 'any_products_listed_below' as free
			$this->product_categories_against_any_product_listed_below( $customer_purchases, $customer_gets_as_free, $coupon_id, $free_item_id, $main_product_id, $wc_cart, $selected_products_as_free );
		}
		// Remove all free items from the cart if the main product does not exist in the cart
		else {
			$this->remove_cart_product( $free_item_id );
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

		$customer_gets_as_free = get_post_meta( $coupon_id, 'customer_gets_as_free', true );

		$free_items_id = get_post_meta( $coupon_id, 'add_specific_product_for_free', true );
		$main_product_id = get_post_meta( $coupon_id, 'add_specific_product_to_purchase', true );
		$main_product_id = ! empty( $main_product_id ) ? $main_product_id : [];

		$main_product_array_to_string = implode( '', $main_product_id );

		$main_product_converted_string = $this->convert_and_replace_unnecessary_string( $main_product_array_to_string );

		$product_min_quantity = get_post_meta( $coupon_id, $main_product_converted_string . '-purchased_min_quantity', true );

		$item_price = wc_get_price_excluding_tax( $cart_item['data'] );

		if ( 'a_specific_product' === $customer_gets_as_free || 'a_combination_of_products' === $customer_gets_as_free || 'any_products_listed_below' === $customer_gets_as_free ) {
			if ( in_array( $cart_item['product_id'], $free_items_id ) ) {
				$string_to_be_replaced = [ ' ', '-' ];

				$product_title = get_the_title( $cart_item['product_id'] );
				$converted_string = strtolower( str_replace( $string_to_be_replaced, '_', $product_title ) );

				$free_amount = get_post_meta( $coupon_id, $converted_string . '-free_amount', true );
				$free_amount = ! empty( $free_amount ) ? $free_amount : 0;
				$hexcoupon_bogo_discount_type = get_post_meta( $coupon_id, $converted_string . '-hexcoupon_bogo_discount_type', true );

				$text = __( '(BOGO Deal) <br>', 'hex-coupon-for-woocommerce' );

				$allowed_tag = [
					'br' => []
				];
				var_dump(!array_diff( $main_product_id, $free_items_id ));

				if ( 'fixed' === $hexcoupon_bogo_discount_type && array_diff( $main_product_id, $free_items_id ) ) {
					// Get the free product quantity
					$product_free_quantity = get_post_meta( $coupon_id,  $converted_string . '-free_product_quantity', true );

					if ( $free_amount > $item_price ) {
						$free_amount = 100;
					}
					if ( $free_amount <= 0 ) {
						$free_amount = 0;
					}

					$item_price = $cart_item['quantity'] * $free_amount;

					$price = '<span class="free_bogo_deal_text">' . wp_kses( $text, $allowed_tag ) . '</span>' . ' (' . $price . ' * ' . $cart_item['quantity'] . 'x) - (' . $product_free_quantity . 'x * ' . $free_amount . ') = (-' . wc_price( $free_amount * $product_free_quantity ) . ')';
				}
				if ( 'percent' === $hexcoupon_bogo_discount_type && array_diff( $main_product_id, $free_items_id ) ) {
					$product_free_quantity = get_post_meta( $coupon_id,  $converted_string . '-free_product_quantity', true );

					if ( $free_amount > 100 ) {
						$free_amount = 100;
					}
					if ( $free_amount <= 0 ) {
						$free_amount = 0;
					}

					$item_price = $item_price * ( ( 100 - $free_amount ) / 100 ) * $cart_item['quantity'];

					$price = '<span class="free_bogo_deal_text">' . wp_kses( $text, $allowed_tag ) . '</span>' . ' (' . $price . ' * ' . $cart_item['quantity'] .'x) - (' . $product_free_quantity . 'x * ' . $free_amount .'%) = (-' . wc_price( ( $free_amount / 100 ) * ( $cart_item['data']->get_price() * $product_free_quantity ) ) .')';
				}
			}
		}

		if ( 'same_product_as_free' === $customer_gets_as_free ) {
			if ( in_array( $cart_item['product_id'], $free_items_id ) && $cart_item['quantity'] >= $product_min_quantity ) {
				$string_to_be_replaced = [ ' ', '-' ];

				$product_title = get_the_title( $cart_item['product_id'] );
				$converted_string = strtolower( str_replace( $string_to_be_replaced, '_', $product_title ) );

				$free_amount = get_post_meta( $coupon_id, $converted_string . '-free_amount', true );
				$free_amount = ! empty( $free_amount ) ? $free_amount : 0;
				$hexcoupon_bogo_discount_type = get_post_meta( $coupon_id, $converted_string . '-hexcoupon_bogo_discount_type', true );

				$text = __( '(BOGO Deal) <br>', 'hex-coupon-for-woocommerce' );

				$allowed_tag = [
					'br' => []
				];

				if ( 'fixed' === $hexcoupon_bogo_discount_type && ! array_diff( $main_product_id, $free_items_id ) ) {
					// Get the free product quantity
					$product_free_quantity = get_post_meta( $coupon_id,  $converted_string . '-free_product_quantity', true );

					if ( $free_amount > $item_price ) {
						$free_amount = $item_price;
					}
					if ( $free_amount <= 0 ) {
						$free_amount = 0;
					}

					$item_price = $cart_item['quantity'] * $free_amount;

					$price = '<span class="free_bogo_deal_text">' . wp_kses( $text, $allowed_tag ) . '</span>' . ' (' . $price . ' * ' . $cart_item['quantity'] . 'x) - ' . '(' . $product_free_quantity . 'x * ' . $free_amount . ') = (-' . wc_price( $free_amount * $product_free_quantity ) . ')';
				}
				if ( 'percent' === $hexcoupon_bogo_discount_type && ! array_diff( $main_product_id, $free_items_id ) ) {
					$product_free_quantity = get_post_meta( $coupon_id,  $converted_string . '-free_product_quantity', true );

					if ( $free_amount > 100 ) {
						$free_amount = 100;
					}
					if ( $free_amount <= 0 ) {
						$free_amount = 0;
					}

					$price = '<span class="free_bogo_deal_text">' . wp_kses( $text, $allowed_tag ) . '</span>' . ' (' . $price . ' * ' . $cart_item['quantity'] .'x) - (' . $product_free_quantity . 'x * ' . $free_amount .'%) = (-' . wc_price( ( $free_amount / 100 ) * ( $cart_item['data']->get_price() * $product_free_quantity ) ) .')';
				}
			}
		}

		return $price;
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
		$coupon_id = $this->coupon_id();

		$all_meta_values = $this->get_all_post_meta( $coupon_id ); // get all free items id's

		$free_items_id = ! empty( $all_meta_values['add_specific_product_for_free'] ) ? $all_meta_values['add_specific_product_for_free'] : [];

		// Displays free item names
		$free_items = '';

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( in_array( $cart_item['product_id'], $free_items_id ) ) {
				$free_items .= esc_html( $cart_item['data']->get_name() ) . ', ';
			}
		}

		if ( ! empty( $free_items ) ) {
			$free_items = rtrim( $free_items, ', ' ); // removing ', ' from the end of the right side of the string
			echo '<tr class="free-items-row">';
			echo '<th>' . esc_html__( 'Free/Discounted Items', 'hex-coupon-for-woocommerce' ) . '</th><td class="free-items-name">' . esc_html( $free_items ) . '</td>';
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
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php echo sprintf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $this->error_message ) ); ?></p>
			</div>
			<?php
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
					?>
					<div class="notice notice-error is-dismissible">
						<p><?php echo sprintf( esc_html__( '%s', 'hex-coupon-for-woocommerce' ), esc_html( $this->error_message ) ); ?></p>
					</div>
					<?php
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
			[ 'add_specific_product_to_purchase', 'string' ],
			[ 'add_categories_to_purchase', 'string' ],
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
	 * @method save_hex_bogo_data
	 * @param int $coupon_id
	 * @return void
	 * Save the coupon bogo deal meta data.
	 */
	private function save_hex_bogo_data( $coupon_id )
	{
		$all_meta_values = $this->get_all_post_meta( $coupon_id );

		$add_specific_product_to_purchase = $all_meta_values['add_specific_product_to_purchase'];

		$string_to_be_replaced = [ ' ', '-' ];

		if ( ! empty( $add_specific_product_to_purchase ) ) {
			foreach ( $add_specific_product_to_purchase as $value ) {
				$product_title = get_the_title( $value );
				$converted_product_title = str_replace( $string_to_be_replaced, '_', strtolower( $product_title ) ) . '-purchased_min_quantity';
				$this->save_meta_data( $converted_product_title, 'string', $coupon_id );
			}
		}

		$add_categories_to_purchase = $all_meta_values['add_categories_to_purchase'];

		if ( ! empty( $add_categories_to_purchase ) ) {
			foreach ( $add_categories_to_purchase as $value ) {
				$category_name = get_term( $value, 'product_cat' );
				$category_name = $category_name->name;

				$converted_categories_title = strtolower( str_replace( $string_to_be_replaced, '_', $category_name ) ) . '-purchased_category_min_quantity';

				$this->save_meta_data( $converted_categories_title, 'string', $coupon_id );
			}
		}

		$add_specific_product_for_free = $all_meta_values['add_specific_product_for_free'];

		if ( ! empty( $add_specific_product_for_free ) ) {
			foreach ( $add_specific_product_for_free as $value ) {
				$product_title = get_the_title( $value );
				$converted_product_title = str_replace( $string_to_be_replaced, '_', strtolower( $product_title ) ) . '-free_product_quantity';
				$this->save_meta_data( $converted_product_title, 'string', $coupon_id );
			}
		}

		if ( ! empty( $add_specific_product_for_free ) ) {
			foreach ( $add_specific_product_for_free as $value ) {
				$product_title = get_the_title( $value );
				$converted_product_title = str_replace( $string_to_be_replaced, '_', strtolower( $product_title ) ) . '-hexcoupon_bogo_discount_type';
				$this->save_meta_data( $converted_product_title, 'string', $coupon_id );
			}

			foreach ( $add_specific_product_for_free as $value ) {
				$product_title = get_the_title( $value );
				$converted_product_title = str_replace( $string_to_be_replaced, '_', strtolower( $product_title ) ) . '-free_amount';
				$this->save_meta_data( $converted_product_title, 'string', $coupon_id );
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

		// Save coupon bogo deals data
		$this->save_hex_bogo_data( $coupon_id );
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
				$message = 'Coupon is not valid for this hour. Comeback at another time.';
			}
			else {
				// initialize the message
				$message = 'Coupon is not valid for today. Comeback on ' . $result . '';
			}
		}

		if ( 100 === $err_code ) {
			// Change the error message for the INVALID_FILTERED error here
			$err = $message;
		}

		return $err;
	}
}
