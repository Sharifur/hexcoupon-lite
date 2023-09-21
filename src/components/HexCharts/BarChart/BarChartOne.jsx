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

	const SelectOptions = [
		{ value: 'Year', label: translate_array.thisYearLabel },
		{ value: 'Month', label: translate_array.thisMonthLabel },
		{ value: 'Week', label: translate_array.thisWeekLabel },
		{ value: 'Yesterday', label: translate_array.yesterdayLabel },
		{ value: 'Today', label: translate_array.todayLabel },
	]

	let labels = getWeekList;
	let dataSet = {
		created: [1333, 821, 1983, 478, 2200,1333, 821, 1983, 478, 2200, 900, 1700],
		redeemed: [708, 1247, 975, 734, 1600,708, 1247, 975, 734, 1600, 250, 1300],
		active: [1708, 347, 1355, 304, 1200,1708, 347, 1355, 304, 1200, 700, 2300],
		expired: [1708, 847, 1355, 304, 1500,1708, 847, 1355, 304, 1500, 1100, 1900],
	};

	let dataSetForMonth = {
		created: [1333, 821, 1983, 478, 2200,1333, 821, 1983, 478, 2200,1333, 821, 1983, 478, 2200,1333, 821, 1983, 478, 2200, 900, 1700,2200,1333, 821, 1983, 478, 2200, 900, 1700],
		redeemed: [708, 1247, 975, 734, 1600,708, 1247, 975, 734, 1600,708, 1247, 975, 734, 1600,708, 1247, 975, 734, 1600,708, 1247, 975, 734, 1600,708, 1247, 975, 734, 1600],
		active: [1708, 347, 1355, 304, 1200,1708, 347, 1355, 304, 1200,1708, 347, 1355, 304, 1200,1708, 347, 1355, 304, 1200,1708, 347, 1355, 304, 1200,1708, 347, 1355, 304, 1200,],
		expired: [1708, 847, 1355, 304, 1500,1708, 847, 1355, 304, 1500,1708, 847, 1355, 304, 1500,1708, 847, 1355, 304, 1500,1708, 847, 1355, 304, 1500,1708, 847, 1355, 304, 1500,],
	}

	let dataSetForYesterday = {
		created: [1333],
		redeemed: [708],
		active: [1708],
		expired: [1708],
	}

	let dataSetForToday = {
		created: [1333],
		redeemed: [708],
		active: [1708],
		expired: [1708],
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
			setBarChartData(getDataForCharJS(getMonthList, dataSet));
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
