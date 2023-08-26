import React from 'react';

const ItemHeader = ({hexItemClass, hexHeaderTitle, hexBtnText, hexBtnIcon, hexBtnClass}) => {
    return (
        <>
            <div className={hexItemClass}>
                <div className="hexItem__header__flex">
                    <div className="hexItem__header__left">
                        <h4 className="hexItem__header__title">{hexHeaderTitle}</h4>
                    </div>
                    <div className="hexItem__header__right">
                        <div className="hexItem__header__right__flex">
                            <div className="hexItem__header__right__item">
                                <div className="btn_wrapper">
                                    <a href="#0" className={hexBtnClass}>{hexBtnText}</a>
                                </div>
                            </div>
                            <div className="hexItem__header__right__item">
                                <div className="hexItem__header__right__item__icon radius-5">
                                    {hexBtnIcon}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default ItemHeader;