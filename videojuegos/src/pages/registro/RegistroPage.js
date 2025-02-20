import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import '../../assets/styles/LoginRegister.css';
import Button from '../../components/ButtonComponent';
import ModalMessage from '../../components/ModalMessageComponent';
import config from '../../config';

const RegistroPage = () => {
    const [usuario, setUsuario] = useState('');
    const [clave, setClave] = useState('');
    let hayError = false;
    const navigate = useNavigate();
    const [modalInfo, setModalInfo] = useState({
        isOpen: false,
        title: '',
        message: '',
        type: 'success',
    });

    const validateUsername = (username) => {
        if (username.length < 6 || username.length > 20) {
            setModalInfo({
                isOpen: true,
                title: 'Error',
                message: 'El nombre de usuario debe tener entre 6 y 20 caracteres',
                type: 'error',
            });
            hayError = true;
        }

        if (!/^[a-zA-Z0-9]+$/.test(username)) {
            setModalInfo({
                isOpen: true,
                title: 'Error',
                message: 'El nombre de usuario debe ser alfanumerico',
                type: 'error',
            });
            hayError = true;
        }
    };

    const validatePassword = (password) => {
        if (password.length < 8 || password.length > 16) {
            setModalInfo({
                isOpen: true,
                title: 'Error',
                message: 'La contraseña debe tener entre 8 y 16 caracteres',
                type: 'error',
            });
            hayError = true;
        }

        if (!/[A-Z]/.test(password) || !/[a-z]/.test(password) || !/[0-9]/.test(password) || !/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            setModalInfo({
                isOpen: true,
                title: 'Error',
                message: 'La constraseña debe tener mayúsculas, minúsculas, números y caracteres especiales',
                type: 'error',
            });
            hayError = true;
        }
    };

    const handleSubmit = async (e) => {
        hayError = false;
        e.preventDefault();

        validateUsername(usuario);
        validatePassword(clave);

        if (!hayError) {
            try {        
                const response = await axios.post((`${config.apiUrl}register`), {
                    nombre_usuario: usuario,
                    clave: clave,
                });

                setModalInfo({
                    isOpen: true,
                    title: response.data.message,
                    message: 'Sera redirigido a la pagina de inicio de sesion',
                    type: 'success',
                });
    
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
                        message: 'Hubo un error al registrar el usuario',
                        type: 'error',
                    });
                }
            }
        }
    };

    const handleCloseModal = () => {
        setModalInfo((prevState) => ({ ...prevState, isOpen: false }));
        if (modalInfo.type === 'success') {
            navigate('/login');
        }
    };

    return (
        <div id='form-container'>
            <form id='form' onSubmit={handleSubmit}>
                <div id='mensaje'>
                    <p>Registrar usuario</p>
                </div>
                <div>
                <label>Nombre de usuario</label>
                <input
                    type="text"
                    value={usuario}
                    onChange={(e) => setUsuario(e.target.value)}
                    required
                />
                </div>
                <div>
                <label>Clave</label>
                <input
                    type="password"
                    value={clave}
                    onChange={(e) => setClave(e.target.value)}
                    required
                />
                </div>
                <Button type="submit">Registrarme</Button>
            </form>
            <ModalMessage
                isOpen={modalInfo.isOpen}
                onClose={handleCloseModal}
                title={modalInfo.title}
                message={modalInfo.message}
                type={modalInfo.type}
            />
        </div>
    );
};

export default RegistroPage;
