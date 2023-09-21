export function getDataForCharJS(labels, data){
	return {
		type: 'bar',
		labels: labels,
		backgroundColor: ['#A760FE', '#03AB67', '#4D77FF', '#98A2B3'],
		datasets: [
			{
				label: "Created",
				backgroundColor: '#A760FE',
				data: data.created,//[1333, 821, 1983, 478, 2200,1333, 821, 1983, 478, 2200, 900, 1700],
				barThickness: 10,
				hoverBackgroundColor: 'transparent',
				hoverBorderColor: '#A760FE',
				borderColor: '#A760FE',
				borderWidth: 1,
			}, {
				label: "Redeemed",
				backgroundColor: '#03AB67',
				data: data.redeemed,//[708, 1247, 975, 734, 1600,708, 1247, 975, 734, 1600, 250, 1300],
				barThickness: 10,
				hoverBackgroundColor: 'transparent',
				hoverBorderColor: '#03AB67',
				borderColor: '#03AB67',
				borderWidth: 1,
			}, {
				label: "Active",
				backgroundColor: '#4D77FF',
				data: data.active,//[1708, 347, 1355, 304, 1200,1708, 347, 1355, 304, 1200, 700, 2300],
				barThickness: 10,
				hoverBackgroundColor: 'transparent',
				hoverBorderColor: '#4D77FF',
				borderColor: '#4D77FF',
				borderWidth: 1,
			}, {
				label: "Expired",
				backgroundColor: '#98A2B3',
				data: data.expired,//[1708, 847, 1355, 304, 1500,1708, 847, 1355, 304, 1500, 1100, 1900],
				barThickness: 10,
				hoverBackgroundColor: 'transparent',
				hoverBorderColor: '#98A2B3',
				borderColor: '#98A2B3',
				borderWidth: 1,
			},
		],
	};
}


export const getWeekList = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

export const getMonthList = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',];

export const getDayList = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30'];

export const getSingleDayList = ['1'];
