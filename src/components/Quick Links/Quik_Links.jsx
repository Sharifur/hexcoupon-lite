import React, {useEffect, useState} from 'react';
import {
	IconFilePlus,
	IconGift,
	IconLink,
	IconMapPinCancel, IconRefresh,
	IconRotate,
	IconRotate2,
	IconTruck
} from "@tabler/icons-react";
import { __ } from '@wordpress/i18n';

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
			<a href={finalUrl} target="_blank"><IconFilePlus />{__("Add New Coupon","hex-coupon-for-woocommerce")}</a>
			<a href={finalUrl+"#general_coupon_data_bogo"} target="_blank"><IconGift />{__("Bogo Coupon","hex-coupon-for-woocommerce")}</a>
			<a href={finalUrl+"#sharable_url_coupon_tab"} onClick="goToCouponTab('sharable_url_coupon_tab'); return false;" target="_blank"><IconLink />{__("URL Coupon","hex-coupon-for-woocommerce")}</a>
			<a href={finalUrl+"#geographic_restriction_tab"} target="_blank"><IconMapPinCancel />{__("Geographic Restriction","hex-coupon-for-woocommerce")}</a>
			<a href={finalUrl+"#custom_coupon_tab"} target="_blank"><IconTruck />{__("Payment and Shipping","hex-coupon-for-woocommerce")}</a>
			<a href={finalUrl+"#reset_usage"} target="_blank"><IconRefresh />{__("Reset Usage","hex-coupon-for-woocommerce")}</a>
		</div>
	)
}
export default Quick_Links;


