import React, { useState } from 'react';
import { useI18n } from '@wordpress/react-i18n';
import { Link } from 'react-router-dom';
import { MdHome, MdCreditCard, MdLocalOffer, MdEmojiEvents, MdCardGiftcard, MdSettings } from 'react-icons/md';
import LogoImg from '../../../img/logo.png';


const Sidebar = ({ searchParam }) => {
	const { __ } = useI18n();

    const [activeLink, setActiveLink] = useState('/');

    const handleLinkClick = (link) => {
        setActiveLink(link);
    };

    const sidebarLinks = [
        { path: '/', text: 'Dashboard', LinkIcon: MdHome },
        { path: '/store-credit', text: 'Store Credit', LinkIcon: MdCreditCard },
        { path: '/coupon', text: 'coupon', LinkIcon: MdLocalOffer },
        { path: '/loyalty-programme', text: 'Loyalty Programme', LinkIcon: MdEmojiEvents },
        { path: '/gift-cards', text: 'Gift Cards/Voucher', LinkIcon: MdCardGiftcard },
        { path: '/automations', text: 'Automations', LinkIcon: MdSettings },
    ];

    return (
        <aside className='hexpDashboard__left sidebarWrapper'>
            <div className="hexpDashboard__left__header">
                <div className="hexpDashboard__left__header__logo logoWrapper">
                    <Link to="/"><img src={LogoImg} alt="" /></Link>
                </div>
            </div>
            <ul className='hexpDashboard__list mt-4'>
                {sidebarLinks.map((link) => (
                    <li key={link.path} className='hexpDashboard__list__item'>
                        <Link to={link.path + searchParam } className={`hexpDashboard__list__item__link ${activeLink === link.path ? 'active' : ''}`} onClick={() => handleLinkClick(link.path)}>
                            <link.LinkIcon className='hexpDashboard__list__item__link__icon' />
                            {link.text}
                        </Link>
                    </li>
                ))}
            </ul>
        </aside>
    );
};

export default Sidebar;











// import React from "react";
// import { useI18n } from '@wordpress/react-i18n';
// import dashIcon from "../../icons/dash-icon.svg";
// import storeCreditIcon from "../../icons/storeCredit.svg";
// import couponIcon from "../../icons/coupon.svg";
// import { Link } from "react-router-dom";


// const Sidebar = () => {
// 	const { __ } = useI18n();
// 	return (
// 		<aside className="sidebarWrapper">
// 			<div className="logoWrapper">
// 				<h6 className="logoText">[HexCoupon]</h6>
// 			</div>
// 			<ul>
// 				<li>
// 					<Link to="/" className="active">
// 						<span className="iconWrap"><img src={dashIcon} alt={__("dash icon")}/></span>
// 						{__('Dashboard')}
// 					</Link>
// 				</li>
// 				<li>
// 					<Link to="store-credit"><span className="iconWrap"><img src={storeCreditIcon} alt={__("store credit icon")}/></span>
// 						{__('Store Credit')}
// 					</Link>
// 				</li>
// 				<li>
// 					<a href="#">
// 						<span className="iconWrap"><img src={couponIcon} alt={__("coupon icon")}/></span>
// 						{__('Coupon')}
// 					</a>
// 				</li>
// 			</ul>
// 		</aside>
// 	);
// }

// export default Sidebar;




