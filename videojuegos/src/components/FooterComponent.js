import React from 'react';
import '../assets/styles/FooterComponent.css'

const FooterComponent = () => {
    const year = new Date().getFullYear();
    return (
        <footer>
            <p>Mariana Luján Mc Inerny - {year}</p>
        </footer>
    )
}

export default FooterComponent;
