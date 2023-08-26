import React from 'react';
import CountUp from 'react-countup';

const HexPointPromoItem = () => {
    const PointItem = [
        {
            promoCounter: {start: 0, end: 126, duration: 2.5, separator: ","},
            promoIcon: "k",
            promoPara: "Points Earned",
        },
        {
            promoCounter: {start: 0, end: 34, duration: 2.5, separator: ","},
            promoIcon: "k",
            promoPara: "Points Redeemed",
        },
        {
            promoCounter: {start: 0, end: 119, duration: 2.5, separator: ","},
            promoIcon: "k",
            promoPara: "Points Pending",
        },
        {
            promoCounter: {start: 0, end: 6900, duration: 2.5, separator: ","},
            promoIcon: "",
            promoPara: "Points Unclaimed",
        },
    ];
    
    return (
        <>
        {PointItem.map((item, i) => (
            <div className="grid-item" key={i}>
                <div className="hexpSingle__promo radius-10">
                    <h2 className="hexpSingle__promo__title">
                        <CountUp
                            start={item.promoCounter.start}
                            end={item.promoCounter.end}
                            duration={item.promoCounter.duration}
                            separator={item.promoCounter.separator}
                        />
                        <span>{item.promoIcon}</span>
                    </h2>
                    <p className="hexpSingle__promo__para mt-2">{item.promoPara}</p>
                </div>
            </div>
        ))}
            
        </>
    );
};

export default HexPointPromoItem;