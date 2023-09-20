import React, { useEffect, useState } from 'react';
import Counter from '../../Global/Counter/Counter';
import axios from "axios";

const HexCouponPromo = () => {
	const [created, setCreated] = useState(0);
	const [active, setActive] = useState(0);
	const [expired, setExpired] = useState(0);
	const [redeemed, setRedeemed] = useState(0);
	const [redeemedAmount, setRedeemedAmount] = useState(0);
	const [sharableUrlPost, setSharableUrlPost] = useState(0);

	const {restApiUrl,nonce,ajaxUrl} = hexCuponData;

	useEffect(() => {

		axios
			.get(ajaxUrl, {
				params: {
					nonce: nonce,
					action: 'coupon_data',
				},
				headers: {
					'Content-Type': 'application/json',
				},
			})
			.then(({data}) => {
				setCreated(data.created)

				// Handle the response data
			})
			.catch((error) => {
				console.error('Error:', error);
			});


	}, []);


    const CounterItem = [
        {
            counterSingle: {start: 0, end: created, duration: 2.5, separator: ","},
            counterPara: "Coupons Created",
        },
        {
            counterSingle: {start: 0, end: redeemed, duration: 2.5, separator: ","},
            counterPara: "Coupons Redeemed",
        },
        {
            counterSingle: {start: 0, end: active, duration: 2.5, separator: ","},
            counterPara: "Coupons Active",
        },
        {
            counterSingle: {start: 0, end: expired, duration: 2.5, separator: ","},
            counterPara: "Coupons Expired",
        },
		{
			counterSingle: {start: 0, end: redeemedAmount, duration: 2.5, separator: ","},
			leftIcon: '$',
			counterPara: "Redeemed Coupon Value",
		},
		{
			counterSingle: {start: 0, end: sharableUrlPost, duration: 2.5, separator: ","},
			counterPara: "Sharable Url Coupons",
		},
    ];

    return (
        <>
            <div className="promo__wrapper mt-4">
                <div className="hex-grid-container column-xxl-4 column-lg-3 column-sm-2">
                    {CounterItem.map((item, i) => (
                        <div className="grid-item" key={i}>
                            <Counter
                                start={item.counterSingle.start}
                                end={item.counterSingle.end}
                                duration={item.counterSingle.duration}
                                separator={item.counterSingle.separator}
								leftIcon={item.leftIcon}
                                counterPara={item.counterPara}
                            />
                        </div>
                    ))}
                </div>
            </div>
        </>
    );
};

export default HexCouponPromo;
