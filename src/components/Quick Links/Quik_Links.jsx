import React, {useEffect, useState} from 'react';
import { TbFilePlus,TbGift,TbLink,TbMapPinCancel,TbTruckDelivery } from "react-icons/tb";

const Quick_Links = () => {
	const [siteUrl, setSiteUrl] = useState('')
	useEffect(() => {
		setSiteUrl(window.location.href);

	}, []);

	const trimmedUrl = siteUrl.split('wp-admin/')[0]

	const finalUrl = trimmedUrl+'wp-admin/post-new.php?post_type=shop_coupon';

	return (
		<div className="hexcoupon_quick_links">
			<p>Quick Links:</p>
			<a href={finalUrl}><TbFilePlus size={24} />Add New Coupon</a>
			<a href={finalUrl+"#general_coupon_data_bogo"}><TbGift size={24}/>Bogo Coupon</a>
			<a href={finalUrl+"#sharable_url_coupon_tab"} onClick="goToCouponTab('sharable_url_coupon_tab'); return false;"><TbLink size={24} />URL Coupon</a>
			<a href={finalUrl+"#geographic_restriction_tab"}><TbMapPinCancel size={24} />Geographic Restriction</a>
			<a href={finalUrl+"#custom_coupon_tab"}><TbTruckDelivery size={24} />Payment and Shipping</a>
		</div>
	)
}
export default Quick_Links;


