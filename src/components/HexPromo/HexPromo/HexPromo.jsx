import React from 'react';
import Counter from '../../Global/Counter/Counter';

const HexPromo = () => {

    const CounterItem = [
        {
            counterSingle: {start: 0, end: 38010, duration: 2.5, separator: ","},
            leftIcon: '$',
            rightIcon: '',
            counterPara: "Redeemed Points Value",
        },
        {
            counterSingle: {start: 0, end: 49064, duration: 2.5, separator: ","},
            leftIcon: '$',
            rightIcon: '',
            counterPara: "Redeemed Coupon Value",
        },
        {
            counterSingle: {start: 0, end: 83, duration: 2.5, separator: ","},
            leftIcon: '',
            rightIcon: '',
            counterPara: "Gift Coupons",
        },
        {
            counterSingle: {start: 0, end: 30, duration: 2.5, separator: ","},
            leftIcon: '',
            rightIcon: 'K',
            counterPara: "Store Credits Refunded ",
        },
    ];

    return (
        <>            
            <div className="promo__wrapper mt-4">
                <div className="hex-grid-container column-xxl-4 column-lg-3 column-sm-2">   

                    {CounterItem.map((item, i) => (
                        <div className="grid-item" key={i}>
                            <Counter
                                start={item.counterSingle.start}
                                end={item.counterSingle.end}
                                duration={item.counterSingle.duration}
                                separator={item.counterSingle.separator}
                                leftIcon={item.leftIcon}
                                rightIcon={item.rightIcon}
                                counterPara={item.counterPara}
                            />
                        </div>
                    ))}
                    
                </div>
            </div>
        </>
    );
};

export default HexPromo;