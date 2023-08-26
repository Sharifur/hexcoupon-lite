import React from 'react';

const Amount = ({currentAmount, children}) => {
    return (
        <>
            <span className="amount_para">{currentAmount} {children}</span>
        </>
    );
};

export default Amount;