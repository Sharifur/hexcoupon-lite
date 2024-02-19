import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import LogoImg from '../../../img/logo.png';
import {IconBook, IconHelpSquareRounded, IconHome, IconArrowGuide} from "@tabler/icons-react";
import { __ } from '@wordpress/i18n';

const Sidebar = () => {
    const [activeLink, setActiveLink] = useState('/');

    const handleLinkClick = (link) => {
        setActiveLink(link);
    };

    const sidebarLinks = [
        { path: '/', text: "Dashboard", LinkIcon: IconHome },
    ];

    return (
        <aside className='hexpDashboard__left sidebarWrapper radius-10'>
            <div className="hexpDashboard__left__header">
                <div className="hexpDashboard__left__header__logo logoWrapper">
                    <Link to="/"><img src={LogoImg} alt="" /></Link>
                </div>
            </div>
            <ul className='hexpDashboard__list mt-4'>
                {sidebarLinks.map((link) => (
                    <li key={link.path} className='hexpDashboard__list__item'>
                        <Link to={link.path} className={`hexpDashboard__list__item__link ${activeLink === link.path ? 'active' : ''}`} onClick={() => handleLinkClick(link.path)}>
                            <link.LinkIcon className='hexpDashboard__list__item__link__icon' />
                            {link.text}
                        </Link>
                    </li>
                ))}
            </ul>
			<div className="hexcoupon_resources">
				<p>Our Resources</p>
				<ul>
					<li><a href="https://hexcoupon.com/docs/" target="_blank"><IconBook />Documentation</a></li>
					<li><a href="https://hexcoupon.com/get-to-know-how-the-coupon-works/" target="_blank"><IconArrowGuide />Getting Started</a></li>
					<li><a href="https://wordpress.org/support/plugin/hex-coupon-for-woocommerce/" target="_blank"><IconHelpSquareRounded />Support</a></li>
				</ul>
			</div>
        </aside>
    );
};

export default Sidebar;


