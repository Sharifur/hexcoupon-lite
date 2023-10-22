<?php if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="vite-react-sample"></div>
<?php
// Your WP_Query loop to query and display products
$args = array(
	'post_type' => 'shop_coupon',
	'posts_per_page' => -1,
);

$loop = new WP_Query($args);

while ($loop->have_posts()) : $loop->the_post();

	$title = get_the_title();

	// Display product information

	// Instantiate the WC_Coupon object with the coupon code
	$coupon_code = $title;
	$coupon = new WC_Coupon($coupon_code);

	// Check if the coupon exists
	if ($coupon->get_id() > 0) {
		// Retrieve coupon data
		$coupon_id = $coupon->get_id();

		$date = $coupon->get_date_expires();


		$new_date = ! empty( $date ) ? explode('T',$date) : '';

		print_r($new_date);

//		$all_expired_dates = [];
//
//		foreach( $new_date as $key => $value ) {
//			$all_expired_dates[] = $value;
//		}



		// You can access other coupon properties as needed

		// Display coupon data
//		echo $coupon_id . ' ';


	}

endwhile;

wp_reset_postdata();
//print_r($all_expired_dates);
