import React from 'react';

const Button = (props) => {
    const {ButtonText, ButtonClass, isLink, to} = props;

    if (isLink) {
        return (
            <Link to={to} className={ButtonClass}>
                {ButtonText}
            </Link>
        );
    } else {
        return (
            <button className={ButtonClass}>
                {ButtonText}
            </button>
        );
    }

};

export default Button;