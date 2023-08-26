import { React , useState } from 'react';
import { MdClose } from 'react-icons/md';



const HexGiftCard = ({imgGift, giftPara, giftPara2, giftSendLink}) => {
    const [giftCardVisible, setGiftCardVisible] = useState(true);

    const handleCloseClick = () => {
        setGiftCardVisible(false);
    };

    return (
        <>
            {giftCardVisible && (
                <div className="giftCard__wrapper radius-5">
                    <div className="giftCard__wrapper__giftThumb">
                        <img src={imgGift} alt="gift" />
                    </div>
                    <div className="giftCard__wrapper__flex">
                        <div className="giftCard__wrapper__left">
                            <div className="giftCard__wrapper__left__flex">                            
                                <div className="giftCard__wrapper__left__contents">
                                    <p className='giftCard__wrapper__left__para'>{giftPara} <a href="#0">{giftSendLink}</a> {giftPara2}</p>
                                </div>
                            </div>
                        </div>
                        <div className="giftCard__wrapper__right">
                            <div className="giftCard__wrapper__close" onClick={handleCloseClick}>                                
                                <MdClose />
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};

export default HexGiftCard;
