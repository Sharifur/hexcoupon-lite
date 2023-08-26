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
        <div className="MainContainer">
            
            <HexGiftCard giftPara='Zahid and 12 Customers has birthdays, anniversary today' giftSendLink='Send Them Gift Cards' imgGift={imgGift} />

            <HexPointPromo />

            <LineChartOne />

            <HexCouponPromo />

            <BarChartOne />

            <HexPromo />

        </div>
    );
};

export default Dashboard;