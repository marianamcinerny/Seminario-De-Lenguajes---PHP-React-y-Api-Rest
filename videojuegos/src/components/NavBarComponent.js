import React, { useNavigate } from 'react-router-dom';
import '../assets/styles/NavBarComponent.css'
import { Link } from 'react-router-dom';
import Button from '../components/ButtonComponent';

const NavBarComponent = () => {
    const navigate = useNavigate();
    const admin = localStorage.getItem('admin');

    const handleLogout = () => {
        localStorage.clear();
        navigate('/');
        window.location.reload();
    };

    const username = localStorage.getItem('username');
    
    return (
        <nav>
            <ul id='ulNavBar'>
                <li>
                    <Link to="/">Juegos</Link>
                </li>

                {username ? (
                    <>
                        <li><Button onClick={handleLogout}>Hola, {username}! Cerrar sesión</Button></li>
                        {admin === '1' && (
                            <li>
                                <Link to="/nuevo-juego">Crear Juego</Link>
                            </li>
                        )}
                    </>
                ) : (
                    <>
                        <li>
                            <Link to="/login">Iniciar sesión</Link>
                        </li>
                        <li>
                            <Link to="/registro">Registrarse</Link>
                        </li>
                    </>
                )}
            </ul>
        </nav>
    );
}

export default NavBarComponent;