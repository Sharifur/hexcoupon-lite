<?php
namespace HexCoupon\App\Core\WooCommerce;

use HexCoupon\App\Core\Helpers\FormHelpers;
use HexCoupon\App\Core\Helpers\RenderHelpers;
use HexCoupon\App\Core\Lib\SingleTon;

class CouponSingleGeographicRestrictions {

	use SingleTon;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return mixed
	 * @since 1.0.0
	 * Registers all hooks that are needed to create custom tab 'Geographic Restrictions' on 'Coupon Single' page.
	 */
	public function register()
	{
		add_action( 'woocommerce_coupon_data_tabs', [ $this, 'add_geographic_restriction_tab_content' ] );
		add_filter( 'woocommerce_coupon_data_panels', [ $this, 'add_geographic_restriction_tab' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_custom_coupon_tab_content
	 * @return string
	 * @since 1.0.0
	 * Displays the new tab in the coupon single page called 'Geographic restrictions'.
	 */
	public function add_geographic_restriction_tab_content( $tabs )
	{
		$tabs['geographic_restriction_tab'] = array(
			'label'    => esc_html__( 'Geographic restrictions', 'hexcoupon' ),
			'target'   => 'geographic_restriction_tab',
			'class'    => array( 'geographic_restriction' ),
		);
		return $tabs;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method get_all_shipping_zones
	 * @return array
	 * @since 1.0.0
	 * Get the shipping zone name and code.
	 */
	private function get_all_shipping_zones() {
		$shipping_zones = [];
		$raw_zones = \WC_Shipping_Zones::get_zones();

		foreach ( $raw_zones as $raw_zone ) {
			$zone_locations = [];

			foreach ( $raw_zone['zone_locations'] as $zone_location ) {
				$zone_locations[] = [
					'type' => $zone_location->type,
					'code' => $zone_location->code,
//					'area_name' => $zone_location->city_name,
				];
			}



			$shipping_zones[ $zone_locations[0]['code'] ] = $raw_zone['zone_name'];
		}

		return $shipping_zones;
	}

	private function get_all_shipping_zones_copy() {
		$shipping_zones = [];
		$raw_zones = \WC_Shipping_Zones::get_zones();

		foreach ( $raw_zones as $raw_zone ) {
			$zone_name = $raw_zone['zone_name'];
			$city_names = []; // Initialize an array to store city names for the current zone

			foreach ( $raw_zone['zone_locations'] as $zone_location ) {
				if ( 'postcode' !== $zone_location->type ) {
					$city_names[] = $zone_location->code; // Add city name to the array
				}
			}

			if ( !empty($city_names) ) {
				foreach($city_names as $city_name) {
					$shipping_zones[ $city_name ] = $zone_name;
				}



			}
		}

		return $shipping_zones;
	}


	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method get_all_countries_name
	 * @return array
	 * @since 1.0.0
	 * Get all the countries names of WooCommerce.
	 */
	private function get_all_countries_name()
	{
		$countries_names = [];
		$all_countries = WC()->countries->get_countries();

		foreach ( $all_countries as $country_code => $country_name ) {
			$countries_names[ $country_code ] = $country_name;
		}

		return $countries_names;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_custom_coupon_tab
	 * @return string
	 * @since 1.0.0
	 * Displays the content of custom tab 'Geographic restrictions'.
	 */
	public function add_geographic_restriction_tab()
	{
		global $post;

		$apply_geographic_restriction = get_post_meta( $post->ID, 'apply_geographic_restriction', true );

		echo '<div id="geographic_restriction_tab" class="panel apply_geographic_restriction">';
		woocommerce_wp_radio(
			[
				'id' => 'apply_geographic_restriction',
				'label' => '',
				'options' => [
					'restrict_by_shipping_zones' => esc_html__( 'Restrict coupon based on shipping zones', 'hexcoupon' ),
					'restrict_by_countries' => esc_html__( 'Restrict coupon by specific countries', 'hexcoupon' ),
				],
				'value' => $apply_geographic_restriction,
			]
		);


		$restricted_shipping_zones = get_post_meta( get_the_ID(),'restricted_shipping_zones',true );

		$output ='<div class="restricted_shipping_zones">';

		$output .= FormHelpers::Init( [
			'label' => esc_html__( 'Add shipping zones', 'hexcoupon' ),
			'name' => 'restricted_shipping_zones',
			'value' => $restricted_shipping_zones,
			'type' => 'select',
			'options' => $this->get_all_shipping_zones_copy(), //if the field is select, this param will be here
			'multiple' => true,
			'select2' => true,
			'class' => 'restricted_shipping_zones',
			'placeholder' => __('Search for shipping zone')
		] );

		echo wp_kses( $output, RenderHelpers::getInstance()->Wp_Kses_Allowed_For_Forms() );

		echo '</div>';

		$restricted_countries = get_post_meta( get_the_ID(),'restricted_countries',true );

		$output ='<div class="restricted_countries">';

		$output .= FormHelpers::Init( [
			'label' => esc_html__( 'Add countries', 'hexcoupon' ),
			'name' => 'restricted_countries',
			'value' => $restricted_countries,
			'type' => 'select',
			'options' => $this->get_all_countries_name(), //if the field is select, this param will be here
			'multiple' => true,
			'select2' => true,
			'class' => 'restricted_countries',
			'placeholder' => __('Search for countries')
		] );

		echo wp_kses( $output, RenderHelpers::getInstance()->Wp_Kses_Allowed_For_Forms() );

		echo '</div>';
	}
}
