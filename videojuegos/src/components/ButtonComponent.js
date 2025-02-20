import React from 'react';

const Button = ({ onClick, children, disabled = false, ...rest }) => {
    return (
        <button 
        {...rest}
        onClick={onClick} 
        disabled={disabled}>
            {children}
        </button>
    );
};

export default Button;
