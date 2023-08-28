import {React, useState} from 'react';

const GrantCouponItem = (props) => {
    const { grantLabelId, grantIconItem, grantSpanText, grantTitleText, grantInputId, grantCouponContent, setGrantCouponContent, isActive} = props;

    const handleRadioChange = (grantLabelId) => {
        changedArray = [];

        grantCouponContent.forEach((value, index) => {
            if(grantLabelId == value.labelId) {
                value.isActive = true;
            } else {
                value.isActive = false;
            }

            changedArray.push(value);
        });
        
        setGrantCouponContent(changedArray);
    };

    return (
        <>
            <label htmlFor={grantLabelId} className={`hexGrantCoupon radius-5 ${isActive ? 'active' : ''}`} onClick={() => handleRadioChange(grantLabelId)}>
                <div className="custom_radio hexGrantCoupon__radio">
                    <input className='hexGrantCoupon__input' type="radio" id={grantInputId} name='grantInput' checked={isActive} onChange={() => {}} />
                </div>
                <div className="hexGrantCoupon__icon">
                    {grantIconItem}
                </div>
                <div className="hexGrantCoupon__contents mt-2">
                    <span className="hexGrantCoupon__subtitle">{grantSpanText}</span>
                    <h4 className="hexGrantCoupon__title mt-3">{grantTitleText}</h4>
                </div>
            </label>
        </>
    );
};

export default GrantCouponItem;