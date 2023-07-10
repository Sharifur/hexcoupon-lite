<?php
namespace HexCoupon\App\Core\WooCommerce;

use HexCoupon\App\Core\Lib\SingleTon;


/**
 * will add more
 * @since  1.0.0
 * */
class CartPage
{
	use SingleTon;

	private $store_credit_amount = 10;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return void
	 * @since 1.0.0
	 * Add all hooks that is need for this page
	 */
	public function register()
	{
		// Action hook for displaying store credit title before to the cart page
		add_action( 'woocommerce_before_cart_table', [ $this, 'custom_cart_content' ] );
		// Action hook for displaying an extra column after the subtotal section text
		add_action( 'woocommerce_cart_totals_after_order_total', [ $this, 'add_store_credit_custom_column_after_order_total' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method my_custom_cart_content
	 * @return string
	 * @since 1.0.0
	 * Define the method to display the additional string or value
	 */
	public function custom_cart_content()
	{
		?>
		<h3><?php echo esc_html__( 'Get store credit via purchasing products.', 'hexcoupon' ) ; ?></h3>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_store_credit_custom_column_after_order_total
	 * @return string
	 * @since 1.0.0
	 * Render total store credit amounts of all products.
	 * */
	public function add_store_credit_custom_column_after_order_total()
	{
		$cart = WC()->cart;

		// Loop through each cart item
		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			// Retrieve the relevant data for the item
			$product_quantity = $cart_item['quantity'];

			// Calculate any additional values you want to display in the column
			$custom_column_value = $product_quantity * $this->store_credit_amount; // Example calculation

			// Output the column content
			echo '<tr><td colspan="2">Total Store Credit: ' . $custom_column_value . '</td></tr>';
		}
	}
}
