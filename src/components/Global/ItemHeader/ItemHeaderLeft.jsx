import React from 'react';

const ItemHeaderLeft = ({hexHeaderTitle,children}) => {
    return (
        <>
            <div className="hexItem__header__left">
                <h4 className="hexItem__header__title">{hexHeaderTitle}</h4>
                {children}
            </div>
        </>
    );
};

export default ItemHeaderLeft;