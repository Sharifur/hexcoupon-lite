import React from 'react';

const ItemHeaderMain = ({hexItemClass, children}) => {
    return (
        <>
        <div className={hexItemClass}>
            {children}
        </div>
        </>
    );
};

export default ItemHeaderMain;