import React from "react";
import { useI18n } from '@wordpress/react-i18n';
import dashIcon from "../icons/dash-icon.svg";
import storeCreditIcon from "../icons/storeCredit.svg";
import couponIcon from "../icons/coupon.svg";


export default function Sidebar(){
	const { __ } = useI18n();
	return (
		<aside className="sidebarWrapper">
			<div className="logoWrapper">
				<h6 className="logoText">[HexCoupon]</h6>
			</div>
			<ul>
				<li>
					<a href="#" className="active">
						<span className="iconWrap"><img src={dashIcon} alt={__("dash icon")}/></span>
						{__('Dashboard')}
					</a>
				</li>
				<li>
					<a href="#"><span className="iconWrap"><img src={storeCreditIcon} alt={__("store credit icon")}/></span>
						{__('Store Credit')}
					</a>
				</li>
				<li>
					<a href="#">
						<span className="iconWrap"><img src={couponIcon} alt={__("coupon icon")}/></span>
						{__('Coupon')}
					</a>
				</li>
			</ul>
		</aside>
	);
}
