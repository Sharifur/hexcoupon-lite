import React from 'react';
import HexChartSelect from './HexChartSelect/HexChartSelect';

const HexCardHeader = (props) => {
    const {titleHeading} = props;
    return (
        <>
            <div className="hexDashboard__card__header">
                <div className="hexDashboard__card__header__flex">
                    <div className="hexDashboard__card__header__left">
                        <h4 className="hexDashboard__card__header__title">{titleHeading}</h4>
                    </div>
                    <div className="hexDashboard__card__header__right">
                        <HexChartSelect />
                    </div>
                </div>
          </div>
        </>
    );
};

export default HexCardHeader;