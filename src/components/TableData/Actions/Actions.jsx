
import React, { useState, useEffect, useRef } from 'react';

const Actions = ({ actionIcon, actionLinik }) => {
    const [showDropdown, setShowDropdown] = useState(false);
    const dropdownRef = useRef(null);
    const iconRef = useRef(null);

    const toggleDropdown = () => {
        setShowDropdown(!showDropdown);
    };

    const handleClickOutside = (event) => {
        if (
            dropdownRef.current &&
            !dropdownRef.current.contains(event.target) &&
            iconRef.current &&
            !iconRef.current.contains(event.target)
        ) {
            setShowDropdown(false);
        }
    };

    useEffect(() => {
        window.addEventListener('click', handleClickOutside);
        return () => {
            window.removeEventListener('click', handleClickOutside);
        };
    }, []);

    return (
        <div className={`actionWrapper ${showDropdown ? 'active' : ''}`}>
            <span ref={iconRef} className="actionWrapper__icon" onClick={toggleDropdown}>
                {actionIcon}
            </span>
            <div ref={dropdownRef} className={`actionWrapper__inner ${showDropdown ? 'show' : ''}`}>
                {actionLinik.map((item, index) => (
                    <a href="#0" key={index} className='actionWrapper__inner__link'>
                        <span className="actionWrapper__inner__link__icon">{item.LinkIcon}</span>
                        <span className="actionWrapper__inner__link__para">{item.LinkName}</span>
                    </a>
                ))}
            </div>
        </div>
    );
};

export default Actions;
