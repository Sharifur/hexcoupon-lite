import React from 'react';
import HexGiftCard from '../../HexGiftCard/HexGiftCard'
import TableOne from '../../StoreCreditHistory/StoreCreditTable';
import ItemHeader from '../../Global/ItemHeader/ItemHeader';
import { MdOutlineSettings } from 'react-icons/md';
import imgGift from '../../HexGiftCard/img/gift.png';

const StoreCredit = () => {
    return (
        <>            

            <HexGiftCard giftPara='Zahid and 12 Customers has birthdays, anniversary today' giftSendLink='Send Them Gift Cards' imgGift={imgGift} />

            <ItemHeader hexItemClass='hexItem__header mt-4' hexHeaderTitle='Store Credit History' hexBtnClass='cmn_btn btn_bg_1' hexBtnText='Grant Credits' hexBtnIcon={<MdOutlineSettings /> } />

            <TableOne />
            
        </>
    );
};

export default StoreCredit;