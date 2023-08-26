import React from 'react';
import HexCouponPromoItem from './HexCouponPromoItem';

const HexCouponPromo = () => {
    return (
        <>
            <div className="promo__wrapper mt-4">
                <div className="hex-grid-container">
                    <HexCouponPromoItem />
                </div>
            </div>
        </>
    );
};

export default HexCouponPromo;