import React from 'react';
import Select from 'react-select';

const SingleSelect = (props) => {
    const {options, selectLabel} = props;

    return (
        <>
            <div className="single__select">
                <label className='single__input__label'>{selectLabel}</label>
                <Select options={options} />
            </div>
        </>
    );
};

export default SingleSelect;