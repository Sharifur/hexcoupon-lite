<?php
namespace HexCoupon\App\Core\WooCommerce;

use HexCoupon\App\Core\Helpers\FormHelpers;
use HexCoupon\App\Core\Helpers\RenderHelpers;
use HexCoupon\App\Core\WooCommerce\CouponSingleUsageRestriction;
use HexCoupon\App\Core\Lib\SingleTon;

class CouponSingleGeneralTab
{
	use singleton;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method register
	 * @return void
	 * Registers all hooks that are needed.
	 */
	public function register()
	{
		add_action( 'woocommerce_coupon_options', [ $this, 'add_coupon_extra_fields' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method show_categories
	 * @return array
	 * Show all the categories of the product.
	 */
	private function show_categories()
	{
		$all_product_categories = [];

		$product_categories = get_categories(
			[
				'taxonomy' => 'product_cat',
				'orderby' => 'name',
			]
		);

		foreach ( $product_categories as $category ) {
			$all_product_categories[$category->term_id] = $category->name;
		}

		return $all_product_categories;
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method expiry_date_message_field
	 * @return void
	 * Add coupon expiry date message textarea field.
	 */
	private function expiry_date_message_field()
	{
		global $post;

		$discount_type = get_post_meta( $post->ID, 'discount_type', true );
		$discount_type = ! empty( $discount_type ) ? $discount_type : '';

		// Adding coupon type select input field
		woocommerce_wp_select( [
			'class' => 'select short',
			'label' => esc_html__( 'Coupon type', 'hex-coupon-for-woocommerce' ),
			'id' => 'coupon_type',
			'name' => 'discount_type',
			'options' => [
				'percent' => 'Percentage discount',
				'fixed_cart' => 'Fixed cart discount',
				'fixed_product' => 'Fixed product discount',
				'buy_x_get_x_bogo' => 'Buy X Get X Product (BOGO)',
			],
			'value' => $discount_type,
		] );

		$customer_purchases = get_post_meta( $post->ID, 'customer_purchases', true );
		$customer_purchases = ! empty( $customer_purchases ) ? $customer_purchases : '';

		// Adding customer purchases radio buttons field
		echo '<div class="options_group customer_purchases">';

		woocommerce_wp_radio(
			[
				'id' => 'customer_purchases',
				'label' => esc_html__( 'Customer purchases', 'hex-coupon-for-woocommerce' ),
				'options' => [
					'a_specific_product' => esc_html__( 'A specific product', 'hex-coupon-for-woocommerce' ),
					'a_combination_of_products' => esc_html__( 'A combination of products', 'hex-coupon-for-woocommerce' ),
					'product_categories' => esc_html__( 'Any product from categories', 'hex-coupon-for-woocommerce' ),
					'any_products_listed_below' => esc_html__( 'Any products listed below', 'hex-coupon-for-woocommerce' ),
				],
				'value' => ! empty( $customer_purchases ) ? $customer_purchases : 'a_specific_product',
			]
		);

		echo '</div>';

		// Adding a select2 field to add specific product
		$add_specific_product_to_purchase = get_post_meta( get_the_ID(),'add_specific_product_to_purchase',true );

		$output ='<div class="add_specific_product_to_purchase">';

		$output .= FormHelpers::Init( [
			'label' => esc_html__( 'Add a specific product', 'hex-coupon-for-woocommerce' ),
			'name' => 'add_specific_product_to_purchase',
			'value' => $add_specific_product_to_purchase,
			'type' => 'select',
			'options' => CouponSingleUsageRestriction::getInstance()->show_all_products(), //if the field is select, this param will be here
			'multiple' => true,
			'select2' => true,
			'class' => 'add_specific_product_to_purchase',
			'id' => 'add_specific_product_to_purchase',
			'placeholder' => __( 'Search for specific product', 'hex-coupon-for-woocommerce' )
		] );

		echo '<span class="add_specific_product_to_purchase_tooltip">'.wc_help_tip( esc_html__( 'Add the product that customer buys.', 'hex-coupon-for-woocommerce' ) ).'</span>';

		echo '<div id="selected_purchased_products">';
		if ( ! empty( $add_specific_product_to_purchase ) ) {
			foreach ( $add_specific_product_to_purchase as $value ) {
				$purchased_product_title = get_the_title( $value );

				$converted_purchased_product_title = strtolower( str_replace( ' ', '_', $purchased_product_title ) );

				$purchased_min_quantity = get_post_meta( $post->ID, $converted_purchased_product_title . '-purchased_min_quantity', true );

				echo '<div class="product-item-whole">';
				echo '<div class="product_title">'.$purchased_product_title.'</div>';
				?>
				<div class="product_min_max_main">
					<div class='product_min product-wrap'>
						<div class="product-wrap-inner">
							<p class="product-wrap-para"><?php echo esc_html__( 'Quantity', 'hex-coupon-for-woocommerce' ); ?></p>
							<input class="product-quantity-input" placeholder='Quantity' type='number' value="<?php echo esc_attr( $purchased_min_quantity ); ?>" name="<?php echo esc_attr( $converted_purchased_product_title );?>-purchased_min_quantity" min="0" max="100">
						</div>
						<a href="javascript:void(0)" class='dashicons dashicons-no-alt remove_purchased_product' data-title="<?php echo esc_attr( $purchased_product_title ); ?>" data-value="<?php echo esc_attr( $value ); ?>"></a>
					</div>
				</div>
				<?php
				echo '</div>';
			}
		}
		echo '</div>';

		echo wp_kses( $output, RenderHelpers::getInstance()->Wp_Kses_Allowed_For_Forms() );

		echo '</div>';


		// Adding a select2 field to add categories
		$add_categories_to_purchase = get_post_meta( get_the_ID(),'add_categories_to_purchase',true );

		$output ='<div class="add_categories_to_purchase">';

		$output .= FormHelpers::Init( [
			'label' => esc_html__( 'Add categories', 'hex-coupon-for-woocommerce' ),
			'name' => 'add_categories_to_purchase',
			'value' => $add_categories_to_purchase,
			'type' => 'select',
			'options' => $this->show_categories(), //if the field is select, this param will be here
			'multiple' => true,
			'select2' => true,
			'class' => 'add_categories_to_purchase',
			'id' => 'add_categories_to_purchase',
			'placeholder' => __( 'Search for categories', 'hex-coupon-for-woocommerce' )
		] );

		echo '<span class="add_categories_to_purchase_tooltip">'.wc_help_tip( esc_html__( 'Add categories that customer need to buy from.', 'hex-coupon-for-woocommerce' ) ).'</span>';

		echo '<div id="selected_purchased_categories">';

		if ( ! empty( $add_categories_to_purchase ) ) {
			foreach ( $add_categories_to_purchase as $value ) {
				$purchased_product_category_title = get_the_category_by_ID( $value );

				$converted_purchased_product_category_title = strtolower( str_replace( ' ', '_', $purchased_product_category_title ) );

				$category_purchased_min_quantity = get_post_meta( $post->ID, $converted_purchased_product_category_title . '-purchased_category_min_quantity', true );

				echo '<div class="product-item-whole">';
				echo '<div class="product_title">'.$purchased_product_category_title.'</div>';
				?>
				<div class="product_min_max_main">
					<div class='product_min product-wrap'>
						<div class="product-wrap-inner">
							<p class="product-wrap-para"><?php echo esc_html__( 'Quantity', 'hex-coupon-for-woocommerce' ); ?></p>
							<input class="product-quantity-input" placeholder='Quantity' type='number' value="<?php echo esc_attr( $category_purchased_min_quantity ); ?>" name="<?php echo esc_attr( $converted_purchased_product_category_title );?>-purchased_category_min_quantity" min="0" max="100">
						</div>
						<a href="javascript:void(0)" class='dashicons dashicons-no-alt remove_purchased_category' data-value="<?php echo esc_attr( $value ); ?>" data-title="<?php echo esc_attr( $purchased_product_category_title ); ?>"></a>
					</div>
				</div>
				<?php
				echo '</div>';
			}
		}

		echo '</div>';

		echo wp_kses( $output, RenderHelpers::getInstance()->Wp_Kses_Allowed_For_Forms() );

		echo '</div>';


		// Adding customer gets as free radio buttons
		$customer_gets_as_free = get_post_meta( $post->ID, 'customer_gets_as_free', true );
		$customer_gets_as_free = ! empty( $customer_gets_as_free ) ? $customer_gets_as_free : '';

		echo '<div class="options_group customer_gets_as_free">';

		woocommerce_wp_radio(
			[
				'id' => 'customer_gets_as_free',
				'label' => esc_html__( 'Customer gets as free', 'hex-coupon-for-woocommerce' ),
				'options' => [
					'a_specific_product' => esc_html__( 'A specific product', 'hex-coupon-for-woocommerce' ),
					'a_combination_of_products' => esc_html__( 'A combination of products', 'hex-coupon-for-woocommerce' ),
					'any_products_listed_below' => esc_html__( 'Any products listed below', 'hex-coupon-for-woocommerce' ),
					'same_product_as_free' => esc_html__( 'Same product as free', 'hex-coupon-for-woocommerce' ),
				],
				'value' => ! empty( $customer_gets_as_free ) ? $customer_gets_as_free : 'a_specific_product',
			]
		);

		echo '</div>';

		// Adding a select2 field to add a specific product
		$add_specific_product_for_free = get_post_meta( get_the_ID(),'add_specific_product_for_free',true );

		$output ='<div class="add_specific_product_for_free">';

		$output .= FormHelpers::Init( [
			'label' => esc_html__( 'Add a specific product', 'hex-coupon-for-woocommerce' ),
			'name' => 'add_specific_product_for_free',
			'value' => $add_specific_product_for_free,
			'type' => 'select',
			'options' => CouponSingleUsageRestriction::getInstance()->show_all_products(), //if the field is select, this param will be here
			'multiple' => true,
			'select2' => true,
			'class' => 'add_specific_product_for_free',
			'id' => 'add_specific_product_for_free',
			'placeholder' => __( 'Search for specific product', 'hex-coupon-for-woocommerce' )
		] );

		echo '<span class="add_specific_product_for_free_tooltip">'.wc_help_tip( esc_html__( 'Add the product that customer will get for free.', 'hex-coupon-for-woocommerce' ) ).'</span>';

		echo '<div id="selected_free_products">';
		if( ! empty( $add_specific_product_for_free ) && 'same_product_as_free' != $customer_gets_as_free ) {
			foreach ( $add_specific_product_for_free as $value ) {
				$free_product_title = get_the_title( $value );

				$converted_free_product_title = strtolower( str_replace( ' ', '_', $free_product_title ) );

				$free_product_quantity = get_post_meta( $post->ID, $converted_free_product_title . '-free_product_quantity', true );

				$free_product_amount = get_post_meta( $post->ID, $converted_free_product_title . '-free_amount', true );

				echo '<div class="product-item-whole">';
				echo '<div class="product_title">'.$free_product_title.'</div>';
				?>
				<div class="product_min_max_main">
					<div class='product_min product-wrap'>
						<div class="product-wrap-inner">
							<p class="product-wrap-para"><?php echo esc_html__( 'Quantity', 'hex-coupon-for-woocommerce' ); ?></p>
							<input class="product-quantity-input minimum" placeholder='Quantity' type='number' value="<?php echo esc_attr( $free_product_quantity ); ?>" name="<?php echo esc_attr( $converted_free_product_title );?>-free_product_quantity" min="0" max="100">
						</div>
					</div>
					<div class='product_min product-wrap'>
						<div class="product-wrap-inner">
							<p class="product-wrap-para"><?php echo esc_html__( 'Discount Type', 'hex-coupon-for-woocommerce' ); ?></p>
							<?php
							$saved_discount_type = get_post_meta($post->ID, $converted_free_product_title . '-hexcoupon_bogo_discount_type', true);

							// Default value if not set
							$saved_discount_type = $saved_discount_type ? $saved_discount_type : 'percent';
							?>
							<select name="<?php echo esc_attr( $converted_free_product_title );?>-hexcoupon_bogo_discount_type" id="hexcoupon_bogo_discount_type">
								<option value="percent" <?php if ( 'percent' === $saved_discount_type ) echo esc_attr( 'selected' );?>>Percent (%)</option>
								<option value="fixed" <?php if ( 'fixed' === $saved_discount_type ) echo esc_attr( 'selected' );?>>Fixed</option>
							</select>
						</div>
						<div class="product-wrap-inner">
							<p class="product-wrap-para"><?php echo esc_html__( 'Amount', 'hex-coupon-for-woocommerce' ); ?></p>
							<input class="product-quantity-input amount" placeholder='Amount' type='number' value="<?php echo esc_attr( $free_product_amount ); ?>" name="<?php echo esc_attr( $converted_free_product_title );?>-free_amount" min="0" max="100">
						</div>
						<a href="javascript:void(0)" class='dashicons dashicons-no-alt remove_free_product' data-title="<?php echo esc_attr( $free_product_title ); ?>" data-value="<?php echo esc_attr( $value ); ?>"></a>
					</div>
				</div>
				<?php
				echo '</div>';
			}
		}
		else {
			foreach ( $add_specific_product_to_purchase as $value ) {
				$free_product_title = get_the_title( $value );

				$converted_free_product_title = strtolower( str_replace( ' ', '_', $free_product_title ) );

				$free_product_quantity = get_post_meta( $post->ID, 'same_free_product_quantity', true );

				$free_product_amount = get_post_meta( $post->ID, 'same_free_amount', true );

				echo '<div class="product-item-whole">';
				echo '<div class="product_title"></div>';
				?>
				<div class="product_min_max_main">
					<div class='product_min product-wrap'>
						<div class="product-wrap-inner">
							<p class="product-wrap-para"><?php echo esc_html__( 'Quantity', 'hex-coupon-for-woocommerce' ); ?></p>
							<input class="product-quantity-input minimum" placeholder='Quantity' type='number' value="<?php echo esc_attr( $free_product_quantity ); ?>" name="same_free_product_quantity" min="0" max="100">
						</div>
					</div>
					<div class='product_min product-wrap'>
						<div class="product-wrap-inner">
							<p class="product-wrap-para"><?php echo esc_html__( 'Discount Type', 'hex-coupon-for-woocommerce' ); ?></p>
							<?php
							$saved_discount_type = get_post_meta($post->ID, 'hexcoupon_bogo_discount_type_on_same_product', true);

							// Default value if not set
							$saved_discount_type = $saved_discount_type ? $saved_discount_type : 'percent';
							?>
							<select name="hexcoupon_bogo_discount_type_on_same_product" id="hexcoupon_bogo_discount_type">
								<option value="percent" <?php if ( 'percent' === $saved_discount_type ) echo esc_attr( 'selected' );?>>Percent (%)</option>
								<option value="fixed" <?php if ( 'fixed' === $saved_discount_type ) echo esc_attr( 'selected' );?>>Fixed</option>
							</select>
						</div>
						<div class="product-wrap-inner">
							<p class="product-wrap-para"><?php echo esc_html__( 'Amount', 'hex-coupon-for-woocommerce' ); ?></p>
							<input class="product-quantity-input" placeholder='Amount' type='number' value="<?php echo esc_attr( $free_product_amount ); ?>" name="same_free_amount" min="0" max="100">
						</div>
					</div>
				</div>
				<?php
				echo '</div>';
			}
		}
		echo '</div>';

		echo wp_kses( $output, RenderHelpers::getInstance()->Wp_Kses_Allowed_For_Forms() );

		echo '</div>';

		woocommerce_wp_textarea_input(
			[
				'id' => 'message_for_coupon_expiry_date',
				'label' => '',
				'desc_tip' => true,
				'description' => esc_html__( 'Set a message for customers about the coupon expiry date.', 'hex-coupon-for-woocommerce' ),
				'placeholder' => esc_html__( 'Message for customer e.g. This coupon has been expired.', 'hex-coupon-for-woocommerce' ),
				'value' => get_post_meta( $post->ID, 'message_for_coupon_expiry_date', true ),
			]
		);
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method starting_date_field
	 * @return void
	 * Add coupon starting date input field.
	 */
	private function starting_date_field()
	{
		global $post;

		woocommerce_wp_text_input(
			[
				'id' => 'coupon_starting_date',
				'label' => esc_html__( 'Coupon starting date', 'hex-coupon-for-woocommerce' ),
				'desc_tip' => true,
				'description' => esc_html__( 'Set the coupon starting date.', 'hex-coupon-for-woocommerce' ),
				'type' => 'text',
				'value' => get_post_meta( $post->ID, 'coupon_starting_date', true ),
				'class' => 'date-picker',
				'placeholder' => esc_html( 'YYYY-MM-DD' ),
				'custom_attributes' => [
					'pattern' => apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ),
				],
			]
		);
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method starting_date_message_filed
	 * @return void
	 * Add coupon starting date input field.
	 */
	private function starting_date_message_filed()
	{
		global $post;

		woocommerce_wp_textarea_input(
			[
				'id' => 'message_for_coupon_starting_date',
				'label' => '',
				'desc_tip' => true,
				'description' => esc_html__( 'Set a message for customers about the coupon starting date.', 'hex-coupon-for-woocommerce' ),
				'placeholder' => esc_html__( 'Message for customer e.g. This coupon has not been started yet.', 'hex-coupon-for-woocommerce' ),
				'value' => get_post_meta( $post->ID, 'message_for_coupon_starting_date', true ),
			]
		);
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_coupon_apply_date_hours_checkbox
	 * @return void
	 * Add coupon day and hours applicable checkbox field.
	 */
	private function add_coupon_apply_date_hours_checkbox()
	{
		global $post;

		$checked = get_post_meta( $post->ID, 'apply_days_hours_of_week', true );
		$checked = ! empty( $checked ) ? 'yes' : '';

		woocommerce_wp_checkbox(
			[
				'id' => 'apply_days_hours_of_week',
				'label' => esc_html__( 'Valid for days/hours', 'hex-coupon-for-woocommerce' ),
				'description' => esc_html__( 'Check this box to make coupon valid for specific days and hours of the week.', 'hex-coupon-for-woocommerce' ),
				'value' => $checked,
			]
		);
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_coupon_apply_on_saturday_fields
	 * @return void
	 * Add coupon apply on saturday all fields.
	 */
	private function add_coupon_apply_on_saturday_fields()
	{
		global $post;

		$sat_coupon_start_time = get_post_meta( $post->ID, 'sat_coupon_start_time', true );
		$sat_coupon_expiry_time = get_post_meta( $post->ID, 'sat_coupon_expiry_time', true );
		?>
		<div class="options_group day_time_hours_block">
			<div class="options_group__saturdayWrap">
				<div class="options_group__saturdayWrap__inner">
					<div class="time-hours-label-checkbox">
						<p class="form-field form-day-field">
                            <span class="day-title">
                                <?php esc_html_e( 'Saturday', 'hex-coupon-for-woocommerce' ); ?>
                            </span>
						</p>
						<p class="form-field">
                            <span>
                                <label id="coupon_apply_on_saturday_label" for="coupon_apply_on_saturday" class="switch">
                                    <input type="checkbox" name="coupon_apply_on_saturday" id="coupon_apply_on_saturday" value="1" <?php checked( get_post_meta( $post->ID, 'coupon_apply_on_saturday', true ), 1 ); ?>>
                                    <span class="slider round"></span>
                                </label>
                            </span>
						</p>
					</div>

					<div class="time-hours-start-expiry">
						<p class="form-field saturday">
                            <span class="first-input">
                                <input type="text" class="time-picker-saturday coupon_start_time" name="sat_coupon_start_time" value="<?php echo esc_attr( $sat_coupon_start_time ); ?>" placeholder="HH:MM" /><span class="input_separator_saturday">-</span>
                                <input type="text" class="time-picker-saturday coupon_start_time" name="sat_coupon_expiry_time" value="<?php echo esc_attr( $sat_coupon_expiry_time ); ?>" placeholder="HH:MM" />

                                <input type="hidden" id="total_hours_count_saturday" name="total_hours_count_saturday" value="<?php $total_hours_count_saturday = intval( get_post_meta( $post->ID, 'total_hours_count_saturday', true ) ); echo esc_attr( $total_hours_count_saturday ); ?>">
                            </span>

							<?php
							for ( $i = 1; $i <= $total_hours_count_saturday; $i++ ) {
								$start_time = get_post_meta( $post->ID, 'sat_coupon_start_time_' . $i, true );
								$expiry_time = get_post_meta( $post->ID, 'sat_coupon_expiry_time_' . $i, true );
								$appendedElement = "<span class='appededItem first-input'>
                                                      <input type='text' class='time-picker-saturday coupon_start_time' name='sat_coupon_start_time_".$i."' value='".$start_time."' placeholder='HH:MM'>
                                                      <span class='input_separator_saturday'>-</span><input type='text' class='time-picker-saturday coupon_expiry_time' name='sat_coupon_expiry_time_".$i."' value='".$expiry_time."' placeholder='HH:MM'>
                                                      <a href='javascript:void(0)' class='dashicons dashicons-no-alt cross_hour_saturday cross-hour'></a>
                                                    </span>";
								echo $appendedElement;
							}
							?>
						</p>
						<p class="form-field add-more-hours-sat">
							<span class="add_more_hours_sat_pro_text"><?php echo esc_html__( 'To add more hours switch to Pro version', 'hex-coupon-for-woocommerce' ); ?></span>
							<a id="sat_add_more_hours" href="javascript:void(0)"><?php echo esc_html__( 'Add More Hours', 'hex-coupon-for-woocommerce' );?></a>
						</p>
					</div>

					<span id="sat_deactivated_text"><?php echo esc_html__( 'Coupon deactivated for Saturday', 'hex-coupon-for-woocommerce' ); ?></span>
				</div>
			</div>
			<span class="time-hours-border-bottom"></span>
		</div>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_coupon_apply_on_sunday_fields
	 * @return void
	 * Add coupon apply on sunday all fields.
	 */
	private function add_coupon_apply_on_sunday_fields()
	{
		global $post;

		$sun_coupon_start_time = get_post_meta( $post->ID, 'sun_coupon_start_time', true );
		$sun_coupon_expiry_time = get_post_meta( $post->ID, 'sun_coupon_expiry_time', true );
		?>
		<div class="options_group day_time_hours_block">
			<div class="options_group__saturdayWrap">
				<div class="options_group__saturdayWrap__inner">
					<div class="time-hours-label-checkbox">
						<p class="form-field form-day-field">
                            <span class="day-title">
                                <?php esc_html_e( 'Sunday', 'hex-coupon-for-woocommerce' ); ?>
                            </span>
						</p>
						<p class="form-field">
                        <span>
                           <label id="coupon_apply_on_sunday_label" for="coupon_apply_on_sunday" class="switch">
                                <input type="checkbox" name="coupon_apply_on_sunday" id="coupon_apply_on_sunday" value="1" <?php checked( get_post_meta( $post->ID, 'coupon_apply_on_sunday', true ), 1 ); ?>>
                                <span class="slider round"></span>
                            </label>
                        </span>
						</p>
					</div>
					<div class="time-hours-start-expiry">
						<p class="form-field sunday">
                            <span class="first-input">
                                <input type="text" class="time-picker-sunday coupon_start_time" name="sun_coupon_start_time" value="<?php echo esc_attr( $sun_coupon_start_time ); ?>" placeholder="HH:MM" /><span class="input_separator_sunday">-</span>
                                <input type="text" class="time-picker-sunday coupon_start_time" name="sun_coupon_expiry_time" value="<?php echo esc_attr( $sun_coupon_expiry_time ); ?>" placeholder="HH:MM" />

                                <input type="hidden" id="total_hours_count_sunday" name="total_hours_count_sunday" value="<?php $total_hours_count_sunday = intval( get_post_meta( $post->ID, 'total_hours_count_sunday', true ) ); echo esc_attr( $total_hours_count_sunday); ?>">
                            </span>

							<?php
							for ( $i = 1; $i <= $total_hours_count_sunday; $i++ ) {
								$start_time = get_post_meta( $post->ID, 'sun_coupon_start_time_' . $i, true );
								$expiry_time = get_post_meta( $post->ID, 'sun_coupon_expiry_time_' . $i, true );
								$appendedElement = "<span class='appededItem first-input'>
                                                      <input type='text' class='time-picker-sunday coupon_start_time' name='sun_coupon_start_time_".$i."' value='".$start_time."' placeholder='HH:MM'>
                                                      <span class='input_separator_sunday'>-</span><input type='text' class='time-picker-sunday coupon_start_time' name='sun_coupon_expiry_time_".$i."' value='".$expiry_time."' placeholder='HH:MM'>
                                                      <a href='javascript:void(0)' class='dashicons dashicons-no-alt cross_hour_sunday cross-hour'></a>
                                                    </span>";
								echo $appendedElement;
							}
							?>
						</p>
						<p class="form-field add-more-hours-sun">
							<span class="add_more_hours_sun_pro_text"><?php echo esc_html__( 'To add more hours switch to Pro version', 'hex-coupon-for-woocommerce' ); ?></span>
							<a id="sun_add_more_hours" href="javascript:void(0)"><?php echo esc_html__( 'Add More Hours', 'hex-coupon-for-woocommerce' );?></a>
						</p>
					</div>
					<span id="sun_deactivated_text"><?php echo esc_html__( 'Coupon deactivated for Sunday', 'hex-coupon-for-woocommerce' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_coupon_apply_on_monday_fields
	 * @return void
	 * Add coupon apply on monday all fields.
	 */
	private function add_coupon_apply_on_monday_fields()
	{
		global $post;

		$mon_coupon_start_time = get_post_meta( $post->ID, 'mon_coupon_start_time', true );
		$mon_coupon_expiry_time = get_post_meta( $post->ID, 'mon_coupon_expiry_time', true );
		?>
		<div class="options_group day_time_hours_block">
			<div class="options_group__saturdayWrap">
				<div class="options_group__saturdayWrap__inner">
					<div class="time-hours-label-checkbox">
						<p class="form-field form-day-field">
                            <span class="day-title">
                                <?php esc_html_e( 'Monday', 'hex-coupon-for-woocommerce' ); ?>
                            </span>
						</p>
						<p class="form-field">
                        <span>
                           <label id="coupon_apply_on_monday_label" for="coupon_apply_on_monday" class="switch">
                                <input type="checkbox" name="coupon_apply_on_monday" id="coupon_apply_on_monday" value="1" <?php checked( get_post_meta( $post->ID, 'coupon_apply_on_monday', true ), 1 ); ?>>
                                <span class="slider round"></span>
                            </label>
                        </span>
						</p>
					</div>
					<div class="time-hours-start-expiry">
						<p class="form-field monday">
                            <span class="first-input">
                                <input type="text" class="time-picker-monday coupon_start_time" name="mon_coupon_start_time" value="<?php echo esc_attr( $mon_coupon_start_time ); ?>" placeholder="HH:MM" /><span class="input_separator_monday">-</span>
                                <input type="text" class="time-picker-monday coupon_start_time" name="mon_coupon_expiry_time" value="<?php echo esc_attr( $mon_coupon_expiry_time ); ?>" placeholder="HH:MM" />

                                <input type="hidden" id="total_hours_count_monday" name="total_hours_count_monday" value="<?php $total_hours_count_monday = intval( get_post_meta( $post->ID, 'total_hours_count_monday', true ) ); echo esc_attr( $total_hours_count_monday ); ?>">
                            </span>

							<?php
							for ( $i = 1; $i <= $total_hours_count_monday; $i++ ) {
								$start_time = get_post_meta( $post->ID, 'mon_coupon_start_time_' . $i, true );
								$expiry_time = get_post_meta( $post->ID, 'mon_coupon_expiry_time_' . $i, true );
								$appendedElement = "<span class='appededItem first-input'>
                                                      <input type='text' class='time-picker-monday' name='mon_coupon_start_time_".$i."' id='coupon_start_time' value='".$start_time."' placeholder='HH:MM'>
                                                      <span class='input_separator_monday'>-</span><input type='text' class='time-picker-monday coupon_expiry_time' name='mon_coupon_expiry_time_".$i."' value='".$expiry_time."' placeholder='HH:MM'>
                                                      <a href='javascript:void(0)' class='dashicons dashicons-no-alt cross_hour_monday cross-hour'></a>
                                                    </span>";
								echo $appendedElement;
							}
							?>
						</p>
						<p class="form-field add-more-hours-mon">
							<span class="add_more_hours_mon_pro_text"><?php echo esc_html__( 'To add more hours switch to Pro version', 'hex-coupon-for-woocommerce' ); ?></span>
							<a id="mon_add_more_hours" href="javascript:void(0)"><?php echo esc_html__( 'Add More Hours', 'hex-coupon-for-woocommerce' );?></a>
						</p>
					</div>
					<span id="mon_deactivated_text"><?php echo esc_html__( 'Coupon deactivated for Monday', 'hex-coupon-for-woocommerce' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_coupon_apply_on_tuesday_fields
	 * @return void
	 * Add coupon apply on tuesday all fields.
	 */
	private function add_coupon_apply_on_tuesday_fields()
	{
		global $post;

		$tue_coupon_start_time = get_post_meta( $post->ID, 'tue_coupon_start_time', true );
		$tue_coupon_expiry_time = get_post_meta( $post->ID, 'tue_coupon_expiry_time', true );
		?>
		<div class="options_group day_time_hours_block">
			<div class="options_group__saturdayWrap">
				<div class="options_group__saturdayWrap__inner">
					<div class="time-hours-label-checkbox">
						<p class="form-field form-day-field">
                            <span class="day-title">
                                <?php esc_html_e( 'Tuesday', 'hex-coupon-for-woocommerce' ); ?>
                            </span>
						</p>
						<p class="form-field">
                        <span>
                           <label id="coupon_apply_on_tuesday_label" for="coupon_apply_on_tuesday" class="switch">
                                <input type="checkbox" name="coupon_apply_on_tuesday" id="coupon_apply_on_tuesday" value="1" <?php checked( get_post_meta( $post->ID, 'coupon_apply_on_tuesday', true ), 1 ); ?>>
                                <span class="slider round"></span>
                            </label>
                        </span>
						</p>
					</div>
					<div class="time-hours-start-expiry">
						<p class="form-field tuesday">
                           <span class="first-input">
                                <input type="text" class="time-picker-tuesday coupon_start_time" name="tue_coupon_start_time" value="<?php echo esc_attr( $tue_coupon_start_time ); ?>" placeholder="HH:MM" /><span class="input_separator_tuesday">-</span>
                                <input type="text" class="time-picker-tuesday coupon_start_time" name="tue_coupon_expiry_time" value="<?php echo esc_attr( $tue_coupon_expiry_time ); ?>" placeholder="HH:MM" />

                                <input type="hidden" id="total_hours_count_tuesday" name="total_hours_count_tuesday" value="<?php $total_hours_count_tuesday = intval( get_post_meta( $post->ID, 'total_hours_count_tuesday', true ) ); echo esc_attr( $total_hours_count_tuesday ); ?>">
                           </span>

							<?php
							for ( $i = 1; $i <= $total_hours_count_tuesday; $i++ ) {
								$start_time = get_post_meta( $post->ID, 'tue_coupon_start_time_' . $i, true );
								$expiry_time = get_post_meta( $post->ID, 'tue_coupon_expiry_time_' . $i, true );
								$appendedElement = "<span class='appededItem first-input'>
                                                      <input type='text' class='time-picker-tuesday' name='tue_coupon_start_time_".$i."' id='coupon_start_time' value='".$start_time."' placeholder='HH:MM'>
                                                      <span class='input_separator_tuesday'>-</span><input type='text' class='time-picker-tuesday coupon_expiry_time' name='tue_coupon_expiry_time_".$i."' value='".$expiry_time."' placeholder='HH:MM'>
                                                      <a href='javascript:void(0)' class='dashicons dashicons-no-alt cross_hour_tuesday cross-hour'></a>
                                                    </span>";
								echo $appendedElement;
							}
							?>
						</p>
						<p class="form-field add-more-hours-tue">
							<span class="add_more_hours_tue_pro_text"><?php echo esc_html__( 'To add more hours switch to Pro version', 'hex-coupon-for-woocommerce' ); ?></span>
							<a id="tue_add_more_hours" href="javascript:void(0)"><?php echo esc_html__( 'Add More Hours', 'hex-coupon-for-woocommerce' );?></a>
						</p>
					</div>
					<span id="tue_deactivated_text"><?php echo esc_html__( 'Coupon deactivated for Tuesday', 'hex-coupon-for-woocommerce' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_coupon_apply_on_wednesday_fields
	 * @return void
	 * Add coupon apply on wednesday all fields.
	 */
	private function add_coupon_apply_on_wednesday_fields()
	{
		global $post;

		$wed_coupon_start_time = get_post_meta( $post->ID, 'wed_coupon_start_time', true );
		$wed_coupon_expiry_time = get_post_meta( $post->ID, 'wed_coupon_expiry_time', true );
		?>
		<div class="options_group day_time_hours_block">
			<div class="options_group__saturdayWrap">
				<div class="options_group__saturdayWrap__inner">
					<div class="time-hours-label-checkbox">
						<p class="form-field form-day-field">
                            <span class="day-title">
                                <?php esc_html_e( 'Wednesday', 'hex-coupon-for-woocommerce' ); ?>
                            </span>
						</p>
						<p class="form-field">
                        <span>
                           <label id="coupon_apply_on_wednesday_label" for="coupon_apply_on_wednesday" class="switch">
                                <input type="checkbox" name="coupon_apply_on_wednesday" id="coupon_apply_on_wednesday" value="1" <?php checked( get_post_meta( $post->ID, 'coupon_apply_on_wednesday', true ), 1 ); ?>>
                                <span class="slider round"></span>
                            </label>
                        </span>
						</p>
					</div>
					<div class="time-hours-start-expiry">
						<p class="form-field wednesday">
                        <span class="first-input">
                            <input type="text" class="time-picker-wednesday coupon_start_time" name="wed_coupon_start_time" value="<?php echo esc_attr( $wed_coupon_start_time ); ?>" placeholder="HH:MM" /><span class="input_separator_wednesday">-</span>
                            <input type="text" class="time-picker-wednesday coupon_start_time" name="wed_coupon_expiry_time" value="<?php echo esc_attr( $wed_coupon_expiry_time ); ?>" placeholder="HH:MM" />

                            <input type="hidden" id="total_hours_count_wednesday" name="total_hours_count_wednesday" value="<?php $total_hours_count_wednesday = intval( get_post_meta( $post->ID, 'total_hours_count_wednesday', true ) ); echo esc_attr( $total_hours_count_wednesday ); ?>">
                        </span>

							<?php
							for ($i = 1; $i <= $total_hours_count_wednesday; $i++) {
								$start_time = get_post_meta( $post->ID, 'wed_coupon_start_time_' . $i, true );
								$expiry_time = get_post_meta( $post->ID, 'wed_coupon_expiry_time_' . $i, true );
								$appendedElement = "<span class='appededItem first-input'>
                                                      <input type='text' class='time-picker-wednesday' name='wed_coupon_start_time_".$i."' id='coupon_start_time' value='".$start_time."' placeholder='HH:MM'>
                                                      <span class='input_separator_wednesday'>-</span><input type='text' class='time-picker-wednesday coupon_expiry_time' name='wed_coupon_expiry_time_".$i."' value='".$expiry_time."' placeholder='HH:MM'>
                                                      <a href='javascript:void(0)' class='dashicons dashicons-no-alt cross_hour_wednesday cross-hour'></a>
                                                    </span>";
								echo $appendedElement;
							}
							?>
						</p>
						<p class="form-field add-more-hours-wed">
							<span class="add_more_hours_wed_pro_text"><?php echo esc_html__( 'To add more hours switch to Pro version', 'hex-coupon-for-woocommerce' ); ?></span>
							<a id="wed_add_more_hours" href="javascript:void(0)"><?php echo esc_html__( 'Add More Hours', 'hex-coupon-for-woocommerce' );?></a>
						</p>
					</div>
					<span id="wed_deactivated_text"><?php echo esc_html__( 'Coupon deactivated for Wednesday', 'hex-coupon-for-woocommerce' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_coupon_apply_on_thursday_fields
	 * @return void
	 * Add coupon apply on thursday all fields.
	 */
	private function add_coupon_apply_on_thursday_fields()
	{
		global $post;

		$thu_coupon_start_time = get_post_meta( $post->ID, 'thu_coupon_start_time', true );
		$thu_coupon_expiry_time = get_post_meta( $post->ID, 'thu_coupon_expiry_time', true );
		?>
		<div class="options_group day_time_hours_block">
			<div class="options_group__saturdayWrap">
				<div class="options_group__saturdayWrap__inner">
					<div class="time-hours-label-checkbox">
						<p class="form-field form-day-field">
                            <span class="day-title">
                                <?php esc_html_e( 'Thursday', 'hex-coupon-for-woocommerce' ); ?>
                            </span>
						</p>
						<p class="form-field">
                        <span>
                            <label id="coupon_apply_on_thursday_label" for="coupon_apply_on_thursday" class="switch">
                                <input type="checkbox" name="coupon_apply_on_thursday" id="coupon_apply_on_thursday" value="1" <?php checked( get_post_meta( $post->ID, 'coupon_apply_on_thursday', true ), 1 ); ?>>
                                <span class="slider round"></span>
                            </label>
                        </span>
						</p>
					</div>
					<div class="time-hours-start-expiry">
						<p class="form-field thursday">
                        <span class="first-input">
                                <input type="text" class="time-picker-thursday coupon_start_time" name="thu_coupon_start_time" value="<?php echo esc_attr( $thu_coupon_start_time ); ?>" placeholder="HH:MM" /><span class="input_separator_thursday">-</span>
                                <input type="text" class="time-picker-thursday coupon_start_time" name="thu_coupon_expiry_time" value="<?php echo esc_attr( $thu_coupon_expiry_time ); ?>" placeholder="HH:MM" />

                                <input type="hidden" id="total_hours_count_thursday" name="total_hours_count_thursday" value="<?php $total_hours_count_thursday = intval( get_post_meta( $post->ID, 'total_hours_count_thursday', true ) ); echo esc_attr( $total_hours_count_thursday ); ?>">
                        </span>

							<?php
							for ( $i = 1; $i <= $total_hours_count_thursday; $i++ ) {
								$start_time = get_post_meta( $post->ID, 'thu_coupon_start_time_' . $i, true );
								$expiry_time = get_post_meta( $post->ID, 'thu_coupon_expiry_time_' . $i, true );
								$appendedElement = "<span class='appededItem first-input'>
                                                      <input type='text' class='time-picker-thursday' name='thu_coupon_start_time_".$i."' id='coupon_start_time' value='".$start_time."' placeholder='HH:MM'>
                                                      <span class='input_separator_thursday'>-</span><input type='text' class='time-picker-thursday coupon_expiry_time' name='thu_coupon_expiry_time_".$i."' value='".$expiry_time."' placeholder='HH:MM'>
                                                      <a href='javascript:void(0)' class='dashicons dashicons-no-alt cross_hour_thursday cross-hour'></a>
                                                    </span>";
								echo $appendedElement;
							}
							?>
						</p>
						<p class="form-field add-more-hours-thu">
							<span class="add_more_hours_thu_pro_text"><?php echo esc_html__( 'To add more hours switch to Pro version', 'hex-coupon-for-woocommerce' ); ?></span>
							<a id="thu_add_more_hours" href="javascript:void(0)"><?php echo esc_html__( 'Add More Hours', 'hex-coupon-for-woocommerce' );?></a>
						</p>
					</div>
					<span id="thu_deactivated_text"><?php echo esc_html__( 'Coupon deactivated for Thursday', 'hex-coupon-for-woocommerce' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_coupon_apply_on_friday_fields
	 * @return void
	 * Add coupon apply on friday all fields.
	 */
	private function add_coupon_apply_on_friday_fields()
	{
		global $post;

		$fri_coupon_start_time = get_post_meta( $post->ID, 'fri_coupon_start_time', true );
		$fri_coupon_expiry_time = get_post_meta( $post->ID, 'fri_coupon_expiry_time', true );
		?>
		<div class="options_group day_time_hours_block">
			<div class="options_group__saturdayWrap">
				<div class="options_group__saturdayWrap__inner">
					<div class="time-hours-label-checkbox">
						<p class="form-field form-day-field">
                            <span class="day-title">
                                <?php esc_html_e( 'Friday', 'hex-coupon-for-woocommerce' ); ?>
                            </span>
						</p>
						<p class="form-field">
                        <span>
                            <label id="coupon_apply_on_friday_label" for="coupon_apply_on_friday" class="switch">
                                <input type="checkbox" name="coupon_apply_on_friday" id="coupon_apply_on_friday" value="1" <?php checked( get_post_meta( $post->ID, 'coupon_apply_on_friday', true ), 1 ); ?>>
                                <span class="slider round"></span>
                            </label>
                        </span>
						</p>
					</div>
					<div class="time-hours-start-expiry">
						<p class="form-field friday">
                        <span class="first-input">
                            <input type="text" class="time-picker-friday coupon_start_time" name="fri_coupon_start_time" value="<?php echo esc_attr( $fri_coupon_start_time ); ?>" placeholder="HH:MM" /><span class="input_separator_friday">-</span>
                            <input type="text" class="time-picker-friday coupon_start_time" name="fri_coupon_expiry_time" value="<?php echo esc_attr( $fri_coupon_expiry_time ); ?>" placeholder="HH:MM" />

                            <input type="hidden" id="total_hours_count_friday" name="total_hours_count_friday" value="<?php $total_hours_count_friday = intval( get_post_meta( $post->ID, 'total_hours_count_friday', true ) ); echo esc_attr( $total_hours_count_friday ); ?>">
                        </span>

							<?php
							for ( $i = 1; $i <= $total_hours_count_friday; $i++ ) {
								$start_time = get_post_meta( $post->ID, 'fri_coupon_start_time_' . $i, true );
								$expiry_time = get_post_meta( $post->ID, 'fri_coupon_expiry_time_' . $i, true );
								$appendedElement = "<span class='appededItem first-input'>
                                                  <input type='text' class='time-picker-friday' name='fri_coupon_start_time_".$i."' id='coupon_start_time' value='".$start_time."' placeholder='HH:MM'>
                                                  <span class='input_separator_friday'>-</span><input type='text' class='time-picker-friday coupon_expiry_time' name='fri_coupon_expiry_time_".$i."' value='".$expiry_time."' placeholder='HH:MM'>
                                                  <a href='javascript:void(0)' class='dashicons dashicons-no-alt cross_hour_friday cross-hour'></a>
                                                </span>";
								echo $appendedElement;
							}
							?>
						</p>
						<p class="form-field add-more-hours-fri">
							<span class="add_more_hours_fri_pro_text"><?php echo esc_html__( 'To add more hours switch to Pro version', 'hex-coupon-for-woocommerce' ); ?></span>
							<a id="fri_add_more_hours" href="javascript:void(0)"><?php echo esc_html__( 'Add More Hours', 'hex-coupon-for-woocommerce' );?></a>
						</p>
					</div>
					<span id="fri_deactivated_text"><?php echo esc_html__( 'Coupon deactivated for Friday', 'hex-coupon-for-woocommerce' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method add_coupon_extra_fields
	 * @return void
	 * Add coupon expiry date message textarea field.
	 */
	public function add_coupon_extra_fields()
	{
		// Textarea message field for coupon expiry date
		$this->expiry_date_message_field();

		// Coupon starting date input field
		$this->starting_date_field();

		// Textarea message field for coupon starting date
		$this->starting_date_message_filed();

		// Add coupon apply date and hours checkbox
		$this->add_coupon_apply_date_hours_checkbox();

		// Add apply on saturday fields
		$this->add_coupon_apply_on_saturday_fields();

		// Add apply on sunday fields
		$this->add_coupon_apply_on_sunday_fields();

		// Add apply on monday fields
		$this->add_coupon_apply_on_monday_fields();

		// Add apply on tuesday fields
		$this->add_coupon_apply_on_tuesday_fields();

		// Add apply on wednesday fields
		$this->add_coupon_apply_on_wednesday_fields();

		// Add apply on thursday fields
		$this->add_coupon_apply_on_thursday_fields();

		// Add apply on friday fields
		$this->add_coupon_apply_on_friday_fields();
	}
}
