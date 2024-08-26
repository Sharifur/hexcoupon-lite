<?php
namespace HexCoupon\App\Core\Helpers;

use HexCoupon\App\Core\Lib\SingleTon;

class GeneralFunctionsHelpers {

	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method show_all_products
	 * @return array
	 * Retrieve all available WoCommerce products.
	 */
	public function show_all_products()
	{
		$all_product_titles = []; // initialize an empty array

		$products = get_posts( [
			'post_type' => 'product',
			'numberposts' => -1,
		] );

		foreach ( $products as $product ) {
			$all_product_titles[$product->ID] = get_the_title( $product );
		}

		// if ( $all_product_titles === 0 ) {
		// 	$all_product_titles[0] === $all_product_titles[0];
		// }

		return $all_product_titles; // return all products id
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method show_all_categories
	 * @return array
	 * Retrieve all available WoCommerce product categories.
	 */
	public function show_all_categories()
	{
		$all_categories = []; // initialize an empty array

		$product_categories = get_terms( [
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
		] );

		if ( ! empty( $product_categories ) && ! is_wp_error( $product_categories ) ) {
			foreach ( $product_categories as $category ) {
				$cat_id = $category->term_id;
				$all_categories[ $cat_id ] = $category->name;
			}
		}

		// if ( $all_categories === 0 ) {
		// 	$all_categories[0] === $all_categories[0];
		// }

		return $all_categories; // return all categories id
	}
}