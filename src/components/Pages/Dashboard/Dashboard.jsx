import React from 'react';
import HexGiftCard from '../../HexGiftCard/HexGiftCard';
import LineChartOne from '../../HexCharts/LineChart/LineChartOne';
import HexPointPromo from '../../HexPromo/HexPointPromo/HexPointPromo';
import HexCouponPromo from '../../HexPromo/HexCouponPromo/HexCouponPromo';
import BarChartOne from '../../HexCharts/BarChart/BarChartOne';
import HexPromo from '../../HexPromo/HexPromo/HexPromo';
import imgGift from '../../HexGiftCard/img/gift.png';

const Dashboard = () => {
    return (
        <>
			<HexCouponPromo />
            <BarChartOne />
        </>
    );
};

export default Dashboard;
