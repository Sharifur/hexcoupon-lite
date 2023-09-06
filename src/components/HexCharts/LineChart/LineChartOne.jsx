import React from 'react';
import {
  Chart as ChartJS,
  CategoryScale,  // x axis
  LinearScale, // y axis
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
} from 'chart.js';
import { Line } from 'react-chartjs-2';
// import HexCardHeader from '../../HexCardHeader/HexCardHeader';
import HexCardHeaderLeft from '../../HexCardHeader/HexCardHeaderLeft';
import HexCardHeaderTitle from '../../HexCardHeader/HexCardHeaderTitle';
import HexCardHeaderRight from '../../HexCardHeader/HexCardHeaderRight';
import SingleSelect from '../../Global/FormComponent/SingleSelect/SingleSelect';

ChartJS.register (
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
  Title,
  Tooltip,
  Legend,
)

const LineChartOne = () => {

  const SelectOptions = [
    { value: 'month', label: 'Last month' },
    { value: 'Year', label: 'Last Year' },
    { value: 'Week', label: 'Last Week' },
    { value: 'Yesterday', label: 'Yesterday' },
    { value: 'Today', label: 'Today' },
  ]
  
  const data = {    
    type: 'line',
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
    datasets: [
      {
        label: 'Earned',
        data: [1, 19, 25, 22, 12, 23, 15],
        fill: true,
        borderColor: '#A760FE',
        backgroundColor: '#A760FE',
        borderWidth: 2,
      },
      {
        label: 'Redeemed',
        data: [3, 12, 9, 28, 18, 13, 25],
        fill: true,
        borderColor: '#03AB67',
        backgroundColor: '#03AB67',
        borderWidth: 2,
      },
      {
        label: 'Unclaimed',
        data: [13, 22, 29, 18, 16, 10, 5],
        fill: true,
        borderColor: '#4D77FF',
        backgroundColor: '#4D77FF',
        borderWidth: 2,
      },
      {
        label: 'Expired',
        data: [7, 32, 19, 24, 8, 30, 11],
        fill: true,
        borderColor: '#98A2B3',
        backgroundColor: '#98A2B3',
        borderWidth: 2,
      },
    ],
  }
  const options = {    
		responsive: true,
		interaction: {
		  intersect: false,
		},
		stacked: false,
		plugins: {
		  legend: true,
		  title: {
			display: true,
			text: '',
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
  }

  return (
    <>
      <div className="hexDashboard__card mt-4 radius-10">
          <div className="hexDashboard__card__header">
              <div className="hexDashboard__card__header__flex">
                  <HexCardHeaderLeft>
                      <HexCardHeaderTitle titleHeading="Points Insights" />
                  </HexCardHeaderLeft>
                  <HexCardHeaderRight>
                      <SingleSelect options={SelectOptions} />
                  </HexCardHeaderRight>
              </div>
          </div>
          <div className="hexDashboard__card__inner mt-4">              
            <Line data={data} options={options} />
          </div>
      </div>
    </>
  )
};

export default LineChartOne;
