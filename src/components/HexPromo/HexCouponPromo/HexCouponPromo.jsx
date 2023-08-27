import React from 'react';
import HexCouponPromoItem from './HexCouponPromoItem';

const HexCouponPromo = () => {
    return (
        <>
            <div className="promo__wrapper mt-4">
                <div className="hex-grid-container column-lg-4">
                    <HexCouponPromoItem />
                </div>
            </div>
        </>
    );
};

export default HexCouponPromo;