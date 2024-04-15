import React, { useEffect, useState } from 'react';
import { useI18n } from '@wordpress/react-i18n';
import { Link, useLocation } from 'react-router-dom';
import { IconBook, IconHelpSquareRounded, IconHome } from "@tabler/icons-react";
import { TbMenu2, TbChevronDown, TbCoins } from "react-icons/tb";
import LogoImg from '../../../img/logo.png';
import { useSidebar } from '../../context/SidebarContext';

const Sidebar = () => {
	const { __ } = useI18n();

	const location = useLocation();
	const [activeLink, setActiveLink] = useState('/');

	useEffect(() => {
		setActiveLink(location.pathname);
	}, [location.pathname]);

	const handleLinkClick = (path) => {
		setActiveLink(path);
	};
	// toggle open class add remove
	const toggleOpenClass = (event) => {
		const currentItem = event.currentTarget;
		const siblings = currentItem.parentNode.children;
		for (let siblingItem of siblings) {
			if (siblingItem !== currentItem && siblingItem.classList.contains('has-children') && siblingItem.classList.contains('open')) {
				siblingItem.classList.remove('open');
			}
		}
		currentItem.classList.toggle('open');
	};
	const stopPropagation = (event) => {
		event.stopPropagation();
	};

	const storeCredit = ['/store-credit', '/store-credit/store-credit-settings', '/store-credit/store-credit-logs'];


	const { toggleSidebar, closeSidebar, isSidebarActive } = useSidebar();

	return (
		<>
			<div className={`sidebarOverlay ${isSidebarActive ? 'active' : ''}`} onClick={closeSidebar}></div>
			<div className="mobileIcon lg:hidden" onClick={toggleSidebar}><TbMenu2 /></div>

			<aside className={`hexpDashboard__left sidebarWrapper ${isSidebarActive ? 'active' : ''}`}>
				<div className="hexpDashboard__left__header">
					<div className="hexpDashboard__left__header__logo logoWrapper">
						<Link to="/"><img src={LogoImg} alt="" /></Link>
					</div>
				</div>
				<div className="hexpDashboard__left__inner">
					<ul className='hexpDashboard__list mt-4'>
						<li className='hexpDashboard__list__item'>
							<Link to="/" className={`hexpDashboard__list__item__link ${activeLink === '/' ? 'active' : ''}`} onClick={() => handleLinkClick('/')}>
								<span className='hexpDashboard__list__item__link__left'><IconHome />{__("Dashboard", "hex-coupon-for-woocommerce")}</span>
							</Link>
						</li>
						<li className={`hexpDashboard__list__item has-children ${storeCredit.includes(activeLink) ? 'active open' : ''}`} onClick={toggleOpenClass}>
							<span className={`hexpDashboard__list__item__link`}>
								<span className='hexpDashboard__list__item__link__left'><TbCoins />{__("Store Credit", "hex-coupon-for-wocommerce")}</span>
								<span className="arrowIcon"><TbChevronDown /></span>
							</span>
							<ul className="hexpDashboard__list submenu">
								<li className="hexpDashboard__list__item" onClick={stopPropagation}>
									<Link to="/store-credit/store-credit-settings" onClick={() => handleLinkClick('/store-credit/store-credit-settings')} className={`hexpDashboard__list__item__link ${activeLink === '/store-credit/store-credit-settings' ? 'active' : ''}`}>
										<span className="hexpDashboard__list__item__link__left">{__("Store Credit Settings", "hex-coupon-for-woocommerce")}</span>
									</Link>
								</li>
								<li className="hexpDashboard__list__item" onClick={stopPropagation}>
									<Link to="/store-credit/store-credit-logs" onClick={() => handleLinkClick('/store-credit/store-credit-logs')} className={`hexpDashboard__list__item__link ${activeLink === '/store-credit/store-credit-logs' ? 'active' : ''}`}>
										<span className="hexpDashboard__list__item__link__left">{__("Store Credit Logs", "hex-coupon-for-woocommerce")}</span>
									</Link>
								</li>
								<li className="hexpDashboard__list__item" onClick={stopPropagation}>
									<Link to="/store-credit/give-new-credit" onClick={() => handleLinkClick('/store-credit/give-new-credit')} className={`hexpDashboard__list__item__link ${activeLink === '/store-credit/give-new-credit' ? 'active' : ''}`}>
										<span className="hexpDashboard__list__item__link__left">{__("Give New Credit", "hex-coupon-for-woocommerce")}</span>
									</Link>
								</li>
							</ul>
						</li>
					</ul>
					<div className="hexcoupon_resources">
						<p className='hexcoupon_resources__title'>{__("Our Resources", "hex-coupon-for-woocommerce")}</p>
						<ul className='hexpDashboard__list'>
							<li className='hexpDashboard__list__item'>
								<a href="https://hexcoupon.com/docs/" target="_blank" className='hexpDashboard__list__item__link'><span className="hexpDashboard__list__item__link__left"><IconBook />{__("Documentation", "hex-coupon-for-woocommerce")}</span></a>
							</li>
							<li className='hexpDashboard__list__item'>
								<a href="https://wordpress.org/support/plugin/hex-coupon-for-woocommerce/" target="_blank" className='hexpDashboard__list__item__link'><span className="hexpDashboard__list__item__link__left"><IconHelpSquareRounded />{__("Support", "hex-coupon-for-woocommerce")}</span></a>
							</li>
						</ul>
					</div>
				</div>
			</aside>
		</>
	);
};

export default Sidebar;
