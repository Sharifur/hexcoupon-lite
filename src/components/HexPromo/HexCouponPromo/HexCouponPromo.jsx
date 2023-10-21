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
	const [bogoCoupon, setBogoCoupon] = useState(0);
	const [geographicRestriction, setGeographicRestriction] = useState(0);

	const {restApiUrl,nonce,ajaxUrl,translate_array} = hexCuponData;

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
				setRedeemedAmount(data.redeemedAmount)
				// Handle the response data
			})
			.catch((error) => {
				console.error('Error:', error);
			});

	}, []);

	useEffect(() => {
		axios
			.get(ajaxUrl, {
				params: {
					nonce: nonce,
					action: 'get_additional_data', // Use the new action name
				},
				headers: {
					'Content-Type': 'application/json',
				},
			})
			.then(({ data }) => {
				if (data) {
					// Handle the additional data here
					setActive(data.active)
					setExpired(data.expired)
					setRedeemed(data.redeemed)
					setSharableUrlPost(data.sharableUrlPost)
					setBogoCoupon(data.bogoCoupon)
					setGeographicRestriction(data.geographicRestriction)
				}
			})
			.catch((error) => {
				console.error('Error:', error);
			});
	}, []);



	const CounterItem = [
        {
            counterSingle: {start: 0, end: created, duration: 2.5, separator: ","},
            counterPara: translate_array.couponsCreatedLabel,
        },
        {
            counterSingle: {start: 0, end: redeemed, duration: 2.5, separator: ","},
            counterPara: translate_array.couponsRedeemedLabel,
        },
        {
            counterSingle: {start: 0, end: active, duration: 2.5, separator: ","},
            counterPara: translate_array.couponsActiveLabel,
        },
        {
            counterSingle: {start: 0, end: expired, duration: 2.5, separator: ","},
            counterPara: translate_array.couponsExpiredLabel,
        },
		{
			counterSingle: {start: 0, end: redeemedAmount, duration: 2.5, separator: ","},
			leftIcon: '$',
			isAllowedDecimal: true,
			counterPara: translate_array.redeemedCouponValueLabel,
		},
		{
			counterSingle: {start: 0, end: sharableUrlPost, duration: 2.5, separator: ","},
			counterPara: translate_array.sharableUrlCouponsLabel,
		},
		{
			counterSingle: {start: 0, end: bogoCoupon, duration: 2.5, separator: ","},
			counterPara: translate_array.bogoCouponlabel,
		},
		{
			counterSingle: {start: 0, end: geographicRestriction, duration: 2.5, separator: ","},
			counterPara: translate_array.geographicRestrictionLabel,
		},
    ];

    return (
        <>
            <div className="promo__wrapper">
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
								isAllowedDecimal={item.isAllowedDecimal ?? false}
                            />
                        </div>
                    ))}
                </div>
            </div>
        </>
    );
};

export default HexCouponPromo;
