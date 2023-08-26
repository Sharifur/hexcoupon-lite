import React from 'react';
import HexPointPromoItem from './HexPointPromoItem';

const HexPointPromo = () => {
    return (
        <> 
            <div className="promo__wrapper mt-4">
                <div className="hex-grid-container">
                    <HexPointPromoItem />
                </div>
            </div>
        </>
    );
};

export default HexPointPromo;