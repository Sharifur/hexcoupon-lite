import React from 'react';
import CountUp from 'react-countup';

const HexCouponPromoItem = () => {
    const CouponsItem = [
        {
            couponsCounter: {start: 0, end: 1240, duration: 2.5, separator: ","},
            couponsPara: "Coupons Created",
        },
        {
            couponsCounter: {start: 0, end: 856, duration: 2.5, separator: ","},
            couponsPara: "Coupons Redeemed",
        },
        {
            couponsCounter: {start: 0, end: 319, duration: 2.5, separator: ","},
            couponsPara: "Coupons Active",
        },
        {
            couponsCounter: {start: 0, end: 65, duration: 2.5, separator: ","},
            couponsPara: "Coupons Expired",
        },
    ];
    
    return (
        <>
            {CouponsItem.map((item, i) => (
                <div className="grid-item" key={i}>
                    <div className="hexpSingle__promo radius-10">
                        <h2 className="hexpSingle__promo__title">
                            <CountUp
                                start={item.couponsCounter.start}
                                end={item.couponsCounter.end}
                                duration={item.couponsCounter.duration}
                                separator={item.couponsCounter.separator}
                            />
                        </h2>
                        <p className="hexpSingle__promo__para mt-2">{item.couponsPara}</p>
                    </div>
                </div>
            ))}
            
        </>
    );
};

export default HexCouponPromoItem;