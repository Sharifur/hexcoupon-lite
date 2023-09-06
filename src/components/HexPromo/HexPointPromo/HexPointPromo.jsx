import React from 'react';
import Counter from '../../Global/Counter/Counter';

const HexPointPromo = () => {
    const CounterItem = [
        {
            counterSingle: {start: 0, end: 126, duration: 2.5, separator: ","},
            rightIcon: "k",
            counterPara: "Points Earned",
        },
        {
            counterSingle: {start: 0, end: 34, duration: 2.5, separator: ","},
            rightIcon: "k",
            counterPara: "Points Redeemed",
        },
        {
            counterSingle: {start: 0, end: 119, duration: 2.5, separator: ","},
            rightIcon: "k",
            counterPara: "Points Pending",
        },
        {
            counterSingle: {start: 0, end: 6900, duration: 2.5, separator: ","},
            rightIcon: "",
            counterPara: "Points Unclaimed",
        },
    ];

    return (
        <>            
            <div className="promo__wrapper mt-4">
                <div className="hex-grid-container column-xxl-4 column-lg-3 column-sm-2">   

                    {CounterItem.map((item, i) => (
                        <div className="grid-item" key={i}>
                            <Counter
                                start={item.counterSingle.start}
                                end={item.counterSingle.end}
                                duration={item.counterSingle.duration}
                                separator={item.counterSingle.separator}
                                rightIcon={item.rightIcon}
                                counterPara={item.counterPara}
                            />
                        </div>
                    ))}
                    
                </div>
            </div>
        </>
    );
};

export default HexPointPromo;