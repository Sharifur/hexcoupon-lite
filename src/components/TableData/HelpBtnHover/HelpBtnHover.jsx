import React, { useState, useRef } from 'react';

const HelpBtnHover = ({ statusHelpBtn, statusHelpPara }) => {
    const [isHovered, setIsHovered] = useState(false);
    const contentsRef = useRef(null);
    const iconRef = useRef(null);

    const handleMouseEnter = () => {
        setIsHovered(true);
        calculatePosition();
    };

    const handleMouseLeave = () => {
        setIsHovered(false);
    };

    const calculatePosition = () => {
        if (iconRef.current && contentsRef.current) {
            const iconRect = iconRef.current.getBoundingClientRect();
            contentsRef.current.style.left = `${iconRect.left}px`;
            contentsRef.current.style.top = `${iconRect.bottom}px`;
        }
    };

    return (
        <div className="HelpBtnHover">
            <span className="HelpBtnHover__icon" onMouseEnter={handleMouseEnter} onMouseLeave={handleMouseLeave} ref={iconRef} >
                {statusHelpBtn}
            </span>
            {isHovered && (
                <div className="HelpBtnHover__contents" onMouseEnter={handleMouseEnter} onMouseLeave={handleMouseLeave} ref={contentsRef} >
                    <p className="HelpBtnHover__para">{statusHelpPara}</p>
                </div>
            )}
        </div>
    );
};

export default HelpBtnHover;
