import React from 'react';
import HexGiftCard from '../../HexGiftCard/HexGiftCard';
import imgGift from '../../HexGiftCard/img/gift2.png';
import TableOne from '../../StoreCreditHistory/StoreCreditTable';
import { MdOutlineSettings } from 'react-icons/md';
import ItemHeaderMain from '../../Global/ItemHeader/ItemHeaderMain';
import ItemHeaderLeft from '../../Global/ItemHeader/ItemHeaderLeft';
import ItemHeaderRight from '../../Global/ItemHeader/ItemHeaderRight';
import ItemHeaderRightItem from '../../Global/ItemHeader/ItemHeaderRightItem';
// import CouponForm from '../../HexCoupon/CouponSettings';
import { Link } from 'react-router-dom';
// import GrantCoupon from '../../GrantCoupon/GrantCoupon';



const Coupon = () => {
    const giftPara = 'Premium features boost sales by an impressive 30%';
    const giftPara2 = 'on our special 40% discount';
    const giftSendLink = 'Get Premium Now';

    const windowParams = window.location.search;

    return (
        <>
            <ItemHeaderMain hexItemClass='hexItem__header'>
                <div className="hexItem__header__flex">
                    <ItemHeaderLeft hexHeaderTitle='Coupon History' />

                    <ItemHeaderRight>
                        <ItemHeaderRightItem>
                            <Link to={'/grant-coupon' + windowParams} className='cmn_btn btn_bg_1  radius-5' >
                                {ButtonText='Grant Credits'}
                            </Link>

                        </ItemHeaderRightItem>

                        <ItemHeaderRightItem>
                            <Link to={'/coupon-settings' + windowParams} className='hexItem__header__right__item__icon  radius-5'>
                                <MdOutlineSettings />
                            </Link>
                        </ItemHeaderRightItem>
                    </ItemHeaderRight>
                </div>
            </ItemHeaderMain>

            <TableOne />

        </>
    );
};

export default Coupon;
