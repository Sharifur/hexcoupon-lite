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
// import HexCardHeader from '../../HexCardHeader/HexCardHeader';
import HexCardHeaderLeft from '../../HexCardHeader/HexCardHeaderLeft';
import HexCardHeaderTitle from '../../HexCardHeader/HexCardHeaderTitle';
import HexCardHeaderRight from '../../HexCardHeader/HexCardHeaderRight';
import SingleSelect from '../../Global/FormComponent/SingleSelect/SingleSelect';
import {getDataForCharJS, getDayList, getMonthList, getSingleDayList, getWeekList} from "../../../helpers/helpers";
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
	const [yesterdayCouponCreated, setYesterdayCouponCreated] = useState(0);
	const [weeklyCouponCreated, setWeeklyCouponCreated] = useState([]);
	const [monthlyCouponCountInYear, setMonthlyCouponCountInYear] = useState([]);
	const [dailyCouponCreatedInMonth, setDailyCouponCreatedInMonth] = useState([]);



	const [todayYesterdayCombinedData, setTodayYesterdayCombinedData] = useState({
		todayActiveCoupons : 0,
		todayExpiredCoupons : 0,
		yesterdayActiveCoupons : 0,
		yesterdayExpiredCoupons : 0,
	});

	useEffect(() => {

		axios
			.get(ajaxUrl, {
				params: {
					nonce: nonce,
					action: 'full_coupon_creation_data',
				},
				headers: {
					'Content-Type': 'application/json',
				},
			})
			.then(({data}) => {
				setTodayCouponCreated(data.todayCouponCreated)
				setYesterdayCouponCreated(data.yesterdayCouponCreated)
				setDailyCouponCreatedInMonth(data.dailyCouponCreatedInMonth)
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
					action: 'weekly_coupon_creation_data',
				},
				headers: {
					'Content-Type': 'application/json',
				},
			})
			.then(({data}) => {
				setWeeklyCouponCreated(data.weeklyCouponCreated)
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
					action: 'monthlyCouponCountInYear',
				},
				headers: {
					'Content-Type': 'application/json',
				},
			})
			.then(({data}) => {
				setMonthlyCouponCountInYear(data.monthlyCouponCountInYear)
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
					action: 'todayActiveExpiredCoupon',
				},
				headers: {
					'Content-Type': 'application/json',
				},
			})
			.then(({data}) => {


				setTodayYesterdayCombinedData({
					todayActiveCoupons: data.todayActiveCoupons,
					todayExpiredCoupons: data.todayExpiredCoupons,
					yesterdayActiveCoupons: data.yesterdayActiveCoupons,
					yesterdayExpiredCoupons: data.yesterdayExpiredCoupons,
				})
				// Handle the response data
			})
			.catch((error) => {
				console.error('Error:', error);
			});

	}, []);

	const { todayActiveCoupons, todayExpiredCoupons, yesterdayActiveCoupons, yesterdayExpiredCoupons } = todayYesterdayCombinedData;

	const SelectOptions = [
		{ value: 'Year', label: translate_array.thisYearLabel },
		{ value: 'Month', label: translate_array.thisMonthLabel },
		{ value: 'Week', label: translate_array.thisWeekLabel },
		{ value: 'Yesterday', label: translate_array.yesterdayLabel },
		{ value: 'Today', label: translate_array.todayLabel },
	]

	let labels = getWeekList;
	let dataSet = {
		created: weeklyCouponCreated,
		redeemed: [708, 1247, 975, 734, 1600,708, 1247, 975, 734, 1600, 250, 1300],
		active: [1708, 347, 1355, 304, 1200,1708, 347, 1355, 304, 1200, 700, 2300],
		expired: [1708, 847, 1355, 304, 1500,1708, 847, 1355, 304, 1500, 1100, 1900],
	};

	let dataSetForYear = {
		created: monthlyCouponCountInYear,
		redeemed: [708, 1247, 975, 734, 1600,708, 1247, 975, 734, 1600, 250, 1300],
		active: [1708, 347, 1355, 304, 1200,1708, 347, 1355, 304, 1200, 700, 2300],
		expired: [1708, 847, 1355, 304, 1500,1708, 847, 1355, 304, 1500, 1100, 1900],
	};

	let dataSetForMonth = {
		created: dailyCouponCreatedInMonth,
		redeemed: [708, 1247, 975, 734, 1600,708, 1247, 975, 734, 1600,708, 1247, 975, 734, 1600,708, 1247, 975, 734, 1600,708, 1247, 975, 734, 1600,708, 1247, 975, 734, 1600],
		active: [1708, 347, 1355, 304, 1200,1708, 347, 1355, 304, 1200,1708, 347, 1355, 304, 1200,1708, 347, 1355, 304, 1200,1708, 347, 1355, 304, 1200,1708, 347, 1355, 304, 1200,],
		expired: [1708, 847, 1355, 304, 1500,1708, 847, 1355, 304, 1500,1708, 847, 1355, 304, 1500,1708, 847, 1355, 304, 1500,1708, 847, 1355, 304, 1500,1708, 847, 1355, 304, 1500,],
	}

	let dataSetForYesterday = {
		created: [yesterdayCouponCreated],
		redeemed: [708],
		active: [yesterdayActiveCoupons],
		expired: [yesterdayExpiredCoupons],
	}

	let dataSetForToday = {
		created: [todayCouponCreated],
		redeemed: [708],
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
		changeBarchartData(getMonthList,dataSet, value);
	}

	function changeBarchartData(getMonthList, dataSet, type){
		// now check value is monthly
		if(type === 'Year'){
			// now change this state value barchartLabel
			setBarChartData(getDataForCharJS(getMonthList, dataSetForYear));
		}
		if(type === 'Week'){
			// now change this state value barchartLabel
			setBarChartData(getDataForCharJS(getWeekList, dataSet));
		}
		if(type === 'Month'){
			// now change this state value barchartLabel
			setBarChartData(getDataForCharJS(getDayList, dataSetForMonth));
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

	// useEffect(function (){
	//
	// },[barchartLabel])

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
