import React from 'react';
import Select from 'react-select';

const HexChartSelect = () => {
    const SelectOptions = [
        { value: 'month', label: 'Last month' },
        { value: 'Year', label: 'Last Year' },
        { value: 'Week', label: 'Last Week' },
        { value: 'Yesterday', label: 'Yesterday' },
        { value: 'Today', label: 'Today' },
    ]
    return (
        <>
            <div className="hexDashboard__card__header__select">
                <Select options={SelectOptions} />
            </div>
        </>
    );
};

export default HexChartSelect;