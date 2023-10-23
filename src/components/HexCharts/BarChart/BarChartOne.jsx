import React, {useEffect, useState} from 'react';
import {
	Chart as ChartJS,
	CategoryScale,
	LinearScale,
	BarElement,
	Title,
	Tooltip,
	Legend,
} from 'chart.js';
import { Bar } from 'react-chartjs-2';

import HexCardHeaderLeft from '../../HexCardHeader/HexCardHeaderLeft';
import HexCardHeaderTitle from '../../HexCardHeader/HexCardHeaderTitle';
import HexCardHeaderRight from '../../HexCardHeader/HexCardHeaderRight';
import SingleSelect from '../../Global/FormComponent/SingleSelect/SingleSelect';
import {getDataForCharJS, getSingleDayList, getWeekList} from "../../../helpers/helpers";
import axios from "axios";

ChartJS.register(
	CategoryScale,
	LinearScale,
	BarElement,
	Title,
	Tooltip,
	Legend
);

const BarChartOne = () => {
	const {restApiUrl,nonce,ajaxUrl,translate_array} = hexCuponData;

	const [todayCouponCreated, setTodayCouponCreated] = useState(0);
	const [todayCouponRedeemed, setTodayCouponRedeemed] = useState(0);
	const [todayActiveCoupons, setTodayActiveCoupons] = useState(0);
	const [todayExpiredCoupons, setTodayExpiredCoupons] = useState(0);

	const [yesterdayCouponCreated, setYesterdayCouponCreated] = useState(0);
	const [yesterdayRedeemedCoupon, setYesterdayRedeemedCoupon] = useState(0);
	const [yesterdayActiveCoupons, setYesterdayActiveCoupons] = useState(0);
	const [yesterdayExpiredCoupons, setYesterdayExpiredCoupons] = useState(0);

	const [weeklyCouponCreated, setWeeklyCouponCreated] = useState([]);
	const [weeklyActiveCoupon, setWeeklyActiveCoupon] = useState([]);
	const [weeklyExpiredCoupon, setWeeklyExpiredCoupon] = useState([]);
	const [weeklyCouponRedeemed, setWeeklyCouponRedeemed] = useState([]);

	useEffect(() => {
		axios
			.get(ajaxUrl, {
				params: {
					nonce: nonce,
					action: 'all_combined_data',
				},
				headers: {
					'Content-Type': 'application/json',
				},
			})
			.then(({data}) => {
				setTodayCouponCreated(data.todayCouponCreated)
				setTodayCouponRedeemed(data.todayRedeemedCoupon)
				setTodayActiveCoupons(data.todayActiveCoupons)
				setTodayExpiredCoupons(data.todayExpiredCoupons)
				setYesterdayCouponCreated(data.yesterdayCouponCreated)
				setYesterdayRedeemedCoupon(data.yesterdayRedeemedCoupon)
				setYesterdayActiveCoupons(data.yesterdayActiveCoupons)
				setYesterdayExpiredCoupons(data.yesterdayExpiredCoupons)
				setWeeklyCouponCreated(data.weeklyCouponCreated)
				setWeeklyCouponRedeemed(data.weeklyCouponRedeemed)
				setWeeklyActiveCoupon(data.weeklyActiveCoupon)
				setWeeklyExpiredCoupon(data.weeklyExpiredCoupon)
				// Handle the response data
			})
			.catch((error) => {
				console.error('Error:', error);
			});

	}, []);

	const SelectOptions = [
		{ value: 'Week', label: translate_array.thisWeekLabel },
		{ value: 'Yesterday', label: translate_array.yesterdayLabel },
		{ value: 'Today', label: translate_array.todayLabel },
	]

	let labels = getWeekList;

	let dataSet = {
		created: weeklyCouponCreated,
		redeemed: weeklyCouponRedeemed,
		active: weeklyActiveCoupon,
		expired: weeklyExpiredCoupon,
	};

	let dataSetForYesterday = {
		created: [yesterdayCouponCreated],
		redeemed: [yesterdayRedeemedCoupon],
		active: [yesterdayActiveCoupons],
		expired: [yesterdayExpiredCoupons],
	}

	let dataSetForToday = {
		created: [todayCouponCreated],
		redeemed: [todayCouponRedeemed],
		active: [todayActiveCoupons],
		expired: [todayExpiredCoupons],
	}

	const [barChartData, setBarChartData] = useState(getDataForCharJS(labels, dataSet));

	const options = {
		indexAxis: 'x',
		elements: {
			bar: {
				borderWidth: 2,
			},
		},
		responsive: true,
		plugins: {
			legend: {
				position: 'top',
			},
			title: {
				display: false,
				text: 'Bar Chart One',
			},
		},
		scales: {
			x: {
				display: true,
				beginAtZero: true,
				grid: {
					drawOnChartArea: false,
				},
			},
			y: {
				display: true,
				beginAtZero: true,
				grid: {
					drawOnChartArea: true,
				},
			},
		},
	};

	function handleChangeSelect(value){
		// call api for getting coupon according to selected value
		// todo:: do api request here

		// todo:: call this function inside success method of ajax request
		changeBarchartData(getWeekList,dataSet, value);
	}

	function changeBarchartData(getWeekList, dataSet, type){
		if(type === 'Week'){
			// now change this state value barchartLabel
			setBarChartData(getDataForCharJS(getWeekList, dataSet));
		}
		if(type === 'Yesterday'){
			// now change this state value barchartLabel
			setBarChartData(getDataForCharJS(getSingleDayList, dataSetForYesterday));
		}
		if(type === 'Today'){
			// now change this state value barchartLabel
			setBarChartData(getDataForCharJS(getSingleDayList, dataSetForToday));
		}
	}

	useEffect(() => {
		// todo:: call this function inside success method of ajax request
		changeBarchartData(getWeekList,{
			created: weeklyCouponCreated,
			redeemed: weeklyCouponRedeemed,
			active: weeklyActiveCoupon,
			expired: weeklyExpiredCoupon,
		}, 'Week');
	}, [weeklyExpiredCoupon])

	return (
		<>
			<div className="hexDashboard__card mt-4 radius-10">
				<div className="hexDashboard__card__header">
					<div className="hexDashboard__card__header__flex">
						<HexCardHeaderLeft>
							<HexCardHeaderTitle titleHeading={translate_array.couponInsightsLabel} />
						</HexCardHeaderLeft>
						<HexCardHeaderRight>
							<SingleSelect options={SelectOptions} handleChangeSelect={handleChangeSelect} />
						</HexCardHeaderRight>
					</div>
				</div>
				<div className="hexDashboard__card__inner mt-4">
					{barChartData && <Bar data={barChartData} options={options}/>}
				</div>
			</div>
		</>
	);
};

export default BarChartOne;
