import React, { useState } from 'react';
import { useI18n } from '@wordpress/react-i18n';
import { Link } from 'react-router-dom';
import { MdHome } from 'react-icons/md';
import LogoImg from '../../../img/logo.png';
const Sidebar = ({ searchParam }) => {
	const { __ } = useI18n();

    const [activeLink, setActiveLink] = useState('/');

    const handleLinkClick = (link) => {
        setActiveLink(link);
    };

    const sidebarLinks = [
        { path: '/', text: 'Dashboard', LinkIcon: MdHome },
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


