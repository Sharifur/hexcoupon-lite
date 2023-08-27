import React from 'react';
import HexPromoItem from './HexPromoItem';

const HexPromo = () => {
    return (
        <>
            <div className="promo__wrapper mt-4">
                <div className="hex-grid-container column-lg-4">
                    <HexPromoItem />
                </div>
            </div>
        </>
    );
};

export default HexPromo;