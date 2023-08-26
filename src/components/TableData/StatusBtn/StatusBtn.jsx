import React from 'react';

const StatusBtn = ({statusName, statusBtnClass, children}) => {
    return (
        <>
            <div className={statusBtnClass} >{statusName} { children }</div>
        </>
    );
};

export default StatusBtn;