import {React} from 'react';
import HexGiftCard from '../HexGiftCard/HexGiftCard';
import imgGift from '../../components/HexGiftCard/img/gift2.png';
import HexCardHeaderLeft from '../HexCardHeader/HexCardHeaderLeft';
import HexCardHeaderTitle from '../HexCardHeader/HexCardHeaderTitle';
import HexCardHeaderPara from '../HexCardHeader/HexCardHeaderPara';
import GrantCouponItem from './GrantCouponItem';
import { TbCashBanknote,TbCoins,TbShare3,TbTargetArrow,TbJewishStar,TbThumbUp,TbGift,TbMessage,TbCake } from 'react-icons/tb';
import { useEffect,useState } from 'react';

const GrantCoupon = () => {
    const giftPara = 'Premium features boost sales by an impressive 30%';
    const giftPara2 = 'on our special 40% discount';
    const giftSendLink = 'Get Premium Now';
    const HeaderPara = 'Choose the automated bulk coupon granting option from below';
    const titleHeading = 'Grant Coupons';

    const [grantCouponContent, setGrantCouponContent] = useState([
        { labelId: 0,isActive: false, spanText: 'Send Coupons for', titleText: 'Min. Cash Spending', grantIcon: <TbCashBanknote /> },
        { labelId: 1,isActive: false, spanText: 'Send Coupons for', titleText: 'Product Purchase', grantIcon: <TbCoins /> },
        { labelId: 2,isActive: false, spanText: 'Send Coupons for', titleText: 'Customer Referrals', grantIcon: <TbShare3 /> },
        { labelId: 3,isActive: false, spanText: 'Send Coupons for', titleText: 'Milestone Achievement', grantIcon: <TbTargetArrow /> },
        { labelId: 4,isActive: false, spanText: 'Send Coupons for', titleText: 'Product Reviews', grantIcon: <TbJewishStar /> },
        { labelId: 5,isActive: false, spanText: 'Send Coupons for', titleText: 'Social Media Engagement', grantIcon: <TbThumbUp /> },
        { labelId: 6,isActive: false, spanText: 'Send Coupons for', titleText: 'Welcome Bonus', grantIcon: <TbGift /> },
        { labelId: 7,isActive: false, spanText: 'Send Coupons for', titleText: 'Encourage Comeback', grantIcon: <TbThumbUp /> },
        { labelId: 8,isActive: false, spanText: 'Send Coupons for', titleText: 'Blog Post Comments', grantIcon: <TbMessage /> },
        { labelId: 9,isActive: false, spanText: 'Send Coupons for', titleText: 'Birthdays/Occasions ', grantIcon: <TbCake /> },
    ]);

    return (
        <>            
            <HexGiftCard giftPara={giftPara} giftPara2={giftPara2} giftSendLink={giftSendLink} imgGift={imgGift} />

            <div className="hexDashboard__card mt-4 radius-10">
                <div className="hexDashboard__card__header">
                    <HexCardHeaderLeft>
                        <HexCardHeaderTitle titleHeading={titleHeading} />
                        <HexCardHeaderPara HeaderPara={HeaderPara} />
                    </HexCardHeaderLeft>      
                </div>
                <div className="hexDashboard__card__inner mt-4">
                    <div className="hex-grid-container column-lg-3">
                        {grantCouponContent.map((item, i) => (
                            <div className="grid-item" key={i}>
                                <GrantCouponItem isActive={item.isActive} grantCouponContent={grantCouponContent} setGrantCouponContent={setGrantCouponContent} grantInputId={item.labelId} grantLabelId={item.labelId}  grantIconItem={item.grantIcon} grantSpanText={item.spanText} grantTitleText={item.titleText} />
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            
        </>
    );
};

export default GrantCoupon;