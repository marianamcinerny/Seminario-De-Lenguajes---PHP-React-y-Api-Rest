import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import '../assets/styles/LoginRegister.css';
import Button from '../components/ButtonComponent';
import ModalMessage from '../components/ModalMessageComponent';
import config from '../config';

const LoginPage = () => {
    const [usuario, setUsuario] = useState("");
    const [clave, setClave] = useState("");
    const navigate = useNavigate();
    const [modalInfo, setModalInfo] = useState({
        isOpen: false,
        title: '',
        message: '',
        type: 'success',
    });


    const handleLogin = async(event) => {
        event.preventDefault();

        try {
            const response = await axios.post((`${config.apiUrl}login`), {
                nombre_usuario: usuario,
                clave: clave
            })

            setModalInfo({
                isOpen: true,
                title: response.data.message,
                message: 'Sera redirigido al inicio',
                type: 'success',
            });

            localStorage.setItem('token', response.data.token);
            localStorage.setItem('username', usuario);
            localStorage.setItem('admin', response.data.admin);

        } catch (error) {
            if (error.response) {
                setModalInfo({
                    isOpen: true,
                    title: 'Error',
                    message: error.response.data.message,
                    type: 'error',
                });
            } else {
                setModalInfo({
                    isOpen: true,
                    title: 'Error',
                    message: 'Hubo un error al iniciar sesion',
                    type: 'error',
                });
            }
        }
    };

    const handleCloseModal = () => {
        setModalInfo((prevState) => ({ ...prevState, isOpen: false }));
        if (modalInfo.type === 'success') {
            navigate('/');
        }
    };

    return (
        <div id='form-container'>
            <form id='form' onSubmit={handleLogin}>
                <div id='mensaje'>
                    <p>Iniciar sesion</p>
                </div>
                <div>
                    <label
                    >Nombre de usuario</label>
                    <input type="text" value={usuario} onChange={(e) => setUsuario(e.target.value)} 
                    required/>
                </div>
                <div>
                    <label>Clave</label>
                    <input type="password" value={clave} onChange={(e) => setClave(e.target.value)}
                    required/>
                </div>
                <Button type="submit">Ingresar</Button>
            </form>
            <ModalMessage
                isOpen={modalInfo.isOpen}
                onClose={handleCloseModal}
                title={modalInfo.title}
                message={modalInfo.message}
                type={modalInfo.type}
            />
        </div>
    )
}

export default LoginPage;
