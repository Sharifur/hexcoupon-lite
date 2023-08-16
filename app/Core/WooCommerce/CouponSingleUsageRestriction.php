<?php
namespace HexCoupon\App\Core\WooCommerce;

use HexCoupon\App\Core\Helpers\FormHelpers;
use HexCoupon\App\Core\Helpers\RenderHelpers;
use HexCoupon\App\Core\Lib\SingleTon;

class CouponSingleUsageRestriction {
	use singleton;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return mixed
	 * @since 1.0.0
	 * Registers all hooks that are needed.
	 */
	public function register()
	{
		add_action( 'woocommerce_coupon_options_usage_restriction', [ $this, 'coupon_usage_restriction_meta_fields' ], 10, 1 );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method get_user_role_names
	 * @return array
	 * @since 1.0.0
	 * Retrieve all available role names.
	 */
	private function get_user_role_names()
	{
		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		return $wp_roles->get_names();
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method show_all_products
	 * @return array
	 * @since 1.0.0
	 * Retrieve all available WoCommerce products.
	 */
	public function show_all_products()
	{
		$all_product_titles = [];

		$products = get_posts( [
			'post_type' => 'product',
			'posts_per_page' => -1,
		] );

		foreach ( $products as $product ) {
			$all_product_titles[$product->ID] = get_the_title( $product );
		}
		return $all_product_titles;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method show_all_categories
	 * @return array
	 * @since 1.0.0
	 * Retrieve all available WoCommerce product categories.
	 */
	private function show_all_categories()
	{
		$all_categories = [];

		$product_categories = get_terms( [
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
		] );

		if ( ! empty($product_categories) && ! is_wp_error( $product_categories ) ) {
			foreach ( $product_categories as $category ) {
				$cat_id = $category->term_id;
				$all_categories[$cat_id] = $category->name;
			}
		}
		return $all_categories;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method show_user_names
	 * @return array
	 * @since 1.0.0
	 * Display name of the users.
	 */
	private function show_user_names()
	{
		// Query all users
		$args = [
			'fields' => 'all', // Get all fields of each user.
		];
		$user_query = new \WP_User_Query($args);

		$all_users_name = [];
		// Check if there are users found
		if ( ! empty( $user_query->results ) ) {
			// Loop through the users and retrieve their 'first_name', 'last_name', and 'ID'.
			foreach ( $user_query->results as $user ) {
				$all_users_name[$user->ID] = $user->first_name . ' ' . $user->last_name . ' ('.$user->user_email.')';
			}
		}

		return $all_users_name;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method product_cart_condition
	 * @return void
	 * @since 1.0.0
	 * Display meta fields for product cart condition.
	 */
	private function product_cart_condition()
	{
		global $post;

		$apply_cart_condition_on_products = get_post_meta( $post->ID, 'apply_cart_condition_for_customer_on_products', true );
		$apply_cart_condition_on_products = ! empty( $apply_cart_condition_on_products ) ? 'yes' : '';

		woocommerce_wp_checkbox(
			[
				'id' => 'apply_cart_condition_for_customer_on_products',
				'label' => esc_html__( 'Product Cart Condition', 'hexcoupon' ),
				'description' => esc_html__( 'Check this box to to add a cart condition for the customer based on product.', 'hexcoupon' ),
				'value' => $apply_cart_condition_on_products,
				'wrapper_class' => 'cart-condition',
			]
		);

		$apply_on_listed_product = get_post_meta( $post->ID, 'apply_on_listed_product', true );
		$apply_on_listed_product = ! empty( $apply_on_listed_product ) ? $apply_on_listed_product : '';

		echo '<div class="apply_on_listed_product">';

		woocommerce_wp_radio(
			[
				'id' => 'apply_on_listed_product',
				'label' => '',
				'options' => [
					'any_of_the_product' => esc_html__( 'Coupon applies if only customers cart contains any of the product listed below', 'hexcoupon' ),
					'all_of_the_product' => esc_html__( 'Coupon applies if only customers cart contains all of the product listed below', 'hexcoupon' ),
				],
				'value' => $apply_on_listed_product,
			]
		);
		echo '</div>';

		//		$output ='<div id="custom_coupon_tab" class="panel woocommerce_options_panel">';



		$all_selected_products = get_post_meta( $post->ID, 'all_selected_products', true );



		echo '<div class="all_selected_products">';

		$output = FormHelpers::Init( [
			'label' => esc_html__( 'Products', 'hexcoupon' ),
			'name' => 'all_selected_products',
			'value' => $all_selected_products,
			'type' => 'select',
			'options' => $this->show_all_products(), //if the field is select, this param will be here
			'multiple' => true,
			'select2' => true,
			'class' => 'all_selected_products',
			'placeholder' => __('Search for Product'),
		] );

		echo '<span class="all_selected_products_tooltip">'.wc_help_tip( esc_html__( 'Products that the coupon will be applied to, or that need to be in the cart in order for the &quot;Fixed cart discount&quot; to be applied.', 'hexcoupon' ) ).'</span>';

		$output .= '</div>';

		echo '<div name="all_selected_products" id="selectedValuesContainer">';
		if ( ! empty( $all_selected_products ) ) {
			foreach ( $all_selected_products as $single_product ) {
				echo '<div class="whole"><span class="select2-selection__choice"></span>';
				echo get_the_title( $single_product );
				echo "<div class='product_min_max'><input name='product_min_quantity[]' placeholder='No minimum' type='number' style='float:left; width:50% !important;'><input name='product_max_quantity[]' style='width:50% !important;' placeholder='No maximum' type='number'><a class='remove_product'>X</a></div></div>";
			}
		}
		echo '</div>';


		echo wp_kses( $output, RenderHelpers::getInstance()->Wp_Kses_Allowed_For_Forms() );

//		echo '</div>';



	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method category_cart_condition
	 * @return void
	 * @since 1.0.0
	 * Display meta fields for category cart condition.
	 */
	public function category_cart_condition()
	{
		global $post;

		$apply_cart_condition_on_categories = get_post_meta( $post->ID, 'apply_cart_condition_for_customer_on_categories', true );
		$apply_cart_condition_on_categories = ! empty( $apply_cart_condition_on_categories ) ? 'yes' : '';

		woocommerce_wp_checkbox(
			[
				'id' => 'apply_cart_condition_for_customer_on_categories',
				'label' => esc_html__( 'Category Cart Condition', 'hexcoupon' ),
				'description' => esc_html__( 'Check this box to to add a cart condition for the customer based on category.', 'hexcoupon' ),
				'value' => $apply_cart_condition_on_categories,
				'wrapper_class' => 'category-cart-condition'
			]
		);

		//		$output ='<div id="custom_coupon_tab" class="panel woocommerce_options_panel">';

		$all_selected_categories = get_post_meta( $post->ID, 'all_selected_categories', true );

		echo '<div class="all_selected_categories">';
		$output = FormHelpers::Init( [
			'label' => esc_html__( 'Product Categories', 'hexcoupon' ),
			'name' => 'all_selected_categories',
			'value' => $all_selected_categories,
			'type' => 'select',
			'options' => $this->show_all_categories(), //if the field is select, this param will be here
			'multiple' => true,
			'select2' => true,
			'class' => 'all_selected_categories',
			'placeholder' => __('Search for category'),
		] );

		echo '<span class="all_selected_categories_tooltip">'.wc_help_tip( esc_html__( 'Categories that the coupon will be applied to, or that need to be in the cart in order for the &quot;Fixed cart discount&quot; to be applied.', 'hexcoupon' ) ).'</span>';

		$output .= '</div>';
		echo wp_kses( $output, RenderHelpers::getInstance()->Wp_Kses_Allowed_For_Forms() );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method allowed_or_restricted_customer
	 * @return void
	 * @since 1.0.0
	 * Display meta fields for allowed or restricted customer.
	 */
	public function allowed_or_restricted_customer()
	{
		global $post;

		$allowed_or_restricted_customer_group = get_post_meta( $post->ID, 'allowed_or_restricted_customer_group', true );
		$allowed_or_restricted_customer_group = ! empty( $allowed_or_restricted_customer_group ) ? 'yes' : '';

		woocommerce_wp_checkbox(
			[
				'id' => 'allowed_or_restricted_customer_group',
				'label' => esc_html__( 'Allowed/Restricted customer', 'hexcoupon' ),
				'description' => esc_html__( 'Check this box to to add groups of Allowed/Restricted customers.', 'hexcoupon' ),
				'value' => $allowed_or_restricted_customer_group,
			]
		);

		$allowed_grp_of_customer = get_post_meta( $post->ID, 'allowed_group_of_customer', true );
		$allowed_grp_of_customer = ! empty( $allowed_grp_of_customer ) ? $allowed_grp_of_customer : '';

		echo '<div class="options_group allowed_group_of_customer">';

		woocommerce_wp_radio(
			[
				'id' => 'allowed_group_of_customer',
				'label' => '',
				'wrapper_class' => 'allowed_group_of_customer',
				'options' => [
					'allowed_for_groups' => esc_html__( 'Coupon allowed for below groups', 'hexcoupon' ),
					'restricted_for_groups' => esc_html__( 'Coupon restricted for below groups', 'hexcoupon' ),
				],
				'value' => $allowed_grp_of_customer,
			]
		);

		//		$output ='<div id="custom_coupon_tab" class="panel woocommerce_options_panel">';

		$selected_customer_group = get_post_meta( $post->ID, 'selected_customer_group', true );

		$output = FormHelpers::Init( [
			'label' => esc_html__( 'Customer Group', 'hexcoupon' ),
			'name' => 'selected_customer_group',
			'value' => $selected_customer_group,
			'type' => 'select',
			'options' => $this->get_user_role_names(), //if the field is select, this param will be here
			'multiple' => true,
			'select2' => true,
			'class' => 'selected_customer_group',
			'placeholder' => __('Search for customer group'),
		] );

		echo '<span class="selected_customer_group_tooltip">'.wc_help_tip( esc_html__( 'Groups that the coupon will be applied to, or that need to be in the cart in order for the &quot;Fixed cart discount&quot; to be applied.', 'hexcoupon' ) ).'</span>';

//		$output .= '</div>';
		echo wp_kses( $output, RenderHelpers::getInstance()->Wp_Kses_Allowed_For_Forms() );

		echo '</div>';

		$allowed_or_restricted_individual_customer = get_post_meta( $post->ID, 'allowed_or_restricted_individual_customer', true );
		$allowed_or_restricted_individual_customer = ! empty( $allowed_or_restricted_individual_customer ) ? 'yes' : '';

		woocommerce_wp_checkbox(
			[
				'id' => 'allowed_or_restricted_individual_customer',
				'label' => '',
				'description' => esc_html__( 'Check this box to to add individual of Allowed/Restricted customers.', 'hexcoupon' ),
				'value' => $allowed_or_restricted_individual_customer,
			]
		);

		$allowed_individual_customer = get_post_meta( $post->ID, 'allowed_individual_customer', true );
		$allowed_individual_customer = ! empty( $allowed_individual_customer ) ? $allowed_individual_customer : '';

		echo '<div class="options_group allowed_individual_customer">';

		woocommerce_wp_radio(
			[
				'id' => 'allowed_individual_customer',
				'wrapper_class' => 'allowed_individual_customer',
				'label' => '',
				'options' => [
					'allowed_for_customers' => esc_html__( 'Coupon allowed for below customers', 'hexcoupon' ),
					'restricted_for_customers' => esc_html__( 'Coupon restricted for below customers', 'hexcoupon' ),
				],
				'value' => $allowed_individual_customer,
			]
		);

		$selected_individual_customer = get_post_meta( $post->ID, 'selected_individual_customer', true );

		$output = FormHelpers::Init( [
			'label' => esc_html__( 'Individual Customer', 'hexcoupon' ),
			'name' => 'selected_individual_customer',
			'value' => $selected_individual_customer,
			'type' => 'select',
			'options' => $this->show_user_names(), //if the field is select, this param will be here
			'multiple' => true,
			'select2' => true,
			'class' => 'selected_individual_customer',
			'placeholder' => __('Search for customers'),
		] );

		echo '<span class="selected_individual_customer_tooltip">'.wc_help_tip( esc_html__( 'Individual customer that the coupon will be applied to, or that need to be in the cart in order for the &quot;Fixed cart discount&quot; to be applied.', 'hexcoupon' ) ).'</span>';

		echo wp_kses( $output, RenderHelpers::getInstance()->Wp_Kses_Allowed_For_Forms() );

		echo '</div>';
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method product_cart_condition_for_customer
	 * @return mixed
	 * @since 1.0.0
	 * Display all the meta fields in the coupon usage restriction tab.
	 */
	public function coupon_usage_restriction_meta_fields()
	{
		// product cart condition
		$this->product_cart_condition();

		// categories cart condition
		$this->category_cart_condition();

		// allowed or restricted customers
		$this->allowed_or_restricted_customer();

	}
}
