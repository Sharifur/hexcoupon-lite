import React from 'react';
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

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
  );

const BarChartOne = () => {

    const SelectOptions = [
      { value: 'month', label: 'Last month' },
      { value: 'Year', label: 'Last Year' },
      { value: 'Week', label: 'Last Week' },
      { value: 'Yesterday', label: 'Yesterday' },
      { value: 'Today', label: 'Today' },
    ]

    const data = {
        type: 'bar',
        labels: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        backgroundColor: ['#A760FE', '#03AB67', '#4D77FF', '#98A2B3'],
        datasets: [                    
            {
                label: "Created",
                backgroundColor: '#A760FE',
                data: [1333, 821, 1983, 478, 2200, 900, 1700],
                barThickness: 10,
                hoverBackgroundColor: 'transparent',
                hoverBorderColor: '#A760FE',
                borderColor: '#A760FE',
                borderWidth: 1,
            }, {
                label: "Redeemed",
                backgroundColor: '#03AB67',
                data: [708, 1247, 975, 734, 1600, 250, 1300],
                barThickness: 10,
                hoverBackgroundColor: 'transparent',
                hoverBorderColor: '#03AB67',
                borderColor: '#03AB67',
                borderWidth: 1,
            }, {
                label: "Active",
                backgroundColor: '#4D77FF',
                data: [1708, 347, 1355, 304, 1200, 700, 2300],
                barThickness: 10,
                hoverBackgroundColor: 'transparent',
                hoverBorderColor: '#4D77FF',
                borderColor: '#4D77FF',
                borderWidth: 1,
            }, {
                label: "Expired",
                backgroundColor: '#98A2B3',
                data: [1708, 847, 1355, 304, 1500, 1100, 1900],
                barThickness: 10,
                hoverBackgroundColor: 'transparent',
                hoverBorderColor: '#98A2B3',
                borderColor: '#98A2B3',
                borderWidth: 1,
            },
        ],
    };

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

    return (
      <>
        <div className="hexDashboard__card mt-4 radius-10">
            <div className="hexDashboard__card__header">
                <div className="hexDashboard__card__header__flex">
                    <HexCardHeaderLeft>
                        <HexCardHeaderTitle titleHeading="Coupon Insights" />
                    </HexCardHeaderLeft>
                    <HexCardHeaderRight>
                        <SingleSelect options={SelectOptions}  />
                    </HexCardHeaderRight>
                </div>
            </div>  
            <div className="hexDashboard__card__inner mt-4">              
                <Bar data={data} options={options} />
            </div>
        </div>
      </>
    );
};

export default BarChartOne;