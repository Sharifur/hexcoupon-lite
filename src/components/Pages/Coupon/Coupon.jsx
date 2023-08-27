import React from 'react';
import HexGiftCard from '../../HexGiftCard/HexGiftCard';
import imgGift from '../../HexGiftCard/img/gift2.png';
import CouponForm from '../../HexCoupon/CouponForm';

const Coupon = () => {
    const giftPara = 'Premium features boost sales by an impressive 30%';
    const giftPara2 = 'on our special 40% discount';
    const giftSendLink = 'Get Premium Now';

    return (
        <>            

            <HexGiftCard giftPara={giftPara} giftPara2={giftPara2} giftSendLink={giftSendLink} imgGift={imgGift} />

            <CouponForm />

        </>
    );
};

export default Coupon;