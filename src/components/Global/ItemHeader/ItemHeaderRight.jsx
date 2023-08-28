import React from 'react';

const ItemHeaderRight = ({children}) => {
    return (
        <>            
            <div className="hexItem__header__right">
                <div className="hexItem__header__right__flex">
                    {children}
                    {/* <div className="hexItem__header__right__item">
                        <div className="btn_wrapper">
                            <a href="#0" className={hexBtnClass}>{hexBtnText}</a>
                        </div>
                    </div>
                    <div className="hexItem__header__right__item">
                        <div className="hexItem__header__right__item__icon radius-5">
                            {hexBtnIcon}
                        </div>
                    </div> */}
                </div>
            </div>
        </>
    );
};

export default ItemHeaderRight;