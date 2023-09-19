<div id="vite-react-sample"></div>
<?php

use CodesVault\Howdyqb\DB;

$result1 = DB::select('posts.post_status')
	->count('posts.ID', 'posts')
	->from('posts posts')
	->where('posts.post_type', '=', 'shop_coupon')
	->andWhere('posts.post_status', '=', 'trash')
	->get();

$result2 = DB::select('posts.post_status')
	->count('posts.ID', 'posts')
	->from('posts posts')
	->where('posts.post_type', '=', 'shop_coupon')
	->andWhere('posts.post_status', '=', 'publish')
	->get();

$final = $result1[0]['posts'] + $result2[0]['posts'];

// Get the current date in the format 'Y-m-d'
$current_time = time();

// Query the database to get all coupon posts and their expiration dates
$coupon_query = new WP_Query(array(
	'post_type' => 'shop_coupon', // WooCommerce coupon post type
	'posts_per_page' => -1, // Retrieve all coupons
));

// Initialize counters for active and expired coupons
$active_coupons = 0;
$expired_coupons = 0;

// Loop through the coupon posts
while ($coupon_query->have_posts()) {
	$coupon_query->the_post();

	// Get the coupon's expiration date from post meta
	$expiry_date = get_post_meta(get_the_ID(), 'expiry_date', true);

	// Check if the coupon has an expiry date
	if (!empty($expiry_date)) {
		// Compare the expiry date with the current date
		if (strtotime($expiry_date) >= $current_time) {
			// Coupon is active
			$active_coupons++;
		} else {
			// Coupon has expired
			$expired_coupons++;
		}
	}
}

// Reset post data
wp_reset_postdata();

?>

<div id="react-container" data-final="<?php echo esc_attr( $final ); ?>" data-active="<?php echo esc_attr( $active_coupons ); ?>></div>
