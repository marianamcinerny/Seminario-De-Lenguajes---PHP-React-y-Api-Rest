import React from 'react';
import '../assets/styles/HeaderComponent.css'
import logo from '../assets/images/logo.png'

const HeaderComponent = () => {
    return (
        <header>
            <img src={logo} alt=''/>
            <h1>RateMyGame</h1>
        </header>
    )
}

export default HeaderComponent;
