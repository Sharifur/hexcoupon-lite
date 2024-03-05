import React, {useEffect, useState} from 'react';
import {IconFilePlus, IconGift, IconLink, IconMapPinCancel, IconTruck} from "@tabler/icons-react";

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
			<a href={finalUrl}><IconFilePlus />Add New Coupon</a>
			<a href={finalUrl+"#general_coupon_data_bogo"}><IconGift />Bogo Coupon</a>
			<a href={finalUrl+"#sharable_url_coupon_tab"} onClick="goToCouponTab('sharable_url_coupon_tab'); return false;"><IconLink />URL Coupon</a>
			<a href={finalUrl+"#geographic_restriction_tab"}><IconMapPinCancel />Geographic Restriction</a>
			<a href={finalUrl+"#custom_coupon_tab"}><IconTruck />Payment and Shipping</a>
		</div>
	)
}
export default Quick_Links;


