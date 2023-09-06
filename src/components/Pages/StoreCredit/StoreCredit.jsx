import React from 'react';
import HexGiftCard from '../../HexGiftCard/HexGiftCard'
import TableOne from '../../StoreCreditHistory/StoreCreditTable';
import { MdOutlineSettings } from 'react-icons/md';
import imgGift from '../../HexGiftCard/img/gift.png';
import ItemHeaderMain from '../../Global/ItemHeader/ItemHeaderMain';
import ItemHeaderLeft from '../../Global/ItemHeader/ItemHeaderLeft';
import ItemHeaderRight from '../../Global/ItemHeader/ItemHeaderRight';
import ItemHeaderRightItem from '../../Global/ItemHeader/ItemHeaderRightItem';
import CouponForm from '../../HexCoupon/CouponSettings';
import { Link } from 'react-router-dom';
import GrantCoupon from '../../GrantCoupon/GrantCoupon';


const StoreCredit = () => {

    const windowParams = window.location.search;

    return (
        <>
            <HexGiftCard giftPara='Zahid and 12 Customers has birthdays, anniversary today' giftSendLink='Send Them Gift Cards' imgGift={imgGift} />

            <ItemHeaderMain hexItemClass='hexItem__header'>
                <div className="hexItem__header__flex">
                    <ItemHeaderLeft hexHeaderTitle='Store Credit History' />
                    <ItemHeaderRight>
                        <ItemHeaderRightItem>
                            <Link to={'/grant-store-credit' + windowParams} element={<GrantCoupon />} className='cmn_btn btn_bg_1 radius-5'>
                                {ButtonText='Grant Credits'}
                            </Link>
                        </ItemHeaderRightItem>
                        <ItemHeaderRightItem>
                            <Link to={'/grant-store-credit' + windowParams} element={<CouponForm />} className='hexItem__header__right__item__icon radius-5'>
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

export default StoreCredit;