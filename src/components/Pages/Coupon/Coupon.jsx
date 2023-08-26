import React from 'react';
import HexGiftCard from '../../HexGiftCard/HexGiftCard';
import imgGift from '../../HexGiftCard/img/gift2.png';

const Coupon = () => {
    return (
        <>
            <div className="MainContainer">

            <HexGiftCard giftPara='Premium features boost sales by an impressive 30%' giftPara2='on our special 40% discount' giftSendLink='Get Premium Now' imgGift={imgGift} />


            </div>
        </>
    );
};

export default Coupon;