import React from 'react';
import Counter from '../../Global/Counter/Counter';

const HexCouponPromo = () => {
    const CounterItem = [
        {
            counterSingle: {start: 0, end: 1240, duration: 2.5, separator: ","},
            counterPara: "Coupons Created",
        },
        {
            counterSingle: {start: 0, end: 856, duration: 2.5, separator: ","},
            counterPara: "Coupons Redeemed",
        },
        {
            counterSingle: {start: 0, end: 319, duration: 2.5, separator: ","},
            counterPara: "Coupons Active",
        },
        {
            counterSingle: {start: 0, end: 65, duration: 2.5, separator: ","},
            counterPara: "Coupons Expired",
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
                                counterPara={item.counterPara}
                            />
                        </div>
                    ))}
                    
                </div>
            </div>
        </>
    );
};

export default HexCouponPromo;